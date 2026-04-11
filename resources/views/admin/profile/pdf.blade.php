<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; background: #fff; }
    .page { padding: 28px 30px; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0F766E; padding-bottom: 14px; margin-bottom: 18px; }
    .header-left h1 { font-size: 15px; font-weight: 700; color: #0D3330; }
    .header-left p  { font-size: 9px; color: #64748b; margin-top: 2px; }
    .header-right   { text-align: right; font-size: 9px; color: #94a3b8; }

    /* Candidate block */
    .candidate-block { background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; }
    .candidate-name  { font-size: 13px; font-weight: 700; color: #0D3330; }
    .candidate-meta  { font-size: 9px; color: #475569; margin-top: 2px; }
    .rec-badge       { font-size: 11px; font-weight: 700; padding: 5px 12px; border-radius: 6px; }
    .rec-apto        { background: #dcfce7; color: #166534; }
    .rec-reservas    { background: #fef9c3; color: #854d0e; }
    .rec-noApto      { background: #fee2e2; color: #991b1b; }

    /* Sections */
    .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #e2e8f0; }
    .two-col { display: flex; gap: 14px; margin-bottom: 14px; }
    .col-6  { width: 48%; }
    .col-4  { width: 31%; }
    .col-8  { width: 65%; }

    /* Progress bars */
    .bar-row { margin-bottom: 6px; }
    .bar-label { display: flex; justify-content: space-between; font-size: 9px; margin-bottom: 2px; }
    .bar-track { height: 6px; background: #f1f5f9; border-radius: 3px; overflow: hidden; }
    .bar-fill  { height: 6px; border-radius: 3px; }
    .bar-brand   { background: #0F766E; }
    .bar-violet  { background: #8b5cf6; }
    .bar-amber   { background: #f59e0b; }
    .bar-emerald { background: #10b981; }
    .bar-red     { background: #ef4444; }

    /* KPI boxes */
    .kpi-row { display: flex; gap: 10px; margin-bottom: 14px; }
    .kpi-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; }
    .kpi-label-sm { font-size: 8px; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; }
    .kpi-val      { font-size: 16px; font-weight: 700; color: #0F766E; margin-top: 2px; }

    /* Risks */
    .risk-item { background: #fff7ed; border-left: 3px solid #f97316; padding: 5px 8px; border-radius: 0 4px 4px 0; margin-bottom: 4px; font-size: 9px; color: #9a3412; }
    .no-risk   { background: #f0fdf4; border-left: 3px solid #22c55e; padding: 5px 8px; border-radius: 0 4px 4px 0; font-size: 9px; color: #166534; }

    /* Star bars */
    .star-row { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
    .star-key  { width: 80px; font-size: 9px; color: #475569; }
    .star-bars { display: flex; gap: 2px; flex: 1; }
    .star-seg  { flex: 1; height: 7px; border-radius: 2px; }
    .seg-fill  { background: #0F766E; }
    .seg-empty { background: #e2e8f0; }

    /* Summary box */
    .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; font-size: 9px; line-height: 1.6; color: #475569; }

    /* Footer */
    .footer { border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 20px; display: flex; justify-content: space-between; font-size: 8px; color: #94a3b8; }

    .card-block { background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; margin-bottom: 12px; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                <svg width="28" height="28" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="23" cy="23" r="21" fill="#14B8A6" fill-opacity="0.12"/>
                    <circle cx="23" cy="23" r="21" stroke="#0F766E" stroke-width="1.5"/>
                    <line x1="9" y1="31" x2="9" y2="15" stroke="#1E293B" stroke-width="2.4" stroke-linecap="round"/>
                    <line x1="9" y1="15" x2="17" y2="24" stroke="#1E293B" stroke-width="2.4" stroke-linecap="round"/>
                    <line x1="17" y1="24" x2="25" y2="15" stroke="#1E293B" stroke-width="2.4" stroke-linecap="round"/>
                    <line x1="25" y1="15" x2="25" y2="31" stroke="#1E293B" stroke-width="2.4" stroke-linecap="round"/>
                    <path d="M39 17 C36 13 28 13 28 23 C28 33 36 33 39 29" stroke="#0F766E" stroke-width="2.4" fill="none" stroke-linecap="round"/>
                    <circle cx="40" cy="10" r="3" fill="#14B8A6"/>
                </svg>
                <h1>MenteClara</h1>
            </div>
            <p>Perfil Psicológico del Candidato — by Emma Naranjo</p>
        </div>
        <div class="header-right">
            Fecha: {{ now()->format('d/m/Y') }}<br>
            Evaluador: {{ $report?->evaluator?->name ?? auth()->user()->name }}
        </div>
    </div>

    {{-- Candidato --}}
    @php
        $recClass = match($report?->recommendation) {
            'apto'              => 'rec-apto',
            'apto_con_reservas' => 'rec-reservas',
            'no_apto'           => 'rec-noApto',
            default             => '',
        };
    @endphp
    <div class="candidate-block">
        <div>
            <div class="candidate-name">{{ $candidate->name }}</div>
            <div class="candidate-meta">
                Cargo: {{ $candidate->position?->name ?? 'No asignado' }} &nbsp;|&nbsp;
                Documento: {{ $candidate->document_number ?? '—' }} &nbsp;|&nbsp;
                {{ $candidate->email ?? '' }}
            </div>
        </div>
        @if($report?->recommendation)
        <div class="rec-badge {{ $recClass }}">{{ $report->recommendationLabel() }}</div>
        @endif
    </div>

    {{-- KPIs --}}
    @if($report)
    <div class="kpi-row">
        <div class="kpi-box">
            <div class="kpi-label-sm">Ajuste al cargo</div>
            <div class="kpi-val">{{ ucfirst($report->adjustment_level ?? '—') }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label-sm">Capacidad cognitiva</div>
            <div class="kpi-val">{{ $report->cognitive_score !== null ? number_format($report->cognitive_score, 0).'%' : '—' }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label-sm">Nivel cognitivo</div>
            <div class="kpi-val" style="font-size:12px">{{ $report->cognitive_level ?? '—' }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label-sm">Entrevista STAR</div>
            <div class="kpi-val">{{ $report->interview_score !== null ? number_format($report->interview_score, 0).'%' : '—' }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label-sm">Wartegg</div>
            <div class="kpi-val">{{ $report->wartegg_score !== null ? number_format($report->wartegg_score, 0).'%' : '—' }}</div>
        </div>
    </div>
    @endif

    <div class="two-col">

        {{-- Big Five --}}
        <div class="col-6">
            <div class="card-block">
                <div class="section-title">Módulo Personalidad — Big Five (OCEAN)</div>
                @php
                $bfCols = [
                    ['label'=>'Apertura',      'key'=>'bf_openness',         'cls'=>'bar-violet'],
                    ['label'=>'Responsabilidad','key'=>'bf_conscientiousness','cls'=>'bar-brand'],
                    ['label'=>'Extraversión',   'key'=>'bf_extraversion',     'cls'=>'bar-amber'],
                    ['label'=>'Amabilidad',     'key'=>'bf_agreeableness',    'cls'=>'bar-emerald'],
                    ['label'=>'Neuroticismo',   'key'=>'bf_neuroticism',      'cls'=>'bar-red'],
                ];
                @endphp
                @foreach($bfCols as $dim)
                @php $val = $report ? (float)$report->{$dim['key']} : 0; @endphp
                <div class="bar-row">
                    <div class="bar-label"><span>{{ $dim['label'] }}</span><span>{{ number_format($val, 0) }}%</span></div>
                    <div class="bar-track"><div class="bar-fill {{ $dim['cls'] }}" style="width:{{ $val }}%"></div></div>
                </div>
                @endforeach
            </div>

            {{-- Competencias --}}
            @if($report?->competency_scores)
            <div class="card-block">
                <div class="section-title">Módulo Competencias — Assessment Center</div>
                @php $compL = ['liderazgo'=>'Liderazgo','trabajo_equipo'=>'Trabajo equipo','orientacion_cliente'=>'Orient. cliente','toma_decisiones'=>'Toma decisiones','adaptabilidad'=>'Adaptabilidad']; @endphp
                @foreach($report->competency_scores as $key => $score)
                <div class="bar-row">
                    <div class="bar-label"><span>{{ $compL[$key] ?? $key }}</span><span>{{ number_format($score, 0) }}%</span></div>
                    <div class="bar-track"><div class="bar-fill bar-amber" style="width:{{ $score }}%"></div></div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Cognitivo + STAR + Riesgos --}}
        <div class="col-6">
            {{-- Entrevista STAR --}}
            @php
            $starAssmt = $candidate->evaluatorAssessments->firstWhere('assessment_type','star_interview');
            $starL = ['trabajo_equipo'=>'Trabajo equipo','liderazgo'=>'Liderazgo','resolucion_problemas'=>'Resolución','orientacion_cliente'=>'Cliente','adaptabilidad'=>'Adaptabilidad','comunicacion'=>'Comunicación','iniciativa'=>'Iniciativa','manejo_estres'=>'Estrés','etica_integridad'=>'Ética','planificacion'=>'Planificación'];
            @endphp
            @if($starAssmt?->scores)
            <div class="card-block">
                <div class="section-title">Entrevista STAR — Competencias conductuales</div>
                @foreach($starAssmt->scores as $key => $val)
                <div class="star-row">
                    <div class="star-key">{{ $starL[$key] ?? $key }}</div>
                    <div class="star-bars">
                        @for($s = 1; $s <= 5; $s++)
                        <div class="star-seg {{ $s <= $val ? 'seg-fill' : 'seg-empty' }}"></div>
                        @endfor
                    </div>
                    <span style="font-size:9px;font-weight:700;color:#0F766E;width:12px;text-align:right">{{ $val }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Riesgos --}}
            <div class="card-block">
                <div class="section-title">Riesgos laborales identificados</div>
                @if($report?->labor_risks && count($report->labor_risks) > 0)
                    @foreach($report->labor_risks as $risk)
                    <div class="risk-item">⚠ {{ $risk }}</div>
                    @endforeach
                @else
                    <div class="no-risk">✓ No se identificaron riesgos laborales significativos</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Observaciones proyectivas --}}
    @if($report?->projective_observations)
    <div class="card-block">
        <div class="section-title">Evaluación proyectiva — Wartegg</div>
        <p style="font-size:9px;color:#475569;line-height:1.6">{{ $report->projective_observations }}</p>
    </div>
    @endif

    {{-- Resumen y recomendación --}}
    @if($report?->isCompleted())
    <div class="card-block">
        <div class="section-title">Conclusión y Recomendación del Evaluador</div>
        @if($report->summary)
        <div class="summary-box" style="margin-bottom:8px">{{ $report->summary }}</div>
        @endif
        @if($report->recommendation_notes)
        <p style="font-size:9px;color:#475569;line-height:1.5;margin-bottom:6px"><strong>Justificación:</strong> {{ $report->recommendation_notes }}</p>
        @endif
        <div style="display:flex;align-items:center;gap:10px;margin-top:6px">
            <div class="rec-badge {{ $recClass }}" style="font-size:12px">{{ $report->recommendationLabel() }}</div>
            <span style="font-size:9px;color:#94a3b8">Nivel de ajuste: <strong>{{ ucfirst($report->adjustment_level) }}</strong></span>
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <span>MenteClara · <em>Donde el talento encuentra su medida</em></span>
        <span>Documento confidencial — Uso exclusivo de RRHH · Generado {{ now()->format('d/m/Y H:i') }}</span>
    </div>

</div>
</body>
</html>
