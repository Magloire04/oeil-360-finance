<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Identité (Auth0) dont le compte a été supprimé volontairement.
 * Sert à bloquer la recréation automatique via le SSO. Ne contient qu'un hash
 * pseudonyme de l'identifiant Auth0 — aucune donnée personnelle (APDP).
 */
class DeletedIdentity extends Model
{
    public $timestamps = false;

    protected $fillable = ['identifier_hash', 'deleted_at'];

    protected $casts = ['deleted_at' => 'datetime'];

    public static function hashFor(string $auth0Id): string
    {
        return hash('sha256', $auth0Id);
    }

    /** Enregistre une identité comme supprimée (idempotent). */
    public static function block(string $auth0Id): void
    {
        static::firstOrCreate(
            ['identifier_hash' => static::hashFor($auth0Id)],
            ['deleted_at' => now()],
        );
    }

    public static function isBlocked(?string $auth0Id): bool
    {
        return $auth0Id !== null
            && static::where('identifier_hash', static::hashFor($auth0Id))->exists();
    }
}
