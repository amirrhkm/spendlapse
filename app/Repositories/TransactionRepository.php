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
        $userId = auth()->id();
        if (!$userId) {
            return collect();
        }
        
        return Transaction::where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    public function getByMonth(int $year, int $month): Collection
    {
        $userId = auth()->id();
        if (!$userId) {
            return collect();
        }
        
        return Transaction::where('user_id', $userId)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    public function getTotalMoneyIn(int $year, int $month): float
    {
        $userId = auth()->id();
        if (!$userId) {
            return 0.0;
        }
        
        return Transaction::where('user_id', $userId)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->whereNotNull('money_in')
            ->sum('money_in') ?? 0.0;
    }

    public function getTotalMoneyOut(int $year, int $month): float
    {
        $userId = auth()->id();
        if (!$userId) {
            return 0.0;
        }
        
        return Transaction::where('user_id', $userId)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->whereNotNull('money_out')
            ->sum('money_out') ?? 0.0;
    }

    public function getFinalBalance(int $year, int $month): float
    {
        $userId = auth()->id();
        if (!$userId) {
            return 0.0;
        }
        
        $lastTransaction = Transaction::where('user_id', $userId)
            ->whereYear('transaction_date', $year)
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
            $userId = auth()->id();
            if (!$userId) {
                return false;
            }
            
            Transaction::where('user_id', $userId)
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
