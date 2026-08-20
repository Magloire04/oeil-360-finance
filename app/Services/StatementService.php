<?php

namespace App\Services;

use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;

/**
 * Assemble le contenu du relevé financier d'un utilisateur (en-tête, résumé et
 * tables détaillées) en une seule structure, consommée à l'identique par
 * l'export Excel (App\Exports\StatementExport) et le PDF (vue exports.statement).
 * Une seule source de vérité pour les deux formats.
 */
class StatementService
{
    public function __construct(private DashboardService $dashboardService) {}

    /**
     * @return array<string, mixed>
     */
    public function buildStatement(User $user, ?string $startDate, ?string $endDate): array
    {
        $isAll = empty($startDate) && empty($endDate);
        [$effStart, $effEnd] = $this->resolvePeriod($user, $startDate, $endDate, $isAll);

        $summary = $this->dashboardService->getSummary($effStart, $effEnd, $user->id);

        return [
            'generated_at' => now(),
            'period' => [
                'start' => $isAll ? null : $effStart,
                'end' => $isAll ? null : $effEnd,
                'label' => $isAll
                    ? 'Toutes les données'
                    : 'Du '.Carbon::parse($effStart)->format('d/m/Y').' au '.Carbon::parse($effEnd)->format('d/m/Y'),
                'is_all' => $isAll,
            ],
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'summary' => [
                'total_balance' => (float) $summary['balances']['total'],
                'accounts' => array_map(fn (array $a) => [
                    'name' => $a['name'],
                    'type' => $this->accountTypeLabel($a['type']),
                    'balance' => (float) $a['balance'],
                ], $summary['balances']['accounts']),
                'income' => (float) $summary['period']['income'],
                'expense' => (float) $summary['period']['expense'],
                'net' => (float) $summary['period']['net'],
                'transactions_count' => (int) $summary['kpis']['transactions_count'],
                'daily_avg_expense' => (float) $summary['kpis']['daily_avg_expense'],
                'top_expense_category' => $summary['kpis']['top_expense_category'],
                'expense_by_category' => $summary['expense_by_category'],
            ],
            'transactions' => $this->transactions($user, $effStart, $effEnd, $isAll),
            'transfers' => $this->transfers($user, $effStart, $effEnd, $isAll),
            'recurring' => $this->recurring($user),
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function resolvePeriod(User $user, ?string $start, ?string $end, bool $isAll): array
    {
        $today = now()->toDateString();
        $earliest = fn (): string => (string) (Transaction::where('user_id', $user->id)->min('transaction_date')
            ?? $user->created_at?->toDateString()
            ?? $today);

        if ($isAll) {
            return [$earliest(), $today];
        }

        return [$start ?: $earliest(), $end ?: $today];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function transactions(User $user, string $start, string $end, bool $isAll): array
    {
        $query = Transaction::query()->where('user_id', $user->id)->with(['category', 'account'])
            ->orderBy('transaction_date')->orderBy('id');

        if (! $isAll) {
            $query->whereBetween('transaction_date', [$start, $end]);
        }

        return $query->get()->map(fn (Transaction $t) => [
            'date' => $t->transaction_date->format('d/m/Y'),
            'category' => $t->category->name,
            'account' => $t->account->name,
            'sense' => $this->senseLabel($t->sense),
            'note' => $t->note ?? '',
            'amount' => (float) $t->amount,
        ])->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function transfers(User $user, string $start, string $end, bool $isAll): array
    {
        $query = Transfer::query()->where('user_id', $user->id)->with(['fromAccount', 'toAccount'])
            ->orderBy('transfer_date')->orderBy('id');

        if (! $isAll) {
            $query->whereBetween('transfer_date', [$start, $end]);
        }

        return $query->get()->map(fn (Transfer $t) => [
            'date' => $t->transfer_date->format('d/m/Y'),
            'from' => $t->fromAccount->name,
            'to' => $t->toAccount->name,
            'amount' => (float) $t->amount,
            'note' => $t->note ?? '',
        ])->all();
    }

    /**
     * Les transactions récurrentes sont des modèles permanents (non bornés par la
     * période) : on liste toutes celles de l'utilisateur avec leur état.
     *
     * @return array<int, array<string, mixed>>
     */
    private function recurring(User $user): array
    {
        return RecurringTransaction::query()->where('user_id', $user->id)->with(['category', 'account'])
            ->orderByDesc('is_active')->orderBy('next_occurrence_date')
            ->get()->map(fn (RecurringTransaction $r) => [
                'category' => $r->category->name,
                'account' => $r->account->name,
                'sense' => $this->senseLabel($r->sense),
                'frequency' => $this->frequencyLabel($r->frequency),
                'amount' => (float) $r->amount,
                'next' => $r->next_occurrence_date->format('d/m/Y'),
                'active' => $r->is_active ? 'Oui' : 'Non',
            ])->all();
    }

    private function senseLabel(string $sense): string
    {
        return $sense === 'income' ? 'Entrée' : 'Dépense';
    }

    private function accountTypeLabel(string $type): string
    {
        return match ($type) {
            'cash' => 'Espèces',
            'mobile_money' => 'Mobile Money',
            'bank' => 'Banque',
            default => $type,
        };
    }

    private function frequencyLabel(string $frequency): string
    {
        return match ($frequency) {
            'daily' => 'Quotidienne',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuelle',
            'yearly' => 'Annuelle',
            default => $frequency,
        };
    }
}
