<?php

namespace App\Exports;

use App\Exports\Concerns\SanitizesForExcel;
use App\Models\Candidate;
use App\Models\Position;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RankingExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    use SanitizesForExcel;

    public function __construct(private int $positionId) {}

    public function title(): string
    {
        return 'Ranking';
    }

    public function headings(): array
    {
        return [
            'Posición',
            'Candidato',
            'Documento',
            'Promedio (%)',
            'Pruebas aprobadas',
            'Total pruebas',
            'Estado general',
        ];
    }

    public function collection()
    {
        $candidates = Candidate::where('position_id', $this->positionId)
            ->with([
                'assignments' => fn ($q) => $q->where('status', 'completed')->with('result', 'test'),
            ])
            ->get()
            ->map(function (Candidate $candidate) {
                $avgPct      = round($candidate->assignments->avg(fn ($a) => $a->result?->percentage ?? 0), 1);
                $passedCount = $candidate->assignments->filter(fn ($a) => $a->result?->passed)->count();
                $total       = $candidate->assignments->count();

                return [
                    'candidate'   => $candidate,
                    'avg_pct'     => $avgPct,
                    'passed'      => $passedCount,
                    'total_tests' => $total,
                    'all_passed'  => $passedCount === $total && $total > 0,
                ];
            })
            ->sortByDesc('avg_pct')
            ->values();

        return $candidates->map(fn ($row, $i) => [
            $i + 1,
            $this->sanitize($row['candidate']->name),
            $this->sanitize($row['candidate']->document_number ?? '—'),
            $row['avg_pct'],
            $row['passed'],
            $row['total_tests'],
            $row['all_passed'] ? 'Aprobó' : ($row['total_tests'] === 0 ? 'Sin pruebas' : 'No aprobó'),
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4338CA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
