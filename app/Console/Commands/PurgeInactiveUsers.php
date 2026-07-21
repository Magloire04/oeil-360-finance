<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeInactiveUsers extends Command
{
    protected $signature = 'oeil360:purge-inactive
                            {--execute : Effectue la suppression réelle (sans ce flag : DRY-RUN uniquement)}';

    protected $description = 'Supprime/anonymise les utilisateurs inactifs au-delà de la durée de conservation définie (DATA_RETENTION_YEARS).';

    public function handle(): int
    {
        $years = (int) config('app.data_retention_years', 5);
        $cutoff = now()->subYears($years);
        $execute = $this->option('execute');

        $this->info($execute
            ? "Mode RÉEL — suppression des utilisateurs inactifs depuis plus de {$years} an(s)."
            : 'Mode DRY-RUN — aucune suppression. Ajoutez --execute pour activer.');

        $users = User::where(function ($q) use ($cutoff): void {
            // Inactif depuis X ans (last_activity_at renseigné)
            $q->whereNotNull('last_activity_at')
                ->where('last_activity_at', '<', $cutoff);
        })->orWhere(function ($q) use ($cutoff): void {
            // Jamais actif et compte créé depuis X ans
            $q->whereNull('last_activity_at')
                ->where('created_at', '<', $cutoff);
        })->get();

        $total = User::count();
        $count = $users->count();

        if ($count === 0) {
            $this->info("Aucun utilisateur à purger. {$total} utilisateur(s) actif(s) conservé(s).");

            return self::SUCCESS;
        }

        $tableRows = [];
        foreach ($users as $u) {
            $tableRows[] = [
                $u->id,
                $this->maskEmail((string) $u->email),
                $u->last_activity_at?->format('Y-m-d') ?? 'jamais',
                $u->created_at?->format('Y-m-d') ?? '?',
            ];
        }
        $this->table(['ID', 'Email (masqué)', 'Dernière activité', 'Créé le'], $tableRows);

        if (! $execute) {
            $this->warn("{$count} utilisateur(s) SERAIENT supprimés (dry-run). Lancez avec --execute pour confirmer.");

            return self::SUCCESS;
        }

        DB::transaction(function () use ($users): void {
            foreach ($users as $user) {
                // La suppression cascade sur toutes les données financières (FK cascadeOnDelete)
                $user->delete();
            }
        });

        $this->info("{$count} utilisateur(s) supprimé(s). {$total} - {$count} = ".($total - $count).' utilisateur(s) conservé(s).');

        return self::SUCCESS;
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $masked = substr($local, 0, 2).str_repeat('*', max(0, strlen($local) - 2));

        return $masked.'@'.$domain;
    }
}
