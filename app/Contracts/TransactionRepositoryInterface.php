<?php

namespace App\Contracts;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    /**
     * Create multiple transactions
     *
     * @param array $transactions
     * @return bool
     */
    public function createBatch(array $transactions): bool;

    /**
     * Get all transactions
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Get transactions for a specific month
     *
     * @param int $year
     * @param int $month
     * @return Collection
     */
    public function getByMonth(int $year, int $month): Collection;

    /**
     * Calculate total money in for a month
     *
     * @param int $year
     * @param int $month
     * @return float
     */
    public function getTotalMoneyIn(int $year, int $month): float;

    /**
     * Calculate total money out for a month
     *
     * @param int $year
     * @param int $month
     * @return float
     */
    public function getTotalMoneyOut(int $year, int $month): float;

    /**
     * Get final balance for a month
     *
     * @param int $year
     * @param int $month
     * @return float
     */
    public function getFinalBalance(int $year, int $month): float;

    /**
     * Clear all transactions
     *
     * @return bool
     */
    public function clearAll(): bool;
    
    /**
     * Clear transactions for a specific month
     *
     * @param int $year
     * @param int $month
     * @return bool
     */
    public function clearByMonth(int $year, int $month): bool;
}
