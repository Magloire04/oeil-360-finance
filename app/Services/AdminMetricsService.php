<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ActivityEvent;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Agrégations d'observabilité pour le tableau de bord admin.
 * Toutes les métriques sont agrégées / pseudonymes : jamais les données financières
 * individuelles d'un utilisateur (conforme minimisation APDP).
 */
class AdminMetricsService
{
    /** @return array{start: Carbon, end: Carbon} */
    private function period(string $startDate, string $endDate): array
    {
        return [
            'start' => Carbon::parse($startDate)->startOfDay(),
            'end' => Carbon::parse($endDate)->endOfDay(),
        ];
    }

    public function getOverview(string $startDate, string $endDate): array
    {
        ['start' => $start, 'end' => $end] = $this->period($startDate, $endDate);

        $totalUsers = User::count();
        $consented = User::whereNotNull('consent_given_at')->count();

        $operations = Transaction::whereBetween('created_at', [$start, $end])->count()
            + Transfer::whereBetween('created_at', [$start, $end])->count();

        return [
            'total_users' => $totalUsers,
            'new_users' => User::whereBetween('created_at', [$start, $end])->count(),
            'active_users' => [
                'day' => User::where('last_activity_at', '>=', now()->startOfDay())->count(),
                'week' => User::where('last_activity_at', '>=', now()->subDays(7))->count(),
                'month' => User::where('last_activity_at', '>=', now()->subDays(30))->count(),
            ],
            'operations' => $operations,
            'consent' => [
                'count' => $consented,
                'rate' => $totalUsers > 0 ? round($consented / $totalUsers, 4) : 0.0,
            ],
        ];
    }

    /** Inscriptions par mois sur les 12 derniers mois. */
    public function getUserGrowth(): array
    {
        $start = now()->subMonths(11)->startOfMonth();

        $rows = User::where('created_at', '>=', $start)->get(['created_at']);

        $byMonth = [];
        foreach ($rows as $row) {
            $month = $row->created_at->format('Y-m');
            $byMonth[$month] = ($byMonth[$month] ?? 0) + 1;
        }

        $result = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $result[] = ['month' => $month, 'count' => $byMonth[$month] ?? 0];
        }

        return $result;
    }

    /** Utilisateurs actifs distincts par jour sur la période (basé sur les événements). */
    public function getActiveUsers(string $startDate, string $endDate): array
    {
        ['start' => $start, 'end' => $end] = $this->period($startDate, $endDate);

        $rows = ActivityEvent::whereBetween('created_at', [$start, $end])
            ->whereNotNull('user_id')
            ->get(['created_at', 'user_id']);

        $byDate = [];
        foreach ($rows as $row) {
            $date = $row->created_at->toDateString();
            $byDate[$date][$row->user_id] = true;
        }

        return array_map(
            fn (string $date) => [
                'date' => $date,
                'active_users' => isset($byDate[$date]) ? count($byDate[$date]) : 0,
            ],
            $this->datesBetween($start, $end),
        );
    }

    /** Nombre d'opérations créées par type sur la période. */
    public function getOperationsBreakdown(string $startDate, string $endDate): array
    {
        ['start' => $start, 'end' => $end] = $this->period($startDate, $endDate);
        $range = [$start, $end];

        return [
            ['type' => 'transactions', 'count' => Transaction::whereBetween('created_at', $range)->count()],
            ['type' => 'transfers', 'count' => Transfer::whereBetween('created_at', $range)->count()],
            ['type' => 'recurring', 'count' => RecurringTransaction::whereBetween('created_at', $range)->count()],
            ['type' => 'accounts', 'count' => Account::whereBetween('created_at', $range)->count()],
            ['type' => 'categories', 'count' => Category::whereBetween('created_at', $range)->count()],
        ];
    }

    /** Fonctionnalités les plus utilisées sur la période. */
    public function getTopFeatures(string $startDate, string $endDate, int $limit = 10): array
    {
        ['start' => $start, 'end' => $end] = $this->period($startDate, $endDate);

        return ActivityEvent::whereBetween('created_at', [$start, $end])
            ->select('feature', DB::raw('COUNT(*) as total'))
            ->groupBy('feature')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn (ActivityEvent $e) => [
                'feature' => $e->feature,
                'count' => (int) $e->getAttribute('total'),
            ])
            ->values()
            ->toArray();
    }

    /** Volume de visites (événements) par jour sur la période. */
    public function getTraffic(string $startDate, string $endDate): array
    {
        ['start' => $start, 'end' => $end] = $this->period($startDate, $endDate);

        $rows = ActivityEvent::whereBetween('created_at', [$start, $end])->get(['created_at']);

        $byDate = [];
        foreach ($rows as $row) {
            $date = $row->created_at->toDateString();
            $byDate[$date] = ($byDate[$date] ?? 0) + 1;
        }

        return array_map(
            fn (string $date) => ['date' => $date, 'visits' => $byDate[$date] ?? 0],
            $this->datesBetween($start, $end),
        );
    }

    /** Performances et taux d'erreur sur la période. */
    public function getPerformance(string $startDate, string $endDate): array
    {
        ['start' => $start, 'end' => $end] = $this->period($startDate, $endDate);
        $range = [$start, $end];

        $total = ActivityEvent::whereBetween('created_at', $range)->count();
        $errors = ActivityEvent::whereBetween('created_at', $range)->where('status', '>=', 400)->count();

        $perFeature = ActivityEvent::whereBetween('created_at', $range)
            ->select(
                'feature',
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(duration_ms) as avg_ms'),
                DB::raw('MAX(duration_ms) as max_ms'),
            )
            ->groupBy('feature')
            ->orderByDesc('total')
            ->get()
            ->map(fn (ActivityEvent $e) => [
                'feature' => $e->feature,
                'count' => (int) $e->getAttribute('total'),
                'avg_ms' => round((float) $e->getAttribute('avg_ms'), 1),
                'max_ms' => (int) $e->getAttribute('max_ms'),
            ])
            ->values()
            ->toArray();

        $errorsByStatus = ActivityEvent::whereBetween('created_at', $range)
            ->where('status', '>=', 400)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn (ActivityEvent $e) => [
                'status' => (int) $e->status,
                'count' => (int) $e->getAttribute('total'),
            ])
            ->values()
            ->toArray();

        return [
            'total_requests' => $total,
            'error_rate' => $total > 0 ? round($errors / $total, 4) : 0.0,
            'p95_ms' => $this->percentile(
                ActivityEvent::whereBetween('created_at', $range)->pluck('duration_ms')->all(),
                95,
            ),
            'per_feature' => $perFeature,
            'errors_by_status' => $errorsByStatus,
        ];
    }

    /** Calcul du percentile en PHP (portable SQLite/MySQL). */
    private function percentile(array $values, int $percentile): int
    {
        if ($values === []) {
            return 0;
        }

        sort($values);
        $index = (int) ceil($percentile / 100 * count($values)) - 1;
        $index = max(0, min($index, count($values) - 1));

        return (int) $values[$index];
    }

    /** @return list<string> Liste des jours (Y-m-d) inclus dans l'intervalle. */
    private function datesBetween(Carbon $start, Carbon $end): array
    {
        $dates = [];
        $cursor = $start->copy()->startOfDay();
        $last = $end->copy()->startOfDay();

        while ($cursor->lte($last)) {
            $dates[] = $cursor->toDateString();
            $cursor->addDay();
        }

        return $dates;
    }
}
