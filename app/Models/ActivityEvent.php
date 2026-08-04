<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Événement d'usage pseudonyme (visite/action) — sert aux statistiques admin.
 * Ne stocke aucune donnée personnelle sensible ni secret (conforme APDP / OWASP A09).
 */
class ActivityEvent extends Model
{
    use HasFactory;

    // Seul created_at est conservé (pas de updated_at).
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'feature',
        'method',
        'path',
        'status',
        'duration_ms',
    ];

    protected $casts = [
        'status' => 'integer',
        'duration_ms' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
