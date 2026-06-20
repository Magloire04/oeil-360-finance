<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;

class DashboardService
{
    public function __construct(private AccountBalanceService $balanceService) {}

    public function getSummary(string $startDate, string $endDate): array
    {
        return [
            'balances'            => $this->getBalances(),
            'period'              => $this->getPeriodSummary($startDate, $endDate),
            'expense_by_category' => $this->getExpenseByCategory($startDate, $endDate),
            'balance_evolution'   => $this->getBalanceEvolution($startDate, $endDate),
            'recent_transactions' => $this->getRecentTransactions(),
        ];
    }

    private function getBalances(): array
    {
        $accounts = Account::where('is_archived', false)->orderBy('name')->get();
        $accountBalances = $accounts->map(fn($a) => [
            'id'      => $a->id,
            'name'    => $a->name,
            'type'    => $a->type,
            'balance' => $this->balanceService->getBalance($a),
        ])->values()->toArray();

        return [
            'total'    => $this->balanceService->getTotalBalance(),
            'accounts' => $accountBalances,
        ];
    }

    private function getPeriodSummary(string $startDate, string $endDate): array
    {
        $income = (float) Transaction::where('sense', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $expense = (float) Transaction::where('sense', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        return [
            'income'  => $income,
            'expense' => $expense,
            'net'     => $income - $expense,
        ];
    }

    private function getExpenseByCategory(string $startDate, string $endDate): array
    {
        return Transaction::with('category')
            ->where('sense', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category_id, SUM(amount) as amount')
            ->groupBy('category_id')
            ->orderByDesc('amount')
            ->get()
            ->map(fn($t) => [
                'category_name' => $t->category->name,
                'amount'        => (float) $t->amount,
            ])
            ->values()
            ->toArray();
    }

    private function getBalanceEvolution(string $startDate, string $endDate): array
    {
        // Solde d'ouverture = tout ce qui précède start_date
        $totalInitial = (float) Account::where('is_archived', false)->sum('initial_balance');
        $beforeIncome = (float) Transaction::where('sense', 'income')
            ->where('transaction_date', '<', $startDate)
            ->sum('amount');
        $beforeExpense = (float) Transaction::where('sense', 'expense')
            ->where('transaction_date', '<', $startDate)
            ->sum('amount');
        $openingBalance = $totalInitial + $beforeIncome - $beforeExpense;

        // Transactions de la période groupées par date
        $byDate = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw("transaction_date, sense, SUM(amount) as total")
            ->groupBy('transaction_date', 'sense')
            ->orderBy('transaction_date')
            ->get()
            ->groupBy('transaction_date');

        $evolution = [['date' => $startDate, 'cumulative_balance' => $openingBalance]];
        $running = $openingBalance;

        foreach ($byDate as $date => $rows) {
            foreach ($rows as $row) {
                if ($row->sense === 'income') {
                    $running += (float) $row->total;
                } else {
                    $running -= (float) $row->total;
                }
            }
            // Skip if date === start_date (already recorded as opening)
            if ($date !== $startDate) {
                $evolution[] = ['date' => $date, 'cumulative_balance' => $running];
            } else {
                // Update opening entry to include start_date transactions
                $evolution[0]['cumulative_balance'] = $running;
            }
        }

        return $evolution;
    }

    private function getRecentTransactions(): array
    {
        return Transaction::with(['category', 'account'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->toArray();
    }
}
