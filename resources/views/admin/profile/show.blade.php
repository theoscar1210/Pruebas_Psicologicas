@extends('layouts.admin')

@section('title', 'Perfil Psicológico — ' . $candidate->name)
@section('header', 'Perfil Psicológico')

@section('header-actions')
    @if($report?->isCompleted())
        <a href="{{ route('admin.profile.pdf', $candidate) }}" class="btn-danger btn-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exportar PDF
        </a>
    @endif
    <form method="POST" action="{{ route('admin.profile.generate', $candidate) }}" class="inline">
        @csrf
        <button type="submit" class="btn-secondary btn-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Generar / Actualizar perfil
        </button>
    </form>
    <a href="{{ route('admin.candidates.show', $candidate) }}" class="btn-ghost btn-sm">← Candidato</a>
@endsection

@section('content')

@php
    $bigFive = [
        ['key' => 'bf_openness',         'label' => 'Apertura',         'color' => 'bg-violet-500'],
        ['key' => 'bf_conscientiousness','label' => 'Responsabilidad',  'color' => 'bg-brand-500'],
        ['key' => 'bf_extraversion',     'label' => 'Extraversión',     'color' => 'bg-amber-500'],
        ['key' => 'bf_agreeableness',    'label' => 'Amabilidad',       'color' => 'bg-emerald-500'],
        ['key' => 'bf_neuroticism',      'label' => 'Neuroticismo',     'color' => 'bg-red-500'],
    ];
    $pf16Names = [
        'factor_A'=>'Afabilidad','factor_B'=>'Razonamiento','factor_C'=>'Estabilidad',
        'factor_E'=>'Dominancia','factor_F'=>'Animación','factor_G'=>'Normas',
        'factor_H'=>'Atrevimiento','factor_I'=>'Sensibilidad','factor_L'=>'Vigilancia',
        'factor_M'=>'Abstracción','factor_N'=>'Privacidad','factor_O'=>'Aprensión',
        'factor_Q1'=>'Apertura al cambio','factor_Q2'=>'Autosuficiencia',
        'factor_Q3'=>'Perfeccionismo','factor_Q4'=>'Tensión',
    ];

    $assessments = $candidate->evaluatorAssessments->keyBy('assessment_type');
    $wartegg = $assessments['wartegg'] ?? null;
    $star    = $assessments['star_interview'] ?? null;

    $ravenAssignment = $candidate->assignments->filter(
        fn($a) => $a->test?->test_type === 'raven' && $a->status === 'completed'
    )->sortByDesc('completed_at')->first();
    $ravenTotal = $ravenAssignment?->dimensionScores->firstWhere('dimension_key', 'raven_total');
@endphp

