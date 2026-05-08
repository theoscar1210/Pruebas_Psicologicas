<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
    {{-- safe: inlined desde archivos CSS locales del servidor, no datos de usuario --}}
    {!! file_get_contents(public_path('css/pdf/base.css')) !!}
    {!! file_get_contents(public_path('css/pdf/perfil.css')) !!}
    </style>
</head>

<body>

    {{-- Marca de agua: se repite en cada página gracias a position:fixed --}}
    <div class="marca-agua">CONFIDENCIAL</div>

    <div class="pagina">

        {{-- ══ ENCABEZADO ══════════════════════════════════════════════════════════ --}}
        <div class="header">
            <div class="header-celda-logo">
                <span class="logo-circulo">MC</span>
                <span class="logo-textos">
                    <div class="logo-nombre">MenteClara</div>
                    <div class="logo-autor">by Emma Naranjo</div>
                    <div class="logo-eslogan">Donde el talento encuentra su medida</div>
                </span>
            </div>
            <div class="header-celda-info">
                <div class="header-titulo">Perfil Psicológico del Candidato</div>
                <div class="header-subtitulo">Sistema de Evaluación — RRHH</div>
                <div class="header-fecha">
                    Fecha: {{ now()->format('d/m/Y') }}<br>
                    Psicóloga: {{ $report?->evaluator?->name ?? auth()->user()->name }}
                </div>
            </div>
        </div>

        {{-- ══ CANDIDATO ════════════════════════════════════════════════════════════ --}}
        <div class="bloque-candidato">
            <div class="candidato-celda-info">
                <div class="candidato-nombre">{{ $candidate->name }}</div>
                <div class="candidato-meta">
                    Cargo: {{ $candidate->position?->name ?? 'No asignado' }} &nbsp;|&nbsp;
                    Doc: {{ $candidate->document_number ?? '—' }} &nbsp;|&nbsp;
                    {{ $candidate->email ?? '' }}
                </div>
            </div>
        </div>

        {{-- ══ RIESGOS LABORALES ════════════════════════════════════════════════════ --}}
        <div class="tarjeta">
            <div class="titulo-seccion">Riesgos laborales identificados</div>
            @if($report?->labor_risks && count($report->labor_risks) > 0)
            @foreach($report->labor_risks as $riesgo)
            <div class="riesgo-item">⚠ {{ $riesgo }}</div>
            @endforeach
            @else
            <div class="sin-riesgo">✓ No se identificaron riesgos laborales significativos</div>
            @endif
        </div>

        {{-- ══ INFORME COMPLETO IA ═══════════════════════════════════════════════════ --}}
        @if($report?->ai_full_report)
        <div class="tarjeta">
            <div class="titulo-seccion">Informe Psicológico Completo — Análisis </div>
            @if($report->ai_full_report_recommendation)
            @php
            $aiRecClass = match($report->ai_full_report_recommendation) {
            'apto' => 'rec-apto',
            'apto_con_reservas' => 'rec-reservas',
            'no_apto' => 'rec-no-apto',
            default => '',
            };
            $aiRecLabel = match($report->ai_full_report_recommendation) {
            'apto' => 'APTO',
            'apto_con_reservas' => 'APTO CON RESERVAS',
            'no_apto' => 'NO APTO',
            default => '—',
            };
            @endphp
            <div style="margin-bottom:8px">
                <span class="badge-recomendacion {{ $aiRecClass }}">{{ $aiRecLabel }}</span>
                @if($report->ai_full_report_at)
                <span style="font-size:8px;color:#94a3b8;margin-left:8px">Generado el {{ $report->ai_full_report_at->format('d/m/Y H:i') }}</span>
                @endif
            </div>
            @endif
            @php
                $reportHtml = nl2br(preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', e($report->ai_full_report)));
            @endphp
            <p style="font-size:9px;color:#475569;line-height:1.7">{!! $reportHtml !!}</p>
        </div>
        @endif

        {{-- ══ PIE DE PÁGINA ════════════════════════════════════════════════════════ --}}
        <div class="footer">
            <div class="footer-izquierda">MenteClara · <em>Donde el talento encuentra su medida</em></div>
            <div class="footer-derecha">Documento confidencial — Uso exclusivo de RRHH · Generado {{ now()->format('d/m/Y H:i') }}</div>
        </div>

    </div>
</body>

</html>