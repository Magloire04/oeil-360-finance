<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithFreezePane;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Base d'une feuille tabulaire du relevé : en-tête stylé (bandeau navy, texte
 * blanc gras), volet figé sur la ligne d'en-tête, colonnes de montant formatées
 * en XOF. Les feuilles concrètes fournissent titre, en-têtes, lignes et colonnes
 * de montant.
 */
abstract class TabularSheet implements FromArray, ShouldAutoSize, WithColumnFormatting, WithFreezePane, WithHeadings, WithStyles, WithTitle
{
    private const XOF_FORMAT = '#,##0" XOF"';

    private const NAVY = '1A2E4A';

    /**
     * @param  array<string, mixed>  $report
     */
    public function __construct(protected array $report) {}

    abstract public function title(): string;

    /**
     * @return array<int, string>
     */
    abstract public function headings(): array;

    /**
     * @return array<int, array<int, mixed>>
     */
    abstract public function array(): array;

    /**
     * Lettres des colonnes contenant un montant (formatage XOF).
     *
     * @return array<int, string>
     */
    abstract protected function amountColumns(): array;

    /**
     * @return array<string, string>
     */
    public function columnFormats(): array
    {
        $formats = [];
        foreach ($this->amountColumns() as $column) {
            $formats[$column] = self::XOF_FORMAT;
        }

        return $formats;
    }

    public function freezePane(): string
    {
        return 'A2';
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        $lastColumn = Coordinate::stringFromColumnIndex(count($this->headings()));

        return [
            'A1:'.$lastColumn.'1' => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }
}
