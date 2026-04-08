<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte — {{ $candidate->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

        /* ── Encabezado ── */
        .header { background: #4338ca; color: #fff; padding: 20px 28px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; font-weight: bold; margin-bottom: 3px; }
        .header p  { font-size: 11px; opacity: .85; }
        .header-meta { font-size: 10px; margin-top: 8px; opacity: .7; }

        /* ── Info candidato ── */
        .info-grid { display: table; width: 100%; border-collapse: collapse; margin: 0 28px 20px; width: calc(100% - 56px); }
        .info-box  { display: table-cell; width: 50%; border: 1px solid #e5e7eb; padding: 12px 14px; vertical-align: top; }
        .info-box:first-child { border-right: none; }
        .info-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 3px; }
        .info-value { font-size: 12px; font-weight: bold; color: #111827; }

        /* ── Sección prueba ── */
        .section { margin: 0 28px 22px; }
        .section-title { font-size: 13px; font-weight: bold; color: #4338ca; border-bottom: 2px solid #4338ca; padding-bottom: 5px; margin-bottom: 12px; }

        /* ── Resultado resumen ── */
        .result-banner { padding: 14px 18px; margin-bottom: 14px; border-radius: 6px; }
        .result-banner.passed { background: #dcfce7; border-left: 4px solid #16a34a; }
        .result-banner.failed { background: #fee2e2; border-left: 4px solid #dc2626; }
        .result-score { font-size: 28px; font-weight: bold; }
        .result-score.passed { color: #16a34a; }
        .result-score.failed { color: #dc2626; }
        .result-label { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .result-detail { font-size: 10px; margin-top: 6px; }

        /* ── Tabla de respuestas ── */
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { background: #f3f4f6; padding: 7px 10px; text-align: left; font-size: 9px;
             text-transform: uppercase; letter-spacing: .04em; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
        td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        tr:last-child td { border-bottom: none; }
        .q-num  { width: 24px; color: #9ca3af; font-weight: bold; text-align: center; }
        .q-text { color: #374151; line-height: 1.4; }
        .q-ans  { color: #1f2937; }
        .q-pts  { width: 50px; text-align: center; font-weight: bold; }
        .pts-ok { color: #16a34a; }
        .pts-no { color: #dc2626; }
        .badge  { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: bold; }
        .badge-open { background: #f3f4f6; color: #6b7280; }

        /* ── Pie ── */
        .footer { margin: 20px 28px 0; padding-top: 10px; border-top: 1px solid #e5e7eb;
                  font-size: 9px; color: #9ca3af; display: table; width: calc(100% - 56px); }
        .footer-left  { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    {{-- ── Encabezado ── --}}
    <div class="header">
        <h1>Reporte de Candidato</h1>
        <p>Sistema de Pruebas Psicológicas — RRHH</p>
        <p class="header-meta">Generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- ── Info del candidato ── --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Candidato</div>
            <div class="info-value">{{ $candidate->name }}</div>
            @if($candidate->document_number)
                <div style="font-size:10px;color:#6b7280;margin-top:3px;">{{ $candidate->document_number }}</div>
            @endif
        </div>
        <div class="info-box">
            <div class="info-label">Cargo</div>
            <div class="info-value">{{ $candidate->position?->name ?? 'Sin asignar' }}</div>
            @if($candidate->email)
                <div style="font-size:10px;color:#6b7280;margin-top:3px;">{{ $candidate->email }}</div>
            @endif
        </div>
    </div>

    {{-- ── Pruebas ── --}}
    @forelse($candidate->assignments->where('status', 'completed') as $i => $assignment)

        @if($i > 0)
            <div class="page-break"></div>
            <div style="height:20px"></div>
        @endif

        <div class="section">
            <div class="section-title">{{ $assignment->test->name }}</div>

            {{-- Resultado resumen --}}
            @if($assignment->result)
            @php $r = $assignment->result; @endphp
            <div class="result-banner {{ $r->passed ? 'passed' : 'failed' }}">
                <table style="border:none">
                    <tr>
                        <td style="border:none;padding:0;width:120px">
                            <div class="result-score {{ $r->passed ? 'passed' : 'failed' }}">{{ $r->percentage }}%</div>
                            <div class="result-label">Puntaje obtenido</div>
                        </td>
                        <td style="border:none;padding:0">
                            <div class="result-detail">
                                <strong>{{ $r->total_score }} / {{ $r->max_score }} puntos</strong><br>
                                Mínimo requerido: {{ $assignment->test->passing_score }}%<br>
                                Resultado: <strong>{{ $r->passed ? '✓ Aprobado' : '✗ No aprobado' }}</strong>
                            </div>
                        </td>
                        <td style="border:none;padding:0;text-align:right;vertical-align:top;font-size:10px;color:#6b7280">
                            @if($assignment->started_at)
                                Iniciada: {{ $assignment->started_at->format('d/m/Y H:i') }}<br>
                            @endif
                            @if($assignment->completed_at)
                                Finalizada: {{ $assignment->completed_at->format('d/m/Y H:i') }}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            @endif

            {{-- Detalle por pregunta --}}
            <table>
                <thead>
                    <tr>
                        <th class="q-num">#</th>
                        <th class="q-text">Pregunta</th>
                        <th class="q-ans">Respuesta</th>
                        <th class="q-pts">Pts</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignment->test->questions as $question)
                    @php
                        $answer = $assignment->answers->firstWhere('question_id', $question->id);
                    @endphp
                    <tr>
                        <td class="q-num">{{ $question->order }}</td>
                        <td class="q-text">{{ $question->text }}</td>
                        <td class="q-ans">
                            @if(!$answer)
                                <span style="color:#9ca3af;font-style:italic">Sin responder</span>
                            @elseif($question->isOpen())
                                <span class="badge badge-open">Respuesta abierta</span>
                                @if($answer->text_answer)
                                    <br><span style="color:#6b7280;font-style:italic">{{ Str::limit($answer->text_answer, 80) }}</span>
                                @endif
                            @else
                                {{ $answer->option?->text ?? '—' }}
                            @endif
                        </td>
                        <td class="q-pts">
                            @if($answer && !$question->isOpen())
                                <span class="{{ $answer->score > 0 ? 'pts-ok' : 'pts-no' }}">
                                    {{ number_format($answer->score, 1) }}
                                </span>
                                / {{ $question->points }}
                            @elseif($question->isOpen())
                                <span style="color:#9ca3af">—</span>
                            @else
                                <span class="pts-no">0</span> / {{ $question->points }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @empty
        <div class="section" style="text-align:center;color:#9ca3af;padding:30px 0">
            Este candidato no tiene pruebas completadas aún.
        </div>
    @endforelse

    {{-- ── Pie de página ── --}}
    <div class="footer">
        <div class="footer-left">{{ config('app.name') }} — Documento confidencial</div>
        <div class="footer-right">{{ now()->format('d/m/Y') }}</div>
    </div>

</body>
</html>
