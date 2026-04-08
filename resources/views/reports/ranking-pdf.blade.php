<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking — {{ $position->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; }

        .header { background: #4338ca; color: #fff; padding: 20px 28px; margin-bottom: 24px; }
        .header h1 { font-size: 20px; font-weight: bold; margin-bottom: 3px; }
        .header p  { font-size: 11px; opacity: .85; }
        .header-meta { font-size: 10px; margin-top: 8px; opacity: .7; }

        .section { margin: 0 28px 20px; }
        .section-title { font-size: 13px; font-weight: bold; color: #4338ca;
                         border-bottom: 2px solid #4338ca; padding-bottom: 5px; margin-bottom: 14px; }

        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { background: #4338ca; color: #fff; padding: 8px 10px; text-align: left;
             font-size: 9px; text-transform: uppercase; letter-spacing: .04em; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        tr:nth-child(even) td { background: #f9fafb; }
        tr:last-child td { border-bottom: none; }

        .rank-1 td { background: #fef9c3 !important; font-weight: bold; }
        .rank-2 td { background: #f1f5f9 !important; }
        .rank-3 td { background: #fdf2e9 !important; }

        .medal { font-size: 14px; }
        .pct   { font-size: 13px; font-weight: bold; }
        .green { color: #16a34a; }
        .red   { color: #dc2626; }
        .gray  { color: #9ca3af; }

        .bar-wrap { background: #e5e7eb; border-radius: 4px; height: 7px; width: 100px; display: inline-block; vertical-align: middle; margin-left: 6px; }
        .bar-fill  { height: 7px; border-radius: 4px; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: bold; }
        .badge-pass { background: #dcfce7; color: #16a34a; }
        .badge-fail { background: #fee2e2; color: #dc2626; }
        .badge-pend { background: #f3f4f6; color: #6b7280; }

        .summary { display: table; width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .summary-box { display: table-cell; text-align: center; border: 1px solid #e5e7eb; padding: 12px; }
        .summary-box + .summary-box { border-left: none; }
        .summary-num { font-size: 22px; font-weight: bold; color: #4338ca; }
        .summary-lbl { font-size: 9px; color: #6b7280; margin-top: 2px; text-transform: uppercase; }

        .footer { margin: 20px 28px 0; padding-top: 10px; border-top: 1px solid #e5e7eb;
                  font-size: 9px; color: #9ca3af; display: table; width: calc(100% - 56px); }
        .footer-left  { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Ranking de Candidatos</h1>
        <p>Cargo: {{ $position->name }}</p>
        <p class="header-meta">Generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- Resumen estadístico --}}
    <div class="section">
        @php
            $total     = $candidates->count();
            $aprobaron = $candidates->where('all_passed', true)->count();
            $promedio  = $candidates->avg('avg_pct');
        @endphp
        <div class="summary">
            <div class="summary-box">
                <div class="summary-num">{{ $total }}</div>
                <div class="summary-lbl">Candidatos evaluados</div>
            </div>
            <div class="summary-box">
                <div class="summary-num" style="color:#16a34a">{{ $aprobaron }}</div>
                <div class="summary-lbl">Aprobaron todas</div>
            </div>
            <div class="summary-box">
                <div class="summary-num" style="color:#dc2626">{{ $total - $aprobaron }}</div>
                <div class="summary-lbl">No aprobaron</div>
            </div>
            <div class="summary-box">
                <div class="summary-num">{{ number_format($promedio, 1) }}%</div>
                <div class="summary-lbl">Promedio general</div>
            </div>
        </div>
    </div>

    {{-- Ranking --}}
    <div class="section">
        <div class="section-title">Ranking por puntaje promedio</div>

        @if($candidates->isEmpty())
            <p style="color:#9ca3af;text-align:center;padding:20px 0">No hay candidatos con pruebas completadas para este cargo.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th style="width:35px">#</th>
                    <th>Candidato</th>
                    <th style="width:130px">Promedio</th>
                    <th style="width:70px;text-align:center">Pruebas</th>
                    <th style="width:80px;text-align:center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($candidates as $i => $row)
                @php $rank = $i + 1; @endphp
                <tr class="{{ $rank <= 3 ? 'rank-'.$rank : '' }}">
                    <td style="text-align:center">
                        @if($rank === 1)     <span class="medal">🥇</span>
                        @elseif($rank === 2) <span class="medal">🥈</span>
                        @elseif($rank === 3) <span class="medal">🥉</span>
                        @else                <span style="color:#9ca3af">{{ $rank }}</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $row['candidate']->name }}</strong>
                        @if($row['candidate']->document_number)
                            <br><span style="font-size:9px;color:#9ca3af">{{ $row['candidate']->document_number }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="pct {{ $row['avg_pct'] >= 60 ? 'green' : 'red' }}">
                            {{ $row['avg_pct'] }}%
                        </span>
                        <span class="bar-wrap">
                            <span class="bar-fill" style="width:{{ $row['avg_pct'] }}%;background:{{ $row['avg_pct'] >= 60 ? '#16a34a' : '#dc2626' }}"></span>
                        </span>
                    </td>
                    <td style="text-align:center">{{ $row['passed'] }}/{{ $row['total_tests'] }}</td>
                    <td style="text-align:center">
                        @if($row['all_passed'])
                            <span class="badge badge-pass">Aprobó</span>
                        @elseif($row['total_tests'] === 0)
                            <span class="badge badge-pend">Sin pruebas</span>
                        @else
                            <span class="badge badge-fail">No aprobó</span>
                        @endif
                    </td>
                </tr>

                {{-- Detalle de cada prueba --}}
                @foreach($row['candidate']->assignments as $assignment)
                @if($assignment->result)
                <tr>
                    <td></td>
                    <td colspan="4" style="padding-top:2px;padding-bottom:4px;color:#6b7280;font-size:9px;padding-left:20px">
                        ↳ {{ $assignment->test->name }}:
                        <strong class="{{ $assignment->result->passed ? 'green' : 'red' }}">
                            {{ $assignment->result->percentage }}%
                        </strong>
                        ({{ $assignment->result->total_score }}/{{ $assignment->result->max_score }} pts)
                        — {{ $assignment->result->passed ? 'Aprobó' : 'No aprobó' }}
                    </td>
                </tr>
                @endif
                @endforeach

                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="footer">
        <div class="footer-left">{{ config('app.name') }} — Documento confidencial</div>
        <div class="footer-right">{{ now()->format('d/m/Y') }}</div>
    </div>

</body>
</html>
