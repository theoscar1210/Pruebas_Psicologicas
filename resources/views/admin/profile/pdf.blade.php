<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    {!! file_get_contents(public_path('css/pdf/base.css')) !!}
    {!! file_get_contents(public_path('css/pdf/perfil.css')) !!}
</style>
</head>
<body>
<div class="pagina">

    {{-- ══ ENCABEZADO ══════════════════════════════════════════════════════════ --}}
    <div class="header">
        <div class="header-celda-logo">
            <div class="logo-tabla">
                <div class="logo-celda-circulo">
                    <div class="logo-circulo">MC</div>
                </div>
                <div class="logo-celda-texto">
                    <div class="logo-nombre">MenteClara</div>
                    <div class="logo-autor">by Emma Naranjo</div>
                    <div class="logo-eslogan">Donde el talento encuentra su medida</div>
                </div>
            </div>
        </div>
        <div class="header-celda-info">
            <div class="header-titulo">Perfil Psicológico del Candidato</div>
            <div class="header-subtitulo">Sistema de Evaluación — RRHH</div>
            <div class="header-fecha">
                Fecha: {{ now()->format('d/m/Y') }}<br>
                Evaluador: {{ $report?->evaluator?->name ?? auth()->user()->name }}
            </div>
        </div>
    </div>

    {{-- ══ CANDIDATO ════════════════════════════════════════════════════════════ --}}
    @php
        $recClass = match($report?->recommendation) {
            'apto'              => 'rec-apto',
            'apto_con_reservas' => 'rec-reservas',
            'no_apto'           => 'rec-no-apto',
            default             => '',
        };
    @endphp
    <div class="bloque-candidato">
        <div class="candidato-celda-info">
            <div class="candidato-nombre">{{ $candidate->name }}</div>
            <div class="candidato-meta">
                Cargo: {{ $candidate->position?->name ?? 'No asignado' }} &nbsp;|&nbsp;
                Doc: {{ $candidate->document_number ?? '—' }} &nbsp;|&nbsp;
                {{ $candidate->email ?? '' }}
            </div>
        </div>
        @if($report?->recommendation)
        <div class="candidato-celda-badge">
            <span class="badge-recomendacion {{ $recClass }}">{{ $report->recommendationLabel() }}</span>
        </div>
        @endif
    </div>

    {{-- ══ KPIs ══════════════════════════════════════════════════════════════════ --}}
    @if($report)
    <div class="fila-kpi">
        <div class="kpi-caja">
            <div class="kpi-etiqueta">Ajuste al cargo</div>
            <div class="kpi-valor">{{ ucfirst($report->adjustment_level ?? '—') }}</div>
        </div>
        <div class="kpi-caja">
            <div class="kpi-etiqueta">Capacidad cognitiva</div>
            <div class="kpi-valor">{{ $report->cognitive_score !== null ? number_format($report->cognitive_score, 0).'%' : '—' }}</div>
        </div>
        <div class="kpi-caja">
            <div class="kpi-etiqueta">Nivel cognitivo</div>
            <div class="kpi-valor" style="font-size:12px">{{ $report->cognitive_level ?? '—' }}</div>
        </div>
        <div class="kpi-caja">
            <div class="kpi-etiqueta">Entrevista STAR</div>
            <div class="kpi-valor">{{ $report->interview_score !== null ? number_format($report->interview_score, 0).'%' : '—' }}</div>
        </div>
        <div class="kpi-caja">
            <div class="kpi-etiqueta">Wartegg</div>
            <div class="kpi-valor">{{ $report->wartegg_score !== null ? number_format($report->wartegg_score, 0).'%' : '—' }}</div>
        </div>
    </div>
    @endif

    {{-- ══ COLUMNAS: BIG FIVE + ENTREVISTA STAR ════════════════════════════════ --}}
    <div class="dos-columnas">

        {{-- Big Five --}}
        <div class="columna-6">
            <div class="tarjeta">
                <div class="titulo-seccion">Personalidad — Big Five (OCEAN)</div>
                @php
                $dimensiones = [
                    ['etiqueta' => 'Apertura',       'clave' => 'bf_openness',          'clase' => 'barra-violeta'],
                    ['etiqueta' => 'Responsabilidad', 'clave' => 'bf_conscientiousness', 'clase' => 'barra-brand'],
                    ['etiqueta' => 'Extraversión',    'clave' => 'bf_extraversion',      'clase' => 'barra-ambar'],
                    ['etiqueta' => 'Amabilidad',      'clave' => 'bf_agreeableness',     'clase' => 'barra-verde'],
                    ['etiqueta' => 'Neuroticismo',    'clave' => 'bf_neuroticism',       'clase' => 'barra-rojo'],
                ];
                @endphp
                @foreach($dimensiones as $dim)
                @php $val = $report ? (float)$report->{$dim['clave']} : 0; @endphp
                <div class="barra-fila">
                    <div class="barra-etiqueta">
                        <span class="barra-etiqueta-izq">{{ $dim['etiqueta'] }}</span>
                        <span class="barra-etiqueta-der">{{ number_format($val, 0) }}%</span>
                    </div>
                    <div class="barra-pista">
                        <div class="barra-relleno {{ $dim['clase'] }}" style="width:{{ $val }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Competencias Assessment Center --}}
            @if($report?->competency_scores)
            <div class="tarjeta">
                <div class="titulo-seccion">Competencias — Assessment Center</div>
                @php $etiqComp = ['liderazgo'=>'Liderazgo','trabajo_equipo'=>'Trabajo equipo','orientacion_cliente'=>'Orient. cliente','toma_decisiones'=>'Toma decisiones','adaptabilidad'=>'Adaptabilidad']; @endphp
                @foreach($report->competency_scores as $clave => $puntuacion)
                <div class="barra-fila">
                    <div class="barra-etiqueta">
                        <span class="barra-etiqueta-izq">{{ $etiqComp[$clave] ?? $clave }}</span>
                        <span class="barra-etiqueta-der">{{ number_format($puntuacion, 0) }}%</span>
                    </div>
                    <div class="barra-pista">
                        <div class="barra-relleno barra-ambar" style="width:{{ $puntuacion }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Entrevista STAR + Riesgos --}}
        <div class="columna-6">
            @php
            $evaluacionStar = $candidate->evaluatorAssessments->firstWhere('assessment_type', 'star_interview');
            $etiqStar = ['trabajo_equipo'=>'Trabajo equipo','liderazgo'=>'Liderazgo','resolucion_problemas'=>'Resolución','orientacion_cliente'=>'Cliente','adaptabilidad'=>'Adaptabilidad','comunicacion'=>'Comunicación','iniciativa'=>'Iniciativa','manejo_estres'=>'Estrés','etica_integridad'=>'Ética','planificacion'=>'Planificación'];
            @endphp
            @if($evaluacionStar?->scores)
            <div class="tarjeta">
                <div class="titulo-seccion">Entrevista STAR — Competencias conductuales</div>
                @foreach($evaluacionStar->scores as $clave => $val)
                <div class="star-fila">
                    <div class="star-clave">{{ $etiqStar[$clave] ?? $clave }}</div>
                    <div class="star-segmentos">
                        <div class="star-seg-tabla">
                            @for($s = 1; $s <= 5; $s++)
                            <div class="star-seg-celda {{ $s <= $val ? 'seg-activo' : 'seg-vacio' }}"></div>
                            @endfor
                        </div>
                    </div>
                    <div class="star-puntuacion">{{ $val }}</div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Riesgos laborales --}}
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
        </div>
    </div>

    {{-- ══ OBSERVACIONES WARTEGG ════════════════════════════════════════════════ --}}
    @if($report?->projective_observations)
    <div class="tarjeta">
        <div class="titulo-seccion">Evaluación Proyectiva — Wartegg</div>
        <p style="font-size:9px;color:#475569;line-height:1.6">{{ $report->projective_observations }}</p>
    </div>
    @endif

    {{-- ══ CONCLUSIÓN Y RECOMENDACIÓN ══════════════════════════════════════════ --}}
    @if($report?->isCompleted())
    <div class="tarjeta">
        <div class="titulo-seccion">Conclusión y Recomendación del Evaluador</div>
        @if($report->summary)
        <div class="caja-resumen">{{ $report->summary }}</div>
        @endif
        @if($report->recommendation_notes)
        <p style="font-size:9px;color:#475569;line-height:1.5;margin-bottom:6px">
            <strong>Justificación:</strong> {{ $report->recommendation_notes }}
        </p>
        @endif
        <div style="display:table;margin-top:6px">
            <span class="badge-recomendacion {{ $recClass }}" style="display:table-cell;vertical-align:middle">{{ $report->recommendationLabel() }}</span>
            <span style="display:table-cell;vertical-align:middle;padding-left:10px;font-size:9px;color:#94a3b8">
                Nivel de ajuste: <strong>{{ ucfirst($report->adjustment_level) }}</strong>
            </span>
        </div>
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
