<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'type', 'is_archived'];

    protected $attributes = [
        'is_archived' => false,
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function recurringTransactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class);
    }

    // Helper métier : retourne true si au moins une transaction ou une récurrente lui est liée
    public function isUsed(): bool
    {
        return $this->transactions()->exists() || $this->recurringTransactions()->exists();
    }
}
