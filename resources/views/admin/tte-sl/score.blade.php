@extends('layouts.admin')

@section('title', 'TTE-SL — Calificación M3')
@section('header', 'TTE-SL · Calificar Módulo 3')

@section('header-actions')
    <a href="{{ route('admin.candidates.show', $session->candidate) }}" class="btn-ghost btn-sm">← Candidato</a>
@endsection

@section('content')

@php
$candidate = $session->candidate;
$scenarios = [
    1 => [
        'title' => 'Escenario 1 — Conflicto de responsabilidades en entrega crítica',
        'dims'  => 'C4 + C5 + C7',
        'color' => 'rose',
        'bars'  => [
            5 => 'Actúa de inmediato contactando a Camila y Felipe para informarles. Asume la corrección si es el más capacitado, sin esperar a que "alguien más lo haga". Gestiona el conflicto con Felipe de forma directa y sin culpabilizar. Prioriza el resultado del equipo sobre la distribución formal de responsabilidades. Al día siguiente propone cómo prevenir situaciones similares.',
            4 => 'Contacta a los involucrados y coordina la corrección. Asume parte del trabajo sin conflicto. Puede omitir la conversación con Felipe sobre el proceso de revisión o posponerla para después de la entrega.',
            3 => 'Corrige el error asumiendo la tarea por necesidad pero sin gestionar la conversación con Felipe ni proponer mejoras de proceso. Enfoque solo en el resultado inmediato.',
            2 => 'Contacta a Felipe para que corrija el error aunque no esté disponible. Espera instrucciones antes de actuar. El resultado del equipo queda en riesgo por falta de iniciativa.',
            1 => 'No actúa por no ser su responsabilidad formal o espera hasta el día siguiente. No contacta a nadie. El resultado del equipo se ve comprometido por inacción.',
        ],
    ],
    2 => [
        'title' => 'Escenario 2 — Integración de un miembro con estilo diferente',
        'dims'  => 'C2 + C6 + C3',
        'color' => 'sky',
        'bars'  => [
            5 => 'Habla primero con Andrés en privado, valida su contribución técnica, le da retroalimentación específica sobre el impacto de su estilo sin atacar su carácter. Verifica con los colegas afectados que estén bien. No forma alianzas ni genera exclusión. Informa al líder al regresar de forma factual, sin dramatizar.',
            4 => 'Habla con Andrés y lo hace bien. Puede no verificar el impacto en los colegas afectados o dar retroalimentación algo general. Informa al líder.',
            3 => 'Habla con los colegas afectados para apoyarlos pero evita la conversación directa con Andrés. O habla con Andrés pero de forma muy suave que no genera cambio real.',
            2 => 'Espera a que el líder regrese para que gestione la situación. La inacción mantiene la tensión grupal.',
            1 => 'Toma partido claramente (por Andrés o por los afectados) o comenta la situación con otros generando chisme grupal. Deteriora la dinámica en lugar de mejorarla.',
        ],
    ],
    3 => [
        'title' => 'Escenario 3 — Decisión grupal que no comparte y rendición de cuentas',
        'dims'  => 'C7 + C5 + C1',
        'color' => 'amber',
        'bars'  => [
            5 => 'No menciona que lo advirtió, o si lo hace, es de forma muy breve y solo si aporta contexto para la solución. Toda su energía en la reunión se orienta a proponer soluciones concretas. Colabora activamente para salvar el proyecto sin victimizarse ni culpabilizar a quien apoyó el Enfoque B. Después de la crisis, en un espacio apropiado, propone cómo mejorar el proceso de toma de decisiones.',
            4 => 'Se enfoca principalmente en soluciones. Puede mencionar brevemente que anticipó el problema, pero sin insistir. Contribuye activamente a resolver la situación.',
            3 => 'Menciona su advertencia previa con algo de énfasis antes de aportar soluciones. El equipo puede percibir que está más interesado en tener razón que en resolver.',
            2 => 'La reunión la domina explicando por qué el Enfoque A era mejor. Las soluciones que propone son básicamente "volvamos al Enfoque A". Genera más tensión que avance.',
            1 => 'Toma una actitud de "yo lo dije" que paraliza al grupo. No propone soluciones concretas o boicotea la búsqueda de salidas porque implica validar la decisión original que no apoyó.',
        ],
    ],
];

$colorMap = [
    'rose'  => ['bg-rose-50','border-rose-200','text-rose-700'],
    'sky'   => ['bg-sky-50','border-sky-200','text-sky-700'],
    'amber' => ['bg-amber-50','border-amber-200','text-amber-700'],
];
@endphp

