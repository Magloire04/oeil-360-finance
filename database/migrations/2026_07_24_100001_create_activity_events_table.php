<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Journal d'événements d'usage — minimisé APDP :
     * aucune IP nominative, aucun user-agent, aucun contenu de requête.
     * Seul un lien pseudonyme vers l'utilisateur (user_id) est conservé.
     */
    public function up(): void
    {
        Schema::create('activity_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('feature')->index();
            $table->string('method', 10);
            $table->string('path');
            $table->unsignedSmallInteger('status');
            $table->unsignedInteger('duration_ms');
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_events');
    }
};
