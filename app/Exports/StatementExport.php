<?php

namespace App\Exports;

use App\Exports\Sheets\AccountsSheet;
use App\Exports\Sheets\RecurringSheet;
use App\Exports\Sheets\SummarySheet;
use App\Exports\Sheets\TransactionsSheet;
use App\Exports\Sheets\TransfersSheet;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Classeur Excel du relevé financier : une feuille Résumé (letterhead + synthèse)
 * puis les tables détaillées. Alimenté par App\Services\StatementService.
 */
class StatementExport implements Export, WithMultipleSheets
{
    /**
     * @param  array<string, mixed>  $report
     */
    public function __construct(private array $report) {}

    /**
     * @return array<int, object>
     */
    public function sheets(): array
    {
        return [
            new SummarySheet($this->report),
            new TransactionsSheet($this->report),
            new AccountsSheet($this->report),
            new TransfersSheet($this->report),
            new RecurringSheet($this->report),
        ];
    }
}
