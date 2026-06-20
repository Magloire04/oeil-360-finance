<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'initial_balance', 'is_archived'];

    protected $attributes = [
        'is_archived'     => false,
        'initial_balance' => 0.00,
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'is_archived' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transfersOut(): HasMany
    {
        return $this->hasMany(Transfer::class, 'from_account_id');
    }

    public function transfersIn(): HasMany
    {
        return $this->hasMany(Transfer::class, 'to_account_id');
    }

    public function recurringTransactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class);
    }

    // Helper métier : true si l'account a des transactions, transferts ou récurrentes
    public function isUsed(): bool
    {
        return $this->transactions()->exists()
            || $this->transfersOut()->exists()
            || $this->transfersIn()->exists()
            || $this->recurringTransactions()->exists();
    }
}
