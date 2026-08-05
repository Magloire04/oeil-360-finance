<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Liste des identités supprimées (« tombstone ») pour empêcher la recréation
     * automatique d'un compte via le SSO après une suppression volontaire.
     * Minimisé APDP : on ne stocke qu'un HASH SHA-256 de l'identifiant Auth0
     * (pseudonyme), aucune donnée personnelle (ni email, ni nom).
     */
    public function up(): void
    {
        Schema::create('deleted_identities', function (Blueprint $table): void {
            $table->id();
            $table->string('identifier_hash', 64)->unique();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deleted_identities');
    }
};
