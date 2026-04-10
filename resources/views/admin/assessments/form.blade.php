@extends('layouts.admin')

@section('title', 'Evaluación Clínica — ' . $candidate->name)
@section('header', match($type) {
    'wartegg'           => 'Wartegg — ' . $candidate->name,
    'star_interview'    => 'Entrevista STAR — ' . $candidate->name,
    'assessment_center' => 'Assessment Center — ' . $candidate->name,
    default             => 'Evaluación — ' . $candidate->name,
})

@section('header-actions')
    <a href="{{ route('admin.candidates.show', $candidate) }}" class="btn-ghost btn-sm">← Volver</a>
@endsection

@section('content')

@php
    $isEdit = isset($existing) && $existing;
    $action = $isEdit
        ? route('admin.assessments.update', $existing)
        : route('admin.assessments.store', $candidate);

    $warteggBoxes = [
        ['key' => 'box_1', 'label' => 'Caja 1 — Punto',         'hint' => 'Estabilidad emocional, seguridad interna'],
        ['key' => 'box_2', 'label' => 'Caja 2 — Línea ondulada', 'hint' => 'Flexibilidad, adaptación emocional'],
        ['key' => 'box_3', 'label' => 'Caja 3 — Tres puntos',    'hint' => 'Tendencias de logro, ambición'],
        ['key' => 'box_4', 'label' => 'Caja 4 — Curva pequeña',  'hint' => 'Actitud ante lo nuevo, receptividad'],
        ['key' => 'box_5', 'label' => 'Caja 5 — Triángulo',      'hint' => 'Vitalidad, motricidad'],
        ['key' => 'box_6', 'label' => 'Caja 6 — Línea oblicua',  'hint' => 'Recursos internos, conflictos'],
        ['key' => 'box_7', 'label' => 'Caja 7 — Puntos curvos',  'hint' => 'Vida afectiva, relaciones'],
        ['key' => 'box_8', 'label' => 'Caja 8 — Línea recta',    'hint' => 'Autocontrol, voluntad'],
    ];

    $starCompetencies = [
        ['key' => 'trabajo_equipo',      'label' => 'Trabajo en equipo',        'q' => '¿Cuéntame una situación en la que trabajaste en equipo para lograr un objetivo difícil? ¿Cuál fue tu rol y qué resultados obtuviste?'],
        ['key' => 'liderazgo',           'label' => 'Liderazgo',                'q' => '¿Describe una situación donde tuviste que liderar un grupo bajo presión. Qué hiciste y qué aprendiste?'],
        ['key' => 'resolucion_problemas','label' => 'Resolución de problemas',  'q' => '¿Cuéntame sobre un problema complejo que enfrentaste en el trabajo. ¿Cómo lo diagnosticaste y qué solución implementaste?'],
        ['key' => 'orientacion_cliente', 'label' => 'Orientación al cliente',   'q' => '¿Describe una situación en la que un cliente estaba insatisfecho. ¿Qué hiciste para resolver la situación?'],
        ['key' => 'adaptabilidad',       'label' => 'Adaptabilidad',            'q' => '¿Cuéntame sobre un momento en que tuviste que adaptarte a un cambio importante. ¿Cómo lo manejaste?'],
        ['key' => 'comunicacion',        'label' => 'Comunicación efectiva',    'q' => '¿Describe una situación donde tuviste que comunicar información difícil o compleja. ¿Cómo lo hiciste?'],
        ['key' => 'iniciativa',          'label' => 'Iniciativa y proactividad','q' => '¿Cuéntame sobre una mejora o proyecto que iniciaste por tu cuenta, sin que te lo pidieran.'],
        ['key' => 'manejo_estres',       'label' => 'Manejo del estrés',        'q' => '¿Describe una situación de alta presión laboral. ¿Cómo mantuviste tu efectividad?'],
        ['key' => 'etica_integridad',    'label' => 'Ética e integridad',       'q' => '¿Cuéntame sobre una situación donde debiste tomar una decisión difícil desde el punto de vista ético.'],
        ['key' => 'planificacion',       'label' => 'Planificación y organización','q' => '¿Describe cómo organizas tu trabajo cuando tienes múltiples prioridades simultáneas.'],
    ];

    $scores = $isEdit ? ($existing->scores ?? []) : [];
@endphp

