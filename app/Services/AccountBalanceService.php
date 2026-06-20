<?php

namespace App\Services;

use App\Models\Account;

class AccountBalanceService
{
    public function getBalance(Account $account): float
    {
        $income = (float) $account->transactions()
            ->where('sense', 'income')
            ->sum('amount');

        $expense = (float) $account->transactions()
            ->where('sense', 'expense')
            ->sum('amount');

        $transfersIn = (float) $account->transfersIn()->sum('amount');
        $transfersOut = (float) $account->transfersOut()->sum('amount');

        return (float) $account->initial_balance + $income - $expense + $transfersIn - $transfersOut;
    }

    public function getTotalBalance(): float
    {
        $accounts = Account::where('is_archived', false)->get();
        $total = 0.0;
        foreach ($accounts as $account) {
            $total += $this->getBalance($account);
        }
        return $total;
    }
}
