<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'sense',
        'frequency',
        'start_date',
        'next_occurrence_date',
        'category_id',
        'account_id',
        'note',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date:Y-m-d',
        'is_active' => 'boolean',
    ];

    /**
     * Retourne next_occurrence_date en format Y-m-d (string) pour la lisibilité
     * et pour la compatibilité avec les assertions de tests PHPUnit.
     */
    protected function nextOccurrenceDate(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => substr($value, 0, 10),
            set: fn (mixed $value) => is_string($value) ? $value : date('Y-m-d', strtotime((string) $value)),
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
