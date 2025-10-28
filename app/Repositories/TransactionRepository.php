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
        
        // Fetch all transactions for the month
        $transactions = Transaction::where('user_id', $userId)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('transaction_date', 'asc')
            ->get(['transaction_date', 'money_in', 'money_out', 'balance']);

        if ($transactions->isEmpty()) {
            return 0.0;
        }

        // Group by date: collect set of observed balances and daily net change
        $byDate = [];
        foreach ($transactions as $t) {
            $dateKey = $t->transaction_date->format('Y-m-d');
            if (!isset($byDate[$dateKey])) {
                $byDate[$dateKey] = [
                    'balances' => [],
                    'net' => 0.0,
                ];
            }
            $byDate[$dateKey]['balances'][(string) number_format((float) $t->balance, 2, '.', '')] = true;
            $byDate[$dateKey]['net'] += (float) ($t->money_in ?? 0) - (float) ($t->money_out ?? 0);
        }

        $dates = array_keys($byDate);
        sort($dates);

        // Initialize candidate end-of-day balances with any observed balance on the first date
        $candidates = array_keys($byDate[$dates[0]]['balances']);

        // Propagate through each subsequent date via daily net, matching exact observed balances
        for ($i = 1; $i < count($dates); $i++) {
            $date = $dates[$i];
            $net = $byDate[$date]['net'];
            $observedSet = $byDate[$date]['balances'];

            $nextCandidates = [];
            foreach ($candidates as $candStr) {
                $cand = (float) $candStr;
                $expected = number_format($cand + $net, 2, '.', '');
                if (isset($observedSet[$expected])) {
                    $nextCandidates[$expected] = true;
                }
            }

            if (empty($nextCandidates)) {
                // No exact chain match on this day; break and fallback later
                $candidates = [];
                break;
            }

            $candidates = array_keys($nextCandidates);
        }

        if (!empty($candidates)) {
            // Any remaining candidate is a valid month-end balance; pick the first
            return (float) $candidates[0];
        }

        // Fallbacks: try using the last calendar date's max balance, else overall max
        $lastDate = end($dates);
        $lastDayBalances = array_keys($byDate[$lastDate]['balances']);
        if (!empty($lastDayBalances)) {
            rsort($lastDayBalances, SORT_NATURAL);
            return (float) $lastDayBalances[0];
        }

        $maxBalance = $transactions->max('balance');
        return $maxBalance ? (float) $maxBalance : 0.0;
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
