@extends('layouts.admin')

@section('title', 'TSC-SL Hospitalidad — Calificación M3')
@section('header', 'TSC-SL Hospitalidad · Calificar Módulo 3')

@section('header-actions')
    <a href="{{ route('admin.candidates.show', $session->candidate) }}" class="btn-ghost btn-sm">← Candidato</a>
@endsection

@section('content')

@php
$candidate = $session->candidate;
$scenarios = [
    1 => [
        'title' => 'Escenario 1 — Pareja de aniversario: filete mal cocido y teatro a las 9 pm',
        'dims'  => 'P2 + E1 + E2',
        'color' => 'rose',
        'bars'  => [
            5 => 'Reconoce el peso emocional del aniversario desde las primeras palabras. Valida sinceramente la frustración de la señora sin ponerse defensivo. Comunica la opción de rehacer el filete (12 min) con honestidad, y ajusta la propuesta al contexto real del teatro: ofrece una alternativa concreta que funcione dado el tiempo disponible (ej. plato más rápido, posponer postre, ajustar tiempos de cocina). Cierra la interacción con un gesto compensatorio que repara la noche (copa de cortesía, postre de obsequio, mensaje personalizado). El tono es cálido, profesional y centrado en el cliente durante toda la respuesta.',
            4 => 'Maneja bien la dimensión emocional y resuelve el error del filete. Menciona el teatro y ofrece alguna alternativa, pero esta no está perfectamente ajustada al límite de tiempo. La propuesta existe pero tiene detalles poco claros o el gesto compensatorio es genérico. El cierre emocional con la pareja podría ser más sólido.',
            3 => 'Se disculpa y ofrece reemplazar el filete, comunica el tiempo de espera. Menciona el teatro pero no adapta la solución a ese contexto. La empatía es presente pero básica. Sin gesto compensatorio proactivo ni personalización para una ocasión especial.',
            2 => 'Se enfoca en el error del filete pero no aborda el peso emocional de la ocasión. Comunica el tiempo de espera sin ofrecer alternativas. La frustración de la señora ("nos está arruinando la noche") es reconocida mínimamente. Poca consideración del teatro como variable determinante.',
            1 => 'Respuesta limitada al reemplazo del plato. No aborda el contexto emocional del aniversario. Sin conciencia del límite de tiempo del teatro. El enfoque es procedimental y puede dejar a la pareja sin sentirse valorada.',
        ],
    ],
    2 => [
        'title' => 'Escenario 2 — Evento corporativo: plato principal no disponible por falla del proveedor',
        'dims'  => 'P1 + A1 + E2',
        'color' => 'violet',
        'bars'  => [
            5 => 'Aborda al señor Peña de forma discreta y oportuna — antes de que el retraso se haga evidente — sin crear alarma en la mesa. Encuadra la situación con honestidad y sin culpar a nadie. Presenta ambas alternativas con descripción concreta (sabor, presentación, tiempo de cocción). Pregunta proactivamente por restricciones alimentarias en el grupo antes de confirmar la elección. Gestiona con cocina para que el plato alternativo sea servido con la misma calidad y puntualidad proyectada. El evento transcurre normalmente desde la perspectiva de los invitados.',
            4 => 'Informa al señor Peña con rapidez y presenta las alternativas con claridad. Puede no indagar proactivamente por restricciones alimentarias en todos los comensales. La coordinación con cocina es adecuada pero no completamente invisible para el grupo. El organizador queda satisfecho aunque percibe alguna fricción en la transición.',
            3 => 'Informa del problema y presenta las alternativas. La comunicación es correcta pero algo torpe o sin suficiente detalle sobre cada opción. No aborda las restricciones alimentarias a menos que se le pregunte directamente. La transición al plato alternativo tiene alguna fricción visible.',
            2 => 'Informa al señor Peña pero es excesivamente disculpatorio sin tomar el mando de la situación. Las alternativas se presentan sin detalle persuasivo. No gestiona las restricciones alimentarias. El organizador debe guiar parcialmente la resolución del problema.',
            1 => 'Demora en informar o lo hace de una forma que genera alarma en la mesa. No presenta alternativas claras o deja la decisión totalmente sin orientación. La transición al plato alternativo es desorganizada y los invitados notan el problema.',
        ],
    ],
    3 => [
        'title' => 'Escenario 3 — Error de cobro al cierre de turno: grupo saliendo + mesas en espera',
        'dims'  => 'A2 + P2 + E1',
        'color' => 'orange',
        'bars'  => [
            5 => 'Identifica correctamente la prioridad: abordar al grupo antes de que salga, por encima de atender las mesas en espera (el error de cobro tiene mayor urgencia y menor margen de acción). Se acerca al grupo con compostura, reconoce el error con transparencia y sin excusas, explica cómo se procesará la corrección (nota crédito, reverso del cargo, tiempo estimado). Informa brevemente a las mesas en espera con una disculpa. Documenta el incidente y hace un traspaso completo al compañero de relevo con el supervisor informado. Mantiene la calma en todas las interacciones sin que el estrés altere el trato.',
            4 => 'Aborda al grupo con rapidez y comunica el error con claridad. El proceso de corrección es explicado pero quizás sin precisión total sobre tiempos o mecanismos. Las mesas en espera son gestionadas aunque alguna no recibe la información completa. El traspaso al relevo es mayormente correcto. Estrés visible pero no traslada al cliente.',
            3 => 'Alcanza al grupo a tiempo e informa el error. La comunicación es adecuada pero no explica con claridad cómo se procesará la corrección. Las mesas en espera son atendidas con calidad aceptable. El traspaso al relevo es básico. El estrés es manejado aunque perceptible.',
            2 => 'Aborda al grupo pero el mensaje sobre el error es confuso o excesivamente disculpatorio sin una ruta de resolución clara. Las mesas en espera son descuidadas o informadas muy brevemente. El traspaso al relevo es incompleto. El estrés afecta la calidad de al menos una interacción.',
            1 => 'No logra alcanzar al grupo antes de que se vaya, o comunica el error tan mal que la situación escala. Las mesas en espera no son gestionadas. Sin traspaso adecuado al relevo. El error queda sin resolver al terminar el turno.',
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
            <p class="text-xs text-amber-700">Califique los 3 escenarios de hospitalidad con la rúbrica BARS (1–5). Justifique cada puntaje. El sistema calculará automáticamente el puntaje total y el perfil de desempeño.</p>
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

<form action="{{ route('admin.tsc-sl-h.score.store', $session) }}" method="POST">
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
