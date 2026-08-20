<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Feuille « Résumé » : letterhead de marque (logo + contacts bytechnum), période,
 * titulaire, puis les indicateurs de synthèse et les soldes par compte.
 */
class SummarySheet implements FromArray, WithColumnWidths, WithDrawings, WithStyles, WithTitle
{
    private const NAVY = '1A2E4A';

    private const TEAL = '28C98A';

    private const MUTED = '64748B';

    /**
     * @param  array<string, mixed>  $report
     */
    public function __construct(private array $report) {}

    public function title(): string
    {
        return 'Résumé';
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        $s = $this->report['summary'];

        $rows = [
            ['Oeil 360° Finance', ''],
            ['Relevé financier', ''],
            ['Édité par bytechnum', ''],
            ['oeil360finance@bytechnum.com', ''],
            ['oeil360finance.bytechnum.com  ·  Porto-Novo, Bénin', ''],
            ['', ''],
            ['Période', $this->report['period']['label']],
            ['Titulaire', $this->report['user']['name']],
            ['Email', $this->report['user']['email']],
            ['Généré le', $this->report['generated_at']->format('d/m/Y H:i')],
            ['', ''],
            ['Résumé', ''],
            ['Solde total', formatXof($s['total_balance'])],
            ['Total entrées', formatXof($s['income'])],
            ['Total sorties', formatXof($s['expense'])],
            ['Solde net', formatXof($s['net'])],
            ['Nombre de transactions', (string) $s['transactions_count']],
            ['Dépense moyenne / jour', formatXof($s['daily_avg_expense'])],
            ['Catégorie la plus dépensière', $s['top_expense_category'] ?? '-'],
            ['', ''],
            ['Soldes par compte', ''],
            ['Compte', 'Solde'],
        ];

        foreach ($s['accounts'] as $account) {
            $rows[] = [$account['name'], formatXof($account['balance'])];
        }

        return $rows;
    }

    /**
     * @return array<string, float|int>
     */
    public function columnWidths(): array
    {
        return ['A' => 38, 'B' => 44];
    }

    public function drawings(): Drawing
    {
        $drawing = new Drawing;
        $drawing->setName('Oeil 360 Finance');
        $drawing->setPath(public_path('images/oeil360-icon-white-bg.png'));
        $drawing->setHeight(64);
        $drawing->setCoordinates('C1');

        return $drawing;
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        $sectionHeader = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY]],
        ];

        return [
            1 => ['font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => self::NAVY]]],
            2 => ['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => self::TEAL]]],
            3 => ['font' => ['size' => 10, 'color' => ['rgb' => self::MUTED]]],
            4 => ['font' => ['size' => 10, 'color' => ['rgb' => self::MUTED]]],
            5 => ['font' => ['size' => 10, 'color' => ['rgb' => self::MUTED]]],
            'A7:A10' => ['font' => ['bold' => true, 'color' => ['rgb' => self::NAVY]]],
            'A12:B12' => $sectionHeader,
            'A13:A19' => ['font' => ['bold' => true, 'color' => ['rgb' => self::NAVY]]],
            'A21:B21' => $sectionHeader,
            22 => ['font' => ['bold' => true, 'color' => ['rgb' => self::NAVY]]],
        ];
    }
}
