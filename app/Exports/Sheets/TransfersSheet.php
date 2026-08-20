<?php

namespace App\Exports\Sheets;

class TransfersSheet extends TabularSheet
{
    public function title(): string
    {
        return 'Transferts';
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Date', 'De', 'Vers', 'Montant', 'Note'];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return array_map(fn (array $t) => [
            $t['date'], $t['from'], $t['to'], $t['amount'], $t['note'],
        ], $this->report['transfers']);
    }

    /**
     * @return array<int, string>
     */
    protected function amountColumns(): array
    {
        return ['D'];
    }
}
