<?php

namespace App\Exports\Sheets;

class TransactionsSheet extends TabularSheet
{
    public function title(): string
    {
        return 'Transactions';
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Date', 'Catégorie', 'Compte', 'Sens', 'Note', 'Montant'];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return array_map(fn (array $t) => [
            $t['date'], $t['category'], $t['account'], $t['sense'], $t['note'], $t['amount'],
        ], $this->report['transactions']);
    }

    /**
     * @return array<int, string>
     */
    protected function amountColumns(): array
    {
        return ['F'];
    }
}