{{-- ── Header candidato ──────────────────────────────────────────────────── --}}
<div class="card mb-6">
    <div class="card-body py-4">
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-brand-700 flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
                {{ strtoupper(substr($candidate->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="font-bold text-slate-900 text-base">{{ $candidate->name }}</h2>
                <p class="text-sm text-slate-500">{{ $candidate->position?->name ?? '—' }} · {{ $candidate->document_number ?? 'Sin documento' }}</p>
            </div>
            @if($report)
                <div class="flex items-center gap-3">
                    <div class="text-center">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Ajuste al cargo</p>
                        <span class="{{ $report->adjustmentBadgeClass() }} text-sm">{{ ucfirst($report->adjustment_level ?? '—') }}</span>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Recomendación</p>
                        <span class="{{ $report->recommendationBadgeClass() }}">{{ $report->recommendationLabel() }}</span>
                    </div>
                </div>
            @else
                <span class="badge-neutral">Perfil no generado</span>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ════ COLUMNA IZQUIERDA ════════════════════════════════════════════ --}}
    <div class="space-y-5">

        {{-- Módulo Personalidad — Big Five --}}
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-700 text-sm">Personalidad — Big Five</h3>
                    <span class="badge-purple">OCEAN</span>
                </div>
                @if($report && $report->bf_openness !== null)
                    <div class="space-y-3">
                        @foreach($bigFive as $dim)
                        @php $val = (float)($report->{$dim['key']} ?? 0); @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-slate-600 font-medium">{{ $dim['label'] }}</span>
                                <span class="font-bold text-slate-800">{{ number_format($val, 0) }}%</span>
                            </div>
                            <div class="progress-track h-2">
                                <div class="progress-bar {{ $dim['color'] }} h-2" x-data :style="{ width: '{{ $val }}%' }"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 text-center py-4">Big Five no completado</p>
                @endif
            </div>
        </div>

        {{-- 16PF --}}
        @if($report?->pf16_scores)
        <div class="card">
            <div class="card-body">
                <h3 class="font-semibold text-slate-700 text-sm mb-4">16PF — Factores de Personalidad</h3>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                    @foreach($report->pf16_scores as $key => $score)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 truncate">{{ $pf16Names[$key] ?? $key }}</span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-12 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-1.5 bg-brand-500 rounded-full" x-data :style="{ width: '{{ min(100, $score) }}%' }"></div>
                            </div>
                            <span class="font-semibold text-slate-700 w-6 text-right">{{ number_format($score, 0) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Evaluaciones Clínicas --}}
        <div class="card">
            <div class="card-body">
                <h3 class="font-semibold text-slate-700 text-sm mb-3">Evaluaciones Clínicas</h3>
                <div class="space-y-2">
                    @foreach([['wartegg','Wartegg','proyectivo'],['star_interview','Entrevista STAR','entrevista'],['assessment_center','Assessment Center','competencias']] as [$aType, $aLabel, $aModule])
                    @php $ass = $assessments[$aType] ?? null; @endphp
                    <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ $aLabel }}</p>
                            @if($ass)
                                <p class="text-xs text-slate-400">{{ $ass->completed_at?->format('d/m/Y') }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($ass)
                                <span class="text-sm font-bold text-brand-700">{{ number_format($ass->overall_score, 0) }}/100</span>
                                <a href="{{ route('admin.assessments.edit', $ass) }}" class="text-xs text-brand-600 hover:underline">Editar</a>
                            @else
                                <a href="{{ route('admin.assessments.create', $candidate) }}?type={{ $aType }}"
                                   class="btn-secondary btn-sm">Evaluar</a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- ════ COLUMNA CENTRAL ══════════════════════════════════════════════ --}}
    <div class="space-y-5">

        {{-- Módulo Cognitivo — Raven --}}
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-700 text-sm">Cognitivo — Matrices de Raven</h3>
                    <span class="badge-info">CI</span>
                </div>
                @if($report?->cognitive_score !== null)
                    <div class="text-center py-2">
                        <p class="text-4xl font-bold {{ $report->cognitive_score >= 60 ? 'text-emerald-600' : ($report->cognitive_score >= 40 ? 'text-amber-600' : 'text-red-500') }}">
                            {{ number_format($report->cognitive_score, 0) }}%
                        </p>
                        <p class="text-sm font-semibold text-slate-600 mt-1">{{ $report->cognitive_level }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">Percentil ≈ {{ $report->cognitive_percentile }}°</p>
                    </div>
                    @if($ravenAssignment)
                    <div class="mt-4 space-y-2">
                        @foreach($ravenAssignment->dimensionScores->where('dimension_key', '!=', 'raven_total') as $setScore)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-slate-500">{{ $setScore->dimension_name }}</span>
                                <span class="font-semibold">{{ $setScore->raw_score }} / {{ $ravenAssignment->test->questions->where('category', $setScore->dimension_key)->count() }}</span>
                            </div>
                            <div class="progress-track h-1.5">
                                <div class="progress-bar bg-brand-500 h-1.5" x-data :style="{ width: '{{ $setScore->normalized_score }}%' }"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                @else
                    <p class="text-xs text-slate-400 text-center py-6">Matrices de Raven no completadas</p>
                @endif
            </div>
        </div>

        {{-- Módulo Competencias --}}
        @if($report?->competency_scores)
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-700 text-sm">Competencias Laborales</h3>
                    <span class="badge-warning">AC</span>
                </div>
                @php
                $compLabels = [
                    'liderazgo'=>'Liderazgo','trabajo_equipo'=>'Trabajo en equipo',
                    'orientacion_cliente'=>'Orient. al cliente','toma_decisiones'=>'Toma de decisiones',
                    'adaptabilidad'=>'Adaptabilidad',
                ];
                @endphp
                <div class="space-y-3">
                    @foreach($report->competency_scores as $key => $score)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600">{{ $compLabels[$key] ?? $key }}</span>
                            <span class="font-bold text-slate-800">{{ number_format($score, 0) }}%</span>
                        </div>
                        <div class="progress-track h-2">
                            <div class="progress-bar bg-amber-500 h-2" x-data :style="{ width: '{{ $score }}%' }"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Wartegg Observaciones --}}
        @if($wartegg?->observations)
        <div class="card">
            <div class="card-body">
                <h3 class="font-semibold text-slate-700 text-sm mb-2">Observaciones Proyectivas (Wartegg)</h3>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $wartegg->observations }}</p>
                <p class="text-xs text-slate-400 mt-2">Evaluador: {{ $wartegg->evaluator?->name }}</p>
            </div>
        </div>
        @endif

    </div>

    {{-- ════ COLUMNA DERECHA — Resultado final ═══════════════════════════ --}}
    <div class="space-y-5">

        {{-- Riesgos laborales --}}
        @if($report?->labor_risks)
        <div class="{{ count($report->labor_risks) > 0 ? 'card-danger' : 'card-success' }} card">
            <div class="card-body">
                <h3 class="font-semibold text-sm mb-3 {{ count($report->labor_risks) > 0 ? 'text-red-700' : 'text-emerald-700' }}">
                    Riesgos laborales identificados
                </h3>
                @if(count($report->labor_risks) > 0)
                    <ul class="space-y-1.5">
                        @foreach($report->labor_risks as $risk)
                        <li class="flex items-start gap-2 text-sm text-red-700">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $risk }}
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-emerald-700">No se identificaron riesgos laborales significativos.</p>
                @endif
            </div>
        </div>
        @endif

        {{-- Recomendación final --}}
        @if($report)
        <div class="card">
            <div class="card-body">
                <h3 class="font-semibold text-slate-700 text-sm mb-4">Conclusión del Evaluador</h3>

                @if($report->isCompleted())
                    <div class="text-center py-3">
                        @php
                            $recColor = match($report->recommendation) {
                                'apto'              => 'text-emerald-600 bg-emerald-50 border-emerald-200',
                                'apto_con_reservas' => 'text-amber-700 bg-amber-50 border-amber-200',
                                'no_apto'           => 'text-red-700 bg-red-50 border-red-200',
                                default             => 'text-slate-600 bg-slate-50 border-slate-200',
                            };
                        @endphp
                        <div class="inline-block px-6 py-3 rounded-xl border-2 {{ $recColor }} font-bold text-xl mb-3">
                            {{ $report->recommendationLabel() }}
                        </div>
                        @if($report->recommendation_notes)
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $report->recommendation_notes }}</p>
                        @endif
                        @if($report->summary)
                            <div class="mt-3 p-3 bg-slate-50 rounded-xl text-left">
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Resumen narrativo</p>
                                <p class="text-sm text-slate-700 leading-relaxed">{{ $report->summary }}</p>
                            </div>
                        @endif
                        <p class="text-xs text-slate-400 mt-3">
                            Evaluado por {{ $report->evaluator?->name }} · {{ $report->completed_at?->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @else
                    {{-- Formulario de cierre --}}
                    <form action="{{ route('admin.profile.complete', $candidate) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Recomendación final <span class="form-required">*</span></label>
                            <select name="recommendation" class="select" required>
                                <option value="">— Selecciona —</option>
                                <option value="apto"              {{ old('recommendation') === 'apto'              ? 'selected' : '' }}>APTO</option>
                                <option value="apto_con_reservas" {{ old('recommendation') === 'apto_con_reservas' ? 'selected' : '' }}>APTO CON RESERVAS</option>
                                <option value="no_apto"           {{ old('recommendation') === 'no_apto'           ? 'selected' : '' }}>NO APTO</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nivel de ajuste al cargo <span class="form-required">*</span></label>
                            <select name="adjustment_level" class="select" required>
                                <option value="alto"  {{ old('adjustment_level', $report->adjustment_level) === 'alto'  ? 'selected' : '' }}>Alto</option>
                                <option value="medio" {{ old('adjustment_level', $report->adjustment_level) === 'medio' ? 'selected' : '' }}>Medio</option>
                                <option value="bajo"  {{ old('adjustment_level', $report->adjustment_level) === 'bajo'  ? 'selected' : '' }}>Bajo</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Notas de la recomendación</label>
                            <textarea name="recommendation_notes" rows="3" class="textarea"
                                placeholder="Justificación de la decisión, condiciones especiales…">{{ old('recommendation_notes') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Resumen narrativo del perfil</label>
                            <textarea name="summary" rows="4" class="textarea"
                                placeholder="Síntesis cualitativa del candidato para el área de RRHH…">{{ old('summary') }}</textarea>
                        </div>
                        <button type="submit" class="btn-primary w-full justify-center">
                            Cerrar y emitir recomendación
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @else
            <div class="card border-dashed">
                <div class="card-body py-10 text-center text-slate-400 text-sm">
                    Genera el perfil psicológico usando el botón superior para ver la recomendación automática.
                </div>
            </div>
        @endif

    </div>
</div>

{{-- ── Entrevista STAR — ancho completo ───────────────────────────────────── --}}
@if($star)
<div class="mt-6 card">
    <div class="card-body">

        {{-- Encabezado --}}
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <h2 class="text-base font-semibold text-slate-700">Entrevista STAR</h2>
                <span class="badge-success">Entrevista conductual</span>
            </div>
            <div class="text-right">
                <span class="text-2xl font-bold text-brand-700">{{ number_format($star->overall_score, 0) }}</span>
                <span class="text-sm text-slate-400 ml-0.5">/100</span>
                @if($star->completed_at)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $star->completed_at->format('d/m/Y') }} · {{ $star->evaluator?->name }}</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Competencias evaluadas --}}
            @if($star->scores)
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Competencias evaluadas</p>
                @php
                $starLabels = [
                    'trabajo_equipo'      => 'Trabajo en equipo',
                    'liderazgo'           => 'Liderazgo',
                    'resolucion_problemas'=> 'Resolución de problemas',
                    'orientacion_cliente' => 'Orientación al cliente',
                    'adaptabilidad'       => 'Adaptabilidad',
                    'comunicacion'        => 'Comunicación',
                    'iniciativa'          => 'Iniciativa',
                    'manejo_estres'       => 'Manejo del estrés',
                    'etica_integridad'    => 'Ética e integridad',
                    'planificacion'       => 'Planificación',
                ];
                @endphp
                <div class="space-y-2.5">
                    @foreach($star->scores as $key => $val)
                    <div class="flex items-center gap-3 text-xs">
                        <span class="text-slate-600 font-medium w-36 shrink-0 truncate">{{ $starLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</span>
                        <div class="flex gap-1 flex-1">
                            @for($s = 1; $s <= 5; $s++)
                            <div class="flex-1 h-4 rounded {{ $s <= $val ? 'bg-brand-500' : 'bg-slate-100' }}"></div>
                            @endfor
                        </div>
                        <span class="font-bold text-slate-700 shrink-0 w-8 text-right">{{ $val }}/5</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Observaciones --}}
            @if($star->observations)
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Observaciones del evaluador</p>
                <p class="text-sm text-slate-600 leading-relaxed break-words">{{ $star->observations }}</p>
            </div>
            @elseif(!$star->scores)
            <div class="flex items-center justify-center py-6 text-slate-400 text-sm">
                Sin datos registrados
            </div>
            @endif

        </div>
    </div>
