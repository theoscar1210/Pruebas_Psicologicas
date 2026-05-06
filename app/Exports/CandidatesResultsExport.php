<?php

namespace App\Exports;

use App\Models\TestAssignment;
use App\Exports\Concerns\SanitizesForExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CandidatesResultsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    use SanitizesForExcel;
    public function __construct(private ?int $positionId = null) {}

    public function title(): string
    {
        return 'Resultados';
    }

    public function headings(): array
    {
        return [
            'Candidato',
            'Documento',
            'Cargo',
            'Prueba',
            'Estado',
            'Puntaje',
            'Máximo',
            'Porcentaje',
            'Resultado',
            'Iniciada',
            'Finalizada',
        ];
    }

    public function collection()
    {
        $query = TestAssignment::with(['candidate.position', 'test', 'result'])
            ->where('status', 'completed');

        if ($this->positionId) {
            $query->whereHas('candidate', fn ($q) => $q->where('position_id', $this->positionId));
        }

        return $query->get()->map(fn (TestAssignment $a) => [
            $this->sanitize($a->candidate->name),
            $this->sanitize($a->candidate->document_number ?? '—'),
            $this->sanitize($a->candidate->position?->name ?? '—'),
            $this->sanitize($a->test->name),
            'Completada',
            $a->result?->total_score ?? 0,
            $a->result?->max_score ?? 0,
            ($a->result?->percentage ?? 0) . '%',
            $a->result?->passed ? 'Aprobado' : 'No aprobado',
            $a->started_at?->format('d/m/Y H:i') ?? '—',
            $a->completed_at?->format('d/m/Y H:i') ?? '—',
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Encabezado en azul
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4338CA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
