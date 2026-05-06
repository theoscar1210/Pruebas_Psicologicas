@extends('layouts.candidate')

@section('title', 'TSC-SL — Módulo 3')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">

    {{-- Progreso --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-brand-700">Módulo 3 de 3 — Escenarios de respuesta abierta</span>
            <span class="text-xs text-slate-400">3 escenarios</span>
        </div>
        <div class="progress-track h-2">
            <div class="progress-bar bg-brand-600" style="width: 90%"></div>
        </div>
    </div>

    <div class="mb-5">
        <h1 class="text-lg font-bold text-slate-900">Módulo 3: Escenarios Abiertos</h1>
        <p class="text-sm text-slate-500 mt-1">Para cada situación, describa en detalle cómo actuaría usted. No hay extensión mínima — lo que importa es la calidad de su razonamiento y la coherencia de su respuesta.</p>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-xs text-amber-800">
        <strong>Importante:</strong> Una vez que envíe este módulo, sus respuestas no podrán modificarse. Tómese el tiempo necesario para elaborar cada respuesta antes de enviar.
    </div>

    <form action="{{ route('candidate.tsc-sl.module3.store', $assignment) }}" method="POST" id="form-m3">
        @csrf

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <div class="space-y-6">

            {{-- Escenario 1 --}}
            <div class="card border-slate-100">
                <div class="px-5 py-3 bg-rose-50 border-b border-rose-200">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">Escenario 1</span>
                        <span class="text-[10px] text-rose-500 border border-rose-200 rounded-full px-2 py-0.5">Manejo de socia difícil · Empatía · Comunicación</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">
                        La señora <strong>Inés Castaño</strong>, socia fundadora del club, llega a recepción visiblemente alterada. Le explica que en los últimos tres fines de semana ha llegado con sus nietos y la piscina olímpica siempre ha tenido un problema diferente: la primera vez estaba en mantenimiento no programado, la segunda el salvavidas no estaba en su puesto, y el pasado fin de semana el agua estaba fría. Hoy también hay un inconveniente: el sistema de acceso no reconoce su carné y lleva 15 minutos esperando. Dice: <em>"Llevo 20 años siendo socia de este club y nunca había vivido algo así. Ya no sé para qué pago una cuota tan alta."</em> Al revisar el sistema, usted encuentra que el carné venció hace dos días y necesita renovación, proceso que puede hacerse en el momento si la socia tiene su documento de identidad.
                    </p>
                    <p class="text-xs font-semibold text-slate-500 mb-2">Su respuesta:</p>
                    <p class="text-xs text-slate-400 mb-3">Describa cómo abre la conversación con la señora Castaño, cómo maneja su frustración acumulada, cómo le explica la situación del carné y qué compromiso concreto le ofrece respecto a los problemas recurrentes con la piscina.</p>
                    <textarea name="m3[1]"
                              rows="7"
                              class="textarea w-full text-sm {{ $errors->has('m3.1') ? 'border-red-400' : '' }}"
                              placeholder="Escriba aquí su respuesta detallada..."
                              id="sc1">{{ old('m3.1') }}</textarea>
                    <div class="flex justify-between mt-1.5">
                        <p class="text-xs text-red-600">{{ $errors->first('m3.1') }}</p>
                        <p class="text-xs text-slate-400"><span id="cnt1">0</span> caracteres</p>
                    </div>
                </div>
            </div>

            {{-- Escenario 2 --}}
            <div class="card border-slate-100">
                <div class="px-5 py-3 bg-violet-50 border-b border-violet-200">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-violet-700 uppercase tracking-wider">Escenario 2</span>
                        <span class="text-[10px] text-violet-500 border border-violet-200 rounded-full px-2 py-0.5">Resolución de problemas · Proactividad · Comunicación</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">
                        Son las 4:00 pm del sábado. Llega al club un grupo de 12 personas invitadas por el socio <strong>Mauricio Ríos</strong> para celebrar su cumpleaños. Traen torta, decoración y un pequeño equipo de sonido. Dicen haber reservado el salón social a través de alguien del área de eventos, pero esa persona no está hoy y no dejó registro escrito de ninguna reserva. El salón social está siendo usado por otro evento hasta las 7:00 pm. Usted está de turno en recepción. El socio Ríos aún no ha llegado y no contesta el teléfono.
                    </p>
                    <p class="text-xs font-semibold text-slate-500 mb-2">Su respuesta:</p>
                    <p class="text-xs text-slate-400 mb-3">Describa paso a paso qué haría desde el momento en que el grupo llega. ¿Cómo los recibe? ¿Qué alternativas busca? ¿Cómo gestiona la comunicación con el grupo y con el socio? ¿Qué hace para asegurarse de que la celebración pueda llevarse a cabo de la mejor manera posible?</p>
                    <textarea name="m3[2]"
                              rows="7"
                              class="textarea w-full text-sm {{ $errors->has('m3.2') ? 'border-red-400' : '' }}"
                              placeholder="Escriba aquí su respuesta detallada..."
                              id="sc2">{{ old('m3.2') }}</textarea>
                    <div class="flex justify-between mt-1.5">
                        <p class="text-xs text-red-600">{{ $errors->first('m3.2') }}</p>
                        <p class="text-xs text-slate-400"><span id="cnt2">0</span> caracteres</p>
                    </div>
                </div>
            </div>

            {{-- Escenario 3 --}}
            <div class="card border-slate-100">
                <div class="px-5 py-3 bg-orange-50 border-b border-orange-200">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-orange-700 uppercase tracking-wider">Escenario 3</span>
                        <span class="text-[10px] text-orange-500 border border-orange-200 rounded-full px-2 py-0.5">Regulación emocional · Clientes difíciles · Empatía</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">
                        Son las 7:40 pm del viernes. Su turno en el bar del club termina a las 8:00 pm. El sistema de punto de venta falló hace 20 minutos y ha estado atendiendo los consumos de forma manual. Acaba de cobrarle al socio <strong>Rafael Torres</strong> un total de $85.000 cuando el monto correcto era $58.000. El señor Torres ya se fue con su familia. Usted tiene su número de contacto porque lo registró al inicio de la noche. Si no se corrige, el cobro incorrecto aparecerá en el estado de cuenta mensual del socio. Además, hay dos mesas de invitados esperando que alguien les tome el pedido, y su compañero del turno de la noche llega a las 8:00 pm exactas.
                    </p>
                    <p class="text-xs font-semibold text-slate-500 mb-2">Su respuesta:</p>
                    <p class="text-xs text-slate-400 mb-3">Describa exactamente qué haría en los próximos 20 minutos. ¿Cómo prioriza las situaciones? ¿Cómo contacta al señor Torres y qué le dice? ¿Cómo gestiona a las mesas en espera? ¿Cómo maneja su propio estado emocional ante el error cometido y la presión del cierre de turno?</p>
                    <textarea name="m3[3]"
                              rows="7"
                              class="textarea w-full text-sm {{ $errors->has('m3.3') ? 'border-red-400' : '' }}"
                              placeholder="Escriba aquí su respuesta detallada..."
                              id="sc3">{{ old('m3.3') }}</textarea>
                    <div class="flex justify-between mt-1.5">
                        <p class="text-xs text-red-600">{{ $errors->first('m3.3') }}</p>
                        <p class="text-xs text-slate-400"><span id="cnt3">0</span> caracteres</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Botón enviar --}}
        <div class="sticky bottom-4 mt-6">
            <div class="bg-white border border-slate-200 rounded-xl shadow-lg p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <p class="text-xs text-slate-500">Al enviar, sus respuestas quedarán registradas y no podrán modificarse.</p>
                <button type="button" id="btn-confirm" class="btn-primary btn-sm flex-shrink-0">
                    Enviar y finalizar
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </div>
        </div>

    </form>

    {{-- Modal de confirmación --}}
    <div id="modal-confirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full mx-4 p-6">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 text-center mb-2">¿Confirma el envío?</h3>
            <p class="text-sm text-slate-500 text-center mb-5">Una vez enviadas, sus respuestas no podrán modificarse. ¿Está seguro de que desea finalizar la prueba?</p>
            <div class="flex gap-3">
                <button type="button" id="btn-cancel-modal" class="btn-ghost flex-1 justify-center">Revisar</button>
                <button type="button" id="btn-confirm-submit" class="btn-primary flex-1 justify-center">Sí, enviar</button>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ app('csp-nonce') }}">
(function() {
    [1,2,3].forEach(n => {
        const ta = document.getElementById(`sc${n}`);
        const cnt = document.getElementById(`cnt${n}`);
        if (ta && cnt) {
            cnt.textContent = ta.value.length;
            ta.addEventListener('input', () => cnt.textContent = ta.value.length);
        }
    });

    const modal = document.getElementById('modal-confirm');
    document.getElementById('btn-confirm').addEventListener('click', () => modal.classList.remove('hidden'));
    document.getElementById('btn-cancel-modal').addEventListener('click', () => modal.classList.add('hidden'));
    document.getElementById('btn-confirm-submit').addEventListener('click', () => {
        document.getElementById('form-m3').submit();
    });
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
})();
</script>
@endsection
