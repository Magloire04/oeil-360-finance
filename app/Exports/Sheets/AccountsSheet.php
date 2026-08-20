<?php

namespace App\Exports\Sheets;

class AccountsSheet extends TabularSheet
{
    public function title(): string
    {
        return 'Comptes';
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Compte', 'Type', 'Solde'];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return array_map(fn (array $a) => [
            $a['name'], $a['type'], $a['balance'],
        ], $this->report['summary']['accounts']);
    }

    /**
     * @return array<int, string>
     */
    protected function amountColumns(): array
    {
        return ['C'];
    }
}
