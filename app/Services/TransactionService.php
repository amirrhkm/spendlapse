<?php

namespace App\Services;

use App\Contracts\TransactionParserInterface;
use App\Contracts\TransactionRepositoryInterface;
use Illuminate\Http\UploadedFile;

class TransactionService
{
    public function __construct(
        private TransactionParserInterface $parser,
        private TransactionRepositoryInterface $repository
    ) {}

    public function uploadAndProcess(UploadedFile $file): array
    {
        try {
            $transactions = $this->parser->parse($file);
            
            if (empty($transactions)) {
                throw new \Exception('No valid transactions found in CSV');
            }
            
            $firstTransaction = $transactions[0];
            $transactionDate = \Carbon\Carbon::parse($firstTransaction['transaction_date']);
            $year = $transactionDate->year;
            $month = $transactionDate->month;
            
            $this->repository->clearByMonth($year, $month);
            
            $now = now();
            $userId = auth()->id();
            if (!$userId) {
                throw new \Exception('User not authenticated');
            }
            
            $transactions = array_map(function ($transaction) use ($now, $userId) {
                $transaction['created_at'] = $now;
                $transaction['updated_at'] = $now;
                $transaction['user_id'] = $userId;
                return $transaction;
            }, $transactions);
            
            $success = $this->repository->createBatch($transactions);
            
            if (!$success) {
                throw new \Exception('Failed to store transactions');
            }
            
            return [
                'success' => true,
                'summary' => $this->getMonthlySummary($year, $month),
                'transaction_count' => count($transactions),
                'message' => $this->repository->getByMonth($year, $month)->count() > 0 
                    ? "Updated transactions for {$this->getMonthName($month)} {$year}"
                    : "Added new transactions for {$this->getMonthName($month)} {$year}"
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function getMonthName(int $month): string
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        return $months[$month] ?? 'Unknown';
    }

    public function getMonthlySummary(int $year, int $month): array
    {
        $totalIn = $this->repository->getTotalMoneyIn($year, $month);
        $totalOut = $this->repository->getTotalMoneyOut($year, $month);
        $finalBalance = $this->repository->getFinalBalance($year, $month);
        
        return [
            'total_money_in' => $totalIn,
            'total_money_out' => $totalOut,
            'final_balance' => $finalBalance,
            'net_change' => $totalIn - $totalOut,
            'year' => $year,
            'month' => $month,
        ];
    }

    public function getCurrentMonthlySummary(): array
    {
        $now = now();
        return $this->getMonthlySummary($now->year, $now->month);
    }

    public function getAllTransactions()
    {
        return $this->repository->getAll();
    }
}