{{-- Encabezado del candidato --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    <div class="lg:col-span-2">
        <div class="card border-slate-100">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Candidato</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    <div><dt class="text-slate-400 text-xs">Nombre</dt><dd class="font-semibold text-slate-800">{{ $candidate->name }}</dd></div>
                    <div><dt class="text-slate-400 text-xs">Cargo aspirado</dt><dd class="text-slate-700">{{ $candidate->position?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs">M1 — SJT (0–60)</dt><dd class="font-semibold text-slate-800">{{ $session->m1_score ?? '—' }} pts</dd></div>
                    <div><dt class="text-slate-400 text-xs">M2 — Actitudes (0–200)</dt><dd class="font-semibold text-slate-800">{{ $session->m2_score ?? '—' }} pts</dd></div>
                    <div><dt class="text-slate-400 text-xs">M3 enviado el</dt><dd class="text-slate-600">{{ $session->m3_submitted_at?->format('d/m/Y H:i') }}</dd></div>
                </dl>
            </div>
        </div>
    </div>

    <div class="card border-amber-100 bg-amber-50/40">
        <div class="card-body">
            <h3 class="text-xs font-semibold text-amber-700 uppercase tracking-wider mb-2">Módulo 3 — Pendiente de calificación</h3>
            <p class="text-xs text-amber-700">Califique los 3 escenarios con la rúbrica BARS (1–5). Justifique cada puntaje. El sistema calculará automáticamente el puntaje total y el perfil de desempeño.</p>
            <div class="mt-3 pt-3 border-t border-amber-200 text-xs text-amber-600 space-y-0.5">
                <p>M3 máximo: <strong>15 pts</strong> (3 escenarios × 5)</p>
                <p>Puntaje total máximo: <strong>275 pts</strong></p>
            </div>
        </div>
    </div>
</div>

{{-- Aviso ético --}}
<div class="mb-5 p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-500">
    <strong class="text-slate-700">Uso exclusivo del evaluador.</strong> Las rúbricas BARS son confidenciales. La calificación debe basarse únicamente en los criterios conductuales descritos. Ley 1090 de 2006 — Código Deontológico del Psicólogo · Ley 1581 de 2012.
</div>

<form action="{{ route('admin.tte-sl.score.store', $session) }}" method="POST">
    @csrf

    @if($errors->any())
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
    @endif

    <div class="space-y-8">
        @foreach($scenarios as $num => $sc)
        @php $c = $colorMap[$sc['color']]; @endphp

        <div class="card border-slate-100">
            <div class="px-5 py-3 {{ $c[0] }} border-b {{ $c[1] }} flex items-center justify-between flex-wrap gap-2">
                <span class="text-xs font-bold {{ $c[2] }} uppercase tracking-wider">{{ $sc['title'] }}</span>
                <span class="text-[10px] font-semibold border {{ $c[1] }} {{ $c[2] }} rounded-full px-2 py-0.5">{{ $sc['dims'] }}</span>
            </div>

            <div class="p-5 space-y-5">

                {{-- Respuesta del candidato --}}
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Respuesta del candidato</p>
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-sm text-slate-700 leading-relaxed whitespace-pre-wrap max-h-52 overflow-y-auto">{{ $session->m3_responses[$num] ?? $session->m3_responses[(string)$num] ?? '—' }}</div>
                </div>

                {{-- Rúbrica BARS --}}
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Rúbrica BARS — Seleccione el nivel (1–5)</p>
                    <div class="space-y-2">
                        @foreach($sc['bars'] as $level => $desc)
                        <label class="flex gap-3 p-3 rounded-lg border border-slate-100 hover:border-brand-300 hover:bg-brand-50/40 cursor-pointer transition-all has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                            <input type="radio"
                                   name="scores[{{ $num }}]"
                                   value="{{ $level }}"
                                   class="mt-0.5 flex-shrink-0 text-brand-600 focus:ring-brand-500"
                                   required>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-brand-700 mb-0.5">Nivel {{ $level }}</p>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $desc }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error("scores.$num")
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Justificación --}}
                <div>
                    <label class="form-label text-xs" for="just-{{ $num }}">
                        Justificación del puntaje
                        <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <textarea name="just[{{ $num }}]"
                              id="just-{{ $num }}"
                              rows="3"
                              class="textarea w-full text-sm {{ $errors->has("just.$num") ? 'border-red-400' : '' }}"
                              placeholder="Fundamente su calificación con evidencia concreta de la respuesta del candidato...">{{ old("just.$num") }}</textarea>
                    @error("just.$num")
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Observaciones generales --}}
    <div class="card border-slate-100 mt-6">
        <div class="card-body">
            <label class="form-label text-xs" for="observations">
                Observaciones generales del evaluador <span class="form-hint">(opcional)</span>
            </label>
            <textarea name="observations" id="observations" rows="4"
                      class="textarea w-full text-sm"
                      placeholder="Observaciones adicionales sobre el perfil colaborativo del candidato, señales de alerta, contexto situacional...">{{ old('observations') }}</textarea>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-between gap-4 flex-wrap">
        <a href="{{ route('admin.candidates.show', $session->candidate) }}" class="btn-ghost btn-sm">Cancelar</a>
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Guardar calificación y calcular puntaje final
        </button>
    </div>

</form>

@endsection