<div class="max-w-4xl">

    {{-- Información del candidato --}}
    <div class="card-info p-4 mb-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-brand-700 flex items-center justify-center text-white font-bold flex-shrink-0">
            {{ strtoupper(substr($candidate->name, 0, 1)) }}
        </div>
        <div>
            <p class="font-semibold text-slate-900">{{ $candidate->name }}</p>
            <p class="text-xs text-slate-500">{{ $candidate->position?->name ?? 'Sin cargo asignado' }} · Doc: {{ $candidate->document_number ?? '—' }}</p>
        </div>
        @if($isEdit)
            <span class="ml-auto badge-warning">Editando evaluación existente</span>
        @endif
    </div>

    <form action="{{ $action }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <input type="hidden" name="assessment_type" value="{{ $type }}">

        {{-- ══ WARTEGG ══════════════════════════════════════════════════════ --}}
        @if($type === 'wartegg')

        <div class="card mb-5">
            <div class="card-body">
                <h2 class="text-sm font-semibold text-slate-700 mb-1">Test de Wartegg — Indicadores por caja</h2>
                <p class="text-xs text-slate-400 mb-5">Califica cada caja del 1 (muy deficiente) al 5 (muy destacado) según el dibujo del candidato. Toma en cuenta: originalidad, complejidad, integración del estímulo y contenido simbólico.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($warteggBoxes as $box)
                    <div class="form-group p-4 border border-slate-100 rounded-xl bg-slate-50/60">
                        <label class="form-label">{{ $box['label'] }}</label>
                        <p class="text-[11px] text-slate-400 mb-2">{{ $box['hint'] }}</p>
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="scores[{{ $box['key'] }}]" value="{{ $i }}"
                                       {{ ($scores[$box['key']] ?? null) == $i ? 'checked' : '' }}
                                       class="sr-only peer" required>
                                <div class="py-2 text-center text-sm font-bold rounded-lg border-2 transition-all
                                            border-slate-200 bg-white text-slate-400
                                            peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700">
                                    {{ $i }}
                                </div>
                            </label>
                            @endfor
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                            <span>Muy deficiente</span><span>Muy destacado</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══ ENTREVISTA STAR ═══════════════════════════════════════════════ --}}
        @elseif($type === 'star_interview')

        <div class="card mb-5">
            <div class="card-body">
                <h2 class="text-sm font-semibold text-slate-700 mb-1">Entrevista Estructurada STAR — Calificación por competencia</h2>
                <p class="text-xs text-slate-400 mb-5">Conduce la entrevista con cada pregunta. Califica la respuesta del candidato del 1 al 5 según la calidad de la Situación, Tarea, Acción y Resultado descrito. Registra tus observaciones al final.</p>

                <div class="space-y-4">
                    @foreach($starCompetencies as $comp)
                    <div class="p-4 border border-slate-100 rounded-xl">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-800">{{ $comp['label'] }}</p>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed italic">"{{ $comp['q'] }}"</p>
                            </div>
                            <div class="flex gap-1.5 flex-shrink-0">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="scores[{{ $comp['key'] }}]" value="{{ $i }}"
                                           {{ ($scores[$comp['key']] ?? null) == $i ? 'checked' : '' }}
                                           class="sr-only peer" required>
                                    <div class="w-9 h-9 flex items-center justify-center text-sm font-bold rounded-lg border-2 transition-all
                                                border-slate-200 bg-white text-slate-400
                                                peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700">
                                        {{ $i }}
                                    </div>
                                </label>
                                @endfor
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card mb-5">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Rúbrica de calificación STAR</h3>
                <div class="grid grid-cols-5 gap-2 text-xs text-slate-600">
                    <div class="p-2 bg-red-50 rounded-lg text-center"><strong class="block text-red-700">1 — Insuficiente</strong>No responde con el método STAR. Respuesta vaga o sin evidencia conductual.</div>
                    <div class="p-2 bg-orange-50 rounded-lg text-center"><strong class="block text-orange-700">2 — Básico</strong>Describe situación pero sin detalle de acciones concretas o resultados.</div>
                    <div class="p-2 bg-amber-50 rounded-lg text-center"><strong class="block text-amber-700">3 — Adecuado</strong>Respuesta completa pero poco profunda. Resultados mencionados superficialmente.</div>
                    <div class="p-2 bg-brand-50 rounded-lg text-center"><strong class="block text-brand-700">4 — Bueno</strong>Respuesta sólida con acciones claras y resultados medibles.</div>
                    <div class="p-2 bg-emerald-50 rounded-lg text-center"><strong class="block text-emerald-700">5 — Excelente</strong>Respuesta completa, reflexiva, con impacto demostrable y aprendizaje explícito.</div>
                </div>
            </div>
        </div>

        {{-- ══ ASSESSMENT CENTER (evaluador registra resultados de escenarios) ══ --}}
        @elseif($type === 'assessment_center')

        @php
        $acCompetencies = [
            ['key' => 'liderazgo',           'label' => 'Liderazgo bajo presión'],
            ['key' => 'trabajo_equipo',      'label' => 'Trabajo en equipo y colaboración'],
            ['key' => 'orientacion_cliente', 'label' => 'Orientación al cliente'],
            ['key' => 'toma_decisiones',     'label' => 'Toma de decisiones'],
            ['key' => 'adaptabilidad',       'label' => 'Adaptabilidad al cambio'],
        ];
        @endphp

        <div class="card mb-5">
            <div class="card-body">
                <h2 class="text-sm font-semibold text-slate-700 mb-1">Assessment Center — Evaluación de respuestas escritas</h2>
                <p class="text-xs text-slate-400 mb-5">Califica las respuestas del candidato a los 5 escenarios escritos. Usa la escala 1–5 por competencia evaluada.</p>

                <div class="space-y-3">
                    @foreach($acCompetencies as $idx => $comp)
                    <div class="p-4 border border-slate-100 rounded-xl">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Escenario {{ $idx + 1 }} — {{ $comp['label'] }}</p>
                            </div>
                            <div class="flex gap-1.5">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="scores[{{ $comp['key'] }}]" value="{{ $i }}"
                                           {{ ($scores[$comp['key']] ?? null) == $i ? 'checked' : '' }}
                                           class="sr-only peer" required>
                                    <div class="w-9 h-9 flex items-center justify-center text-sm font-bold rounded-lg border-2 transition-all
                                                border-slate-200 bg-white text-slate-400
                                                peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700">
                                        {{ $i }}
                                    </div>
                                </label>
                                @endfor
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Observaciones generales --}}
        <div class="card mb-5">
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Observaciones clínicas del evaluador</label>
                    <textarea name="observations" rows="5" class="textarea"
                        placeholder="Describe hallazgos relevantes, conductas observadas, aspectos a destacar o señales de alerta…">{{ $isEdit ? $existing->observations : '' }}</textarea>
                    <p class="form-hint">Estas observaciones formarán parte del perfil psicológico del candidato.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $isEdit ? 'Actualizar evaluación' : 'Guardar evaluación' }}
            </button>
            <a href="{{ route('admin.candidates.show', $candidate) }}" class="btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

@endsection
