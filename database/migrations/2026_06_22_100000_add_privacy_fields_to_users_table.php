<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->timestamp('consent_given_at')->nullable()->after('remember_token');
            $table->string('consent_version', 20)->nullable()->after('consent_given_at');
            $table->timestamp('last_activity_at')->nullable()->after('consent_version');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['consent_given_at', 'consent_version', 'last_activity_at']);
        });
    }
};
