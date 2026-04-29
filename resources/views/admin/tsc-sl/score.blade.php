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
        'title' => 'Escenario 1 — Socia fundadora con problemas acumulados',
        'dims'  => 'P2 + E1 + E2',
        'color' => 'rose',
        'bars'  => [
            5 => 'Apertura empática que valida el agotamiento y nombra el historial de inconvenientes específicos de la socia. Reconoce la trayectoria de 20 años con el club. Explica la situación del carné con transparencia y calidez, sin generar vergüenza. Ofrece renovación inmediata. Asume responsabilidad institucional por los problemas de la piscina y propone un compromiso concreto y verificable (escalamiento al área correspondiente, seguimiento con nombre propio y fecha). Tono cálido y profesional durante toda la respuesta.',
            4 => 'Valida la frustración y gestiona bien la situación del carné. El compromiso frente a los problemas recurrentes de la piscina existe pero es vago (fecha aproximada, sin nombre de responsable). Algún momento suena defensivo o muy protocolar. No personaliza suficientemente el trato con una socia de tanta antigüedad.',
            3 => 'Muestra empatía básica y ofrece soluciones genéricas (renovar el carné, escalar los problemas de la piscina). No aborda el impacto acumulado de tres fines de semana fallidos. El tono es correcto pero impersonal. El compromiso final es vago o no incluye un responsable claro.',
            2 => 'La respuesta es mayormente procedimental. Tramita el carné pero no aborda la frustración acumulada. No hay compromiso concreto frente a los problemas de la piscina, o es claramente irrealizable. Puede sonar defensivo o delegar sin acompañar.',
            1 => 'Respuesta que ignora la dimensión emocional de la socia. Se limita al trámite del carné sin reconocer el historial de problemas. Sin compromiso real frente a la situación de la piscina. Puede generar mayor escalamiento o pérdida de la socia.',
        ],
    ],
    2 => [
        'title' => 'Escenario 2 — Grupo sin registro de reserva para celebración',
        'dims'  => 'P1 + A1 + E2',
        'color' => 'violet',
        'bars'  => [
            5 => 'Recibe al grupo con calidez y sin hacerlos sentir culpables por la situación. Reconoce el error institucional (falta de registro) sin culpar a nadie. Busca activamente alternativas viables: otro espacio disponible, ajuste de horario, contacto insistente con el socio. Coordina con áreas internas (eventos, administración) para encontrar una solución antes de decir "no se puede". Acompaña al grupo durante la gestión y mantiene comunicación constante. La celebración logra llevarse a cabo de alguna forma.',
            4 => 'Recibe bien al grupo y busca alternativas. Logra coordinar con otra área o encontrar un espacio alternativo. Puede faltar fluidez en la comunicación con el grupo durante la espera, o el acompañamiento no es continuo. Intenta contactar al socio pero no con suficiente insistencia.',
            3 => 'Trata bien al grupo e informa la situación con honestidad. Ofrece esperar o intenta alguna alternativa básica. No coordina proactivamente con otras áreas ni busca soluciones creativas. La gestión es correcta pero limitada al protocolo.',
            2 => 'Informa al grupo que no hay registro y que no puede hacer nada hasta que llegue el socio. Poca iniciativa para buscar alternativas. No hay gestión con otras áreas. El grupo queda en incertidumbre sin acompañamiento.',
            1 => 'Le dice al grupo que sin reserva registrada no puede ayudarles y que deben esperar al socio. No hay iniciativa, coordinación ni empatía con la situación. La celebración corre serio riesgo de no llevarse a cabo.',
        ],
    ],
    3 => [
        'title' => 'Escenario 3 — Error de cobro al cierre del turno bajo presión',
        'dims'  => 'A2 + P2 + E1',
        'color' => 'orange',
        'bars'  => [
            5 => 'Prioriza correctamente: llama al señor Torres de inmediato antes de atender las mesas en espera (urgencia correctamente identificada, ya que el error puede afectar la relación con el socio). Comunica el error con transparencia, sin excusas, explicando la consecuencia y la corrección que se hará. Informa brevemente a las mesas en espera del retraso con una disculpa. Documenta el incidente. Deja registro para su compañero de turno. Reporta al supervisor antes de salir. Mantiene la compostura durante todo el proceso sin que el estrés altere el trato.',
            4 => 'Llama al señor Torres con rapidez y comunica el error con claridad. Gestiona las mesas en espera aunque con algo de torpeza o retraso. El traspaso a su compañero o el reporte al supervisor puede ser incompleto. La respuesta emocional es funcional aunque se nota el estrés del cierre de turno.',
            3 => 'Atiende primero a las mesas en espera y llama al señor Torres antes de salir. La comunicación del error es adecuada pero sin mucha precisión sobre la corrección. Deja registro básico. No documenta ni reporta con detalle.',
            2 => 'Llama al señor Torres pero el mensaje es confuso, excesivamente disculpatorio o no queda claro cómo se resolverá el cobro. Las mesas en espera son atendidas con calidad reducida o no son informadas del retraso. No hay registro ni reporte formal.',
            1 => 'No llama al señor Torres o lo deja para el día siguiente. La presión del cierre de turno domina todas las decisiones. No hay criterio de priorización. El error queda sin resolver al finalizar el turno.',
        ],
    ],
];

$colorMap = [
    'rose'   => ['bg-rose-50','border-rose-200','text-rose-700'],
    'violet' => ['bg-violet-50','border-violet-200','text-violet-700'],
    'orange' => ['bg-orange-50','border-orange-200','text-orange-700'],
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
                    <div><dt class="text-slate-400 text-xs">M2 — Actitudes (0–150)</dt><dd class="font-semibold text-slate-800">{{ $session->m2_score ?? '—' }} pts</dd></div>
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
                <p>Puntaje total máximo: <strong>225 pts</strong></p>
            </div>
        </div>
    </div>
</div>

{{-- Aviso ético --}}
<div class="mb-5 p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-500">
    <strong class="text-slate-700">Uso exclusivo del evaluador.</strong> Las rúbricas BARS son confidenciales. La calificación debe basarse únicamente en los criterios conductuales descritos. Ley 1090 de 2006 — Código Deontológico del Psicólogo · Ley 1581 de 2012.
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
                      placeholder="Observaciones adicionales sobre el perfil del candidato, alertas de evaluación, contexto situacional...">{{ old('observations') }}</textarea>
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
