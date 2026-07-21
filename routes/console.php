<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Génération des transactions récurrentes dues — à activer en production via cron
// Schedule::command('transactions:generate-recurring')->daily();

// Purge APDP des utilisateurs inactifs (DRY-RUN par défaut — log sans supprimer)
// Pour activer la vraie suppression, ajouter --execute dans la commande.
Schedule::command('oeil360:purge-inactive')->monthly()->runInBackground();
