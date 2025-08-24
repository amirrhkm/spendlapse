<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface TransactionParserInterface
{
    /**
     * Parse CSV file and return array of transaction data
     *
     * @param UploadedFile $file
     * @return array
     */
    public function parse(UploadedFile $file): array;
}
