@extends('layouts.admin')

@section('title', 'TSC-SL — Calificación M3')
@section('header', 'TSC-SL · Calificar Módulo 3')

@section('header-actions')
    <a href="{{ route('admin.candidates.show', $session->candidate) }}" class="btn-ghost btn-sm">← Candidato</a>
@endsection

@section('content')

@php
$candidate = $session->candidate;
$scenarios = [
    1 => [
        'title' => 'Escenario 1 — Queja recurrente con cliente frustrado',
        'dims'  => 'P2 + E1 + E2',
        'color' => 'rose',
        'bars'  => [
            5 => 'Apertura empática que valida el agotamiento y nombra el historial específico. Transparencia total sobre el backlog técnico sin excusas. Asume responsabilidad institucional, no personal. Ofrece un compromiso concreto y verificable (nombre, fecha, canal de seguimiento). Tono cálido y profesional durante toda la respuesta.',
            4 => 'Valida la frustración y presenta información honesta. El compromiso existe pero le falta precisión (fecha aproximada, no exacta). Algún momento suena defensivo o protocolar. No personaliza suficientemente la reapertura del caso.',
            3 => 'Muestra empatía básica y ofrece una solución genérica (reabrir el caso, escalar). No aborda el historial de promesas incumplidas. El tono es correcto pero impersonal. El compromiso final es vago.',
            2 => 'La respuesta es mayormente técnica o procedimental. Poca o ninguna validación emocional. No hay compromiso concreto o es claramente irrealizable. Puede sonar defensivo o echar la culpa al área técnica.',
            1 => 'Respuesta que ignora la dimensión emocional. Repite el mismo mensaje que ya le dieron antes. Sin compromiso real. Puede generar mayor escalamiento del conflicto.',
        ],
    ],
    2 => [
        'title' => 'Escenario 2 — Necesidad no expresada y solución creativa',
        'dims'  => 'P1 + A1 + E2',
        'color' => 'violet',
        'bars'  => [
            5 => 'Atiende a Ramón con calidez y paciencia desde el primer instante. Identifica que el trámite probable es sucesión o certificado de defunción para desbloqueo de cuenta. Le explica los pasos de forma simple. Coordina con un asesor para gestionar su caso con prioridad humanitaria. Acompaña el proceso o le da un punto de contacto claro. No lo deja solo en el sistema.',
            4 => 'Lo atiende bien y lo orienta sobre el trámite probable. Gestiona el turno con algo de prioridad. Puede faltar el acompañamiento o la confirmación de que fue bien recibido por el asesor correspondiente.',
            3 => 'Le da la información básica sobre el trámite, le asigna un turno y le explica la espera. No hay gestión especial de la situación humanitaria. Lo orienta correctamente pero sin ir más allá del protocolo.',
            2 => 'Le dice que necesita turno, le explica cómo pedirlo, y lo deja en la fila general. Poca consideración por la situación particular del cliente. Respuesta correcta pero sin criterio de empatía situacional.',
            1 => 'Lo remite al sistema de turnos sin más ayuda. No identifica las necesidades específicas del caso. No muestra sensibilidad ante la situación del cliente.',
        ],
    ],
    3 => [
        'title' => 'Escenario 3 — Presión extrema y error propio',
        'dims'  => 'A2 + P2 + E1',
        'color' => 'orange',
        'bars'  => [
            5 => 'Llama a Carlos de inmediato para corregir el error antes de atender a los clientes en espera (urgencia correctamente priorizada). Comunica el error con transparencia, sin excusas, explicando la consecuencia y la solución. Informa a los clientes en espera del breve retraso con una disculpa. Documenta el incidente. Reporta a su supervisor al salir. Mantiene la compostura durante todo el proceso.',
            4 => 'Llama a Carlos con rapidez y comunica el error con claridad. Gestiona a los clientes en espera aunque con algo de torpeza. El reporte al supervisor puede ser incompleto o tardío. La respuesta emocional es funcional aunque se nota el estrés.',
            3 => 'Atiende primero a los clientes en espera y luego llama a Carlos antes de salir. La comunicación del error es adecuada pero sin mucha profundidad. Documenta el caso de forma básica.',
            2 => 'Llama a Carlos pero el mensaje es confuso o demasiado disculpatorio, sin claridad sobre la solución. Descuida a los clientes en espera o los atiende con calidad reducida. No documenta ni reporta.',
            1 => 'No llama a Carlos o lo deja para el día siguiente. La presión del cierre del turno domina las decisiones. No hay criterio de priorización ni gestión del error.',
        ],
    ],
];

