<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    /**
     * Upload and process CSV file
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $result = $this->transactionService->uploadAndProcess($request->file('file'));

        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'summary' => $result['summary'],
                'transaction_count' => $result['transaction_count']
            ]);
        }

        return response()->json([
            'message' => 'Failed to upload transactions: ' . $result['error']
        ], 500);
    }

    /**
     * Get monthly summary
     */
    public function summary(Request $request): JsonResponse
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $summary = $this->transactionService->getMonthlySummary($year, $month);

        return response()->json($summary);
    }

    /**
     * Get all transactions
     */
    public function index(): JsonResponse
    {
        $transactions = $this->transactionService->getAllTransactions();

        return response()->json($transactions);
    }
}
