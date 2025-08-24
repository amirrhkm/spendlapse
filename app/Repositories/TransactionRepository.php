<?php

namespace App\Repositories;

use App\Contracts\TransactionRepositoryInterface;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function createBatch(array $transactions): bool
    {
        try {
            DB::transaction(function () use ($transactions) {
                Transaction::insert($transactions);
            });
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAll(): Collection
    {
        return Transaction::orderBy('transaction_date', 'desc')->get();
    }

    public function getByMonth(int $year, int $month): Collection
    {
        return Transaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    public function getTotalMoneyIn(int $year, int $month): float
    {
        return Transaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->whereNotNull('money_in')
            ->sum('money_in') ?? 0.0;
    }

    public function getTotalMoneyOut(int $year, int $month): float
    {
        return Transaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->whereNotNull('money_out')
            ->sum('money_out') ?? 0.0;
    }

    public function getFinalBalance(int $year, int $month): float
    {
        $lastTransaction = Transaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('transaction_date', 'desc')
            ->first();

        return $lastTransaction ? $lastTransaction->balance : 0.0;
    }

    public function clearAll(): bool
    {
        try {
            Transaction::truncate();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function clearByMonth(int $year, int $month): bool
    {
        try {
            Transaction::whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