$colorMap = [
    'rose'   => ['bg-rose-50','border-rose-200','text-rose-700','bg-rose-600'],
    'violet' => ['bg-violet-50','border-violet-200','text-violet-700','bg-violet-600'],
    'orange' => ['bg-orange-50','border-orange-200','text-orange-700','bg-orange-600'],
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
                    <div><dt class="text-slate-400 text-xs">Cargo</dt><dd class="text-slate-700">{{ $candidate->position?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs">M1 SJT</dt><dd class="font-semibold text-slate-800">{{ $session->m1_score ?? '—' }} / 60</dd></div>
                    <div><dt class="text-slate-400 text-xs">M2 Actitudes</dt><dd class="font-semibold text-slate-800">{{ $session->m2_score ?? '—' }} / 150</dd></div>
                    <div><dt class="text-slate-400 text-xs">Enviado</dt><dd class="text-slate-600">{{ $session->m3_submitted_at?->format('d/m/Y H:i') }}</dd></div>
                </dl>
            </div>
        </div>
    </div>

    <div class="card border-amber-100 bg-amber-50/40">
        <div class="card-body">
            <h3 class="text-xs font-semibold text-amber-700 uppercase tracking-wider mb-2">Módulo 3 pendiente</h3>
            <p class="text-xs text-amber-700">Califique los 3 escenarios con la rúbrica BARS (1–5). Justifique cada puntaje. El sistema calculará el puntaje total y el perfil de desempeño automáticamente.</p>
            <div class="mt-3 pt-3 border-t border-amber-200 text-xs text-amber-600">
                <p>M3 máx: <strong>15 pts</strong> (3 escenarios × 5)</p>
                <p>Total máx: <strong>225 pts</strong></p>
            </div>
        </div>
    </div>
</div>

{{-- Aviso ético --}}
<div class="mb-5 p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-500">
    <strong class="text-slate-700">Uso exclusivo del evaluador.</strong> Las rúbricas BARS son confidenciales. La calificación debe basarse únicamente en los criterios objetivos descritos. Ley 1090 de 2006 · Ley 1581 de 2012.
</div>

<form action="{{ route('admin.tsc-sl.score.store', $session) }}" method="POST">
    @csrf

    @if($errors->any())
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
    @endif

    <div class="space-y-8">
        @foreach($scenarios as $num => $sc)
        @php $c = $colorMap[$sc['color']]; @endphp

        <div class="card border-slate-100" id="sc-{{ $num }}">
            {{-- Cabecera --}}
            <div class="px-5 py-3 {{ $c[0] }} border-b {{ $c[1] }} flex items-center justify-between flex-wrap gap-2">
                <div>
                    <span class="text-xs font-bold {{ $c[2] }} uppercase tracking-wider">{{ $sc['title'] }}</span>
                </div>
                <span class="text-[10px] font-semibold border {{ $c[1] }} {{ $c[2] }} rounded-full px-2 py-0.5">{{ $sc['dims'] }}</span>
            </div>

            <div class="p-5 space-y-5">
                {{-- Respuesta del candidato --}}
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Respuesta del candidato</p>
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-sm text-slate-700 leading-relaxed whitespace-pre-wrap max-h-48 overflow-y-auto">{{ $session->m3_responses[$num] ?? '—' }}</div>
                </div>

                {{-- Rúbrica BARS --}}
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Rúbrica BARS — Seleccione el nivel</p>
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
            <label class="form-label text-xs" for="observations">Observaciones generales del evaluador <span class="form-hint">(opcional)</span></label>
            <textarea name="observations" id="observations" rows="4"
                      class="textarea w-full text-sm"
                      placeholder="Observaciones clínicas adicionales, contexto situacional, alertas de evaluación...">{{ old('observations') }}</textarea>
        </div>
    </div>

    {{-- Guardar --}}
    <div class="mt-6 flex items-center justify-between gap-4 flex-wrap">
        <a href="{{ route('admin.candidates.show', $session->candidate) }}" class="btn-ghost btn-sm">Cancelar</a>
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Guardar calificación y calcular puntaje
        </button>
    </div>

</form>

@endsection
