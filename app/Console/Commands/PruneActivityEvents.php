<?php

namespace App\Console\Commands;

use App\Models\ActivityEvent;
use Illuminate\Console\Command;

class PruneActivityEvents extends Command
{
    protected $signature = 'oeil360:prune-activity-events
                            {--days=90 : Durée de conservation des événements en jours}';

    protected $description = "Purge les événements d'usage plus anciens que la durée de conservation (minimisation APDP).";

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days);

        $deleted = ActivityEvent::where('created_at', '<', $cutoff)->delete();

        $this->info("{$deleted} événement(s) d'usage supprimé(s) (antérieurs à {$cutoff->toDateString()}, rétention {$days} j).");

        return self::SUCCESS;
    }
}
