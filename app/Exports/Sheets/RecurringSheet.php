<?php

namespace App\Exports\Sheets;

class RecurringSheet extends TabularSheet
{
    public function title(): string
    {
        return 'Récurrentes';
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Catégorie', 'Compte', 'Sens', 'Fréquence', 'Montant', 'Prochaine occurrence', 'Active'];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return array_map(fn (array $r) => [
            $r['category'], $r['account'], $r['sense'], $r['frequency'], $r['amount'], $r['next'], $r['active'],
        ], $this->report['recurring']);
    }

    /**
     * @return array<int, string>
     */
    protected function amountColumns(): array
    {
        return ['E'];
    }
}
