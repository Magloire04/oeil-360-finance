<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(private AccountBalanceService $balanceService) {}

    public function getSummary(string $startDate, string $endDate, int $userId): array
    {
        return [
            'balances' => $this->getBalances($userId),
            'period' => $this->getPeriodSummary($startDate, $endDate, $userId),
            'kpis' => $this->getKpis($startDate, $endDate, $userId),
            'expense_by_category' => $this->getExpenseByCategory($startDate, $endDate, $userId),
            'balance_evolution' => $this->getBalanceEvolution($startDate, $endDate, $userId),
            'recent_transactions' => $this->getRecentTransactions($userId),
        ];
    }

    public function getMonthlyComparison(int $userId): array
    {
        $start = now()->subMonths(11)->startOfMonth()->toDateString();

        $rows = Transaction::where('user_id', $userId)
            ->where('transaction_date', '>=', $start)
            ->get(['transaction_date', 'sense', 'amount']);

        $byMonth = [];
        foreach ($rows as $row) {
            $month = substr((string) $row->transaction_date, 0, 7);
            $byMonth[$month] ??= ['income' => 0.0, 'expense' => 0.0];
            $byMonth[$month][$row->sense] += (float) $row->amount;
        }

        $result = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $result[] = [
                'month' => $month,
                'income' => $byMonth[$month]['income'] ?? 0.0,
                'expense' => $byMonth[$month]['expense'] ?? 0.0,
            ];
        }

        return $result;
    }

    private function getBalances(int $userId): array
    {
        $accounts = Account::where('user_id', $userId)
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        $accountBalances = $accounts->map(fn (Account $a) => [
            'id' => $a->id,
            'name' => $a->name,
            'type' => $a->type,
            'balance' => $this->balanceService->getBalance($a),
        ])->values()->toArray();

        return [
            'total' => $this->balanceService->getTotalBalance($userId),
            'accounts' => $accountBalances,
        ];
    }

    private function getKpis(string $startDate, string $endDate, int $userId): array
    {
        $transactionsCount = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->count();

        $totalExpense = (float) Transaction::where('user_id', $userId)
            ->where('sense', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $days = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);
        $dailyAvgExpense = round($totalExpense / $days, 2);

        $topRow = Transaction::with('category')
            ->where('user_id', $userId)
            ->where('sense', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->first();

        return [
            'transactions_count' => $transactionsCount,
            'daily_avg_expense' => $dailyAvgExpense,
            'top_expense_category' => $topRow?->category?->name,
        ];
    }

    private function getPeriodSummary(string $startDate, string $endDate, int $userId): array
    {
        $income = (float) Transaction::where('user_id', $userId)
            ->where('sense', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $expense = (float) Transaction::where('user_id', $userId)
            ->where('sense', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
        ];
    }

    private function getExpenseByCategory(string $startDate, string $endDate, int $userId): array
    {
        return Transaction::with('category')
            ->where('user_id', $userId)
            ->where('sense', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('category_id, SUM(amount) as amount')
            ->groupBy('category_id')
            ->orderByDesc('amount')
            ->get()
            ->map(fn (Transaction $t) => [
                'category_name' => $t->category->name,
                'amount' => (float) $t->amount,
            ])
            ->values()
            ->toArray();
    }

    private function getBalanceEvolution(string $startDate, string $endDate, int $userId): array
    {
        $totalInitial = (float) Account::where('user_id', $userId)
            ->where('is_archived', false)
            ->sum('initial_balance');

        $beforeIncome = (float) Transaction::where('user_id', $userId)
            ->where('sense', 'income')
            ->where('transaction_date', '<', $startDate)
            ->sum('amount');

        $beforeExpense = (float) Transaction::where('user_id', $userId)
            ->where('sense', 'expense')
            ->where('transaction_date', '<', $startDate)
            ->sum('amount');

        $openingBalance = $totalInitial + $beforeIncome - $beforeExpense;

        $byDate = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('transaction_date, sense, SUM(amount) as total')
            ->groupBy('transaction_date', 'sense')
            ->orderBy('transaction_date')
            ->get()
            ->groupBy('transaction_date');

        $evolution = [['date' => $startDate, 'cumulative_balance' => $openingBalance]];
        $running = $openingBalance;

        foreach ($byDate as $date => $rows) {
            foreach ($rows as $row) {
                if ($row->sense === 'income') {
                    $running += (float) $row->getAttribute('total');
                } else {
                    $running -= (float) $row->getAttribute('total');
                }
            }
            if ($date !== $startDate) {
                $evolution[] = ['date' => $date, 'cumulative_balance' => $running];
            } else {
                $evolution[0]['cumulative_balance'] = $running;
            }
        }

        return $evolution;
    }

    private function getRecentTransactions(int $userId): array
    {
        return Transaction::with(['category', 'account'])
            ->where('user_id', $userId)
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->toArray();
    }
}