</div>
@endif

{{-- ── Narrativas automáticas con IA ──────────────────────────────────────── --}}
@if($report)
<div class="mt-8">
    <div class="flex items-center gap-3 mb-4">
        <h2 class="text-base font-semibold text-slate-700">Narrativas automáticas</h2>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
            IA — Groq
        </span>
        <p class="text-xs text-slate-400">Genera un borrador de cada sección; edítalo antes de incluirlo en el informe final.</p>
    </div>

    @php
    $narrativeSections = [
        ['key' => 'personality',  'label' => 'Personalidad',   'badge' => 'badge-purple', 'badge_text' => 'Big Five + 16PF'],
        ['key' => 'cognitive',    'label' => 'Cognitivo',       'badge' => 'badge-info',   'badge_text' => 'Raven'],
        ['key' => 'competencies', 'label' => 'Competencias',    'badge' => 'badge-warning','badge_text' => 'AC'],
        ['key' => 'projective',   'label' => 'Proyectivo',      'badge' => 'badge-neutral','badge_text' => 'Wartegg'],
        ['key' => 'interview',    'label' => 'Entrevista STAR', 'badge' => 'badge-success','badge_text' => 'STAR'],
    ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($narrativeSections as $ns)
        @php $narrativeField = 'narrative_' . $ns['key']; $existingText = $report->$narrativeField ?? ''; @endphp
        <div class="card"
             x-data="{
                loading: false,
                text: @js($existingText),
                async generate() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('admin.profile.narrative', $candidate) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ section: '{{ $ns['key'] }}' }),
                        });
                        const data = await res.json();
                        if (data.text) {
                            this.text = data.text;
                        } else {
                            alert(data.error || 'Error generando narrativa.');
                        }
                    } catch(e) {
                        alert('Error de conexión.');
                    } finally {
                        this.loading = false;
                    }
                }
             }">
            <div class="card-body">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-slate-700 text-sm">{{ $ns['label'] }}</h3>
                        <span class="{{ $ns['badge'] }} text-xs">{{ $ns['badge_text'] }}</span>
                    </div>
                    <button type="button"
                            @click="generate()"
                            :disabled="loading"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-violet-600 hover:bg-violet-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg x-show="!loading" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                        </svg>
                        <svg x-show="loading" x-cloak class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="loading ? 'Generando…' : (text ? 'Regenerar' : 'Generar con IA')"></span>
                    </button>
                </div>

                <template x-if="text">
                    <div class="bg-violet-50 border border-violet-100 rounded-xl p-3">
                        <p class="text-sm text-slate-700 leading-relaxed" x-text="text"></p>
                    </div>
                </template>
                <template x-if="!text">
                    <p class="text-xs text-slate-400 text-center py-4 border border-dashed border-slate-200 rounded-xl">
                        Sin narrativa generada aún
                    </p>
                </template>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
