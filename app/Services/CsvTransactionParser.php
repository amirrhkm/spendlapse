<?php

namespace App\Services;

use App\Contracts\TransactionParserInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

class CsvTransactionParser implements TransactionParserInterface
{
    public function parse(UploadedFile $file): array
    {
        $transactions = [];
        $csvData = array_map('str_getcsv', file($file->getPathname()));
        
        // Skip header row
        $header = array_shift($csvData);
        
        foreach ($csvData as $row) {
            if (count($row) < 5) {
                continue; // Skip invalid rows
            }
            
            $transactions[] = [
                'transaction_date' => $this->parseDate($row[0]),
                'transaction_details' => trim($row[1]),
                'money_in' => $this->parseAmount($row[2]),
                'money_out' => $this->parseAmount($row[3]),
                'balance' => $this->parseAmount($row[4]),
                'currency' => $this->extractCurrency($row[4]),
            ];
        }
        
        return $transactions;
    }

    private function parseDate(string $dateString): string
    {
        $dateString = trim($dateString, '"');
        
        // Handle date format like "21-Aug-2025" (full year)
        if (preg_match('/^\d{1,2}-[A-Za-z]{3}-\d{4}$/', $dateString)) {
            return Carbon::createFromFormat('j-M-Y', $dateString)->format('Y-m-d');
        }
        
        // Handle date format like "21-Aug-25" (short year)
        if (preg_match('/^\d{1,2}-[A-Za-z]{3}-\d{2}$/', $dateString)) {
            return Carbon::createFromFormat('j-M-y', $dateString)->format('Y-m-d');
        }
        
        // Fallback: try to parse with Carbon's flexible parser
        try {
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            // If all else fails, assume current year and log the error
            \Log::warning("Failed to parse date: {$dateString}, using current year");
            return Carbon::now()->format('Y-m-d');
        }
    }

    private function parseAmount(string $amountString): ?float
    {
        $amountString = trim($amountString, '"');
        
        if (empty($amountString)) {
            return null;
        }
        
        // Remove currency prefix (e.g., "MYR ")
        $amountString = preg_replace('/^[A-Z]{3}\s*/', '', $amountString);
        
        // Convert to float
        return (float) str_replace(',', '', $amountString);
    }

    private function extractCurrency(string $balanceString): string
    {
        $balanceString = trim($balanceString, '"');
        
        if (preg_match('/^([A-Z]{3})\s/', $balanceString, $matches)) {
            return $matches[1];
        }
        
        return 'MYR'; // Default currency
    }
}
