<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'transaction_details',
        'money_in',
        'money_out',
        'balance',
        'currency',
        'user_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'money_in' => 'decimal:2',
        'money_out' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function isIncoming(): bool
    {
        return !is_null($this->money_in) && $this->money_in > 0;
    }

    public function isOutgoing(): bool
    {
        return !is_null($this->money_out) && $this->money_out > 0;
    }

    public function getAmountAttribute(): float
    {
        return $this->isIncoming() ? $this->money_in : ($this->money_out ?? 0);
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
