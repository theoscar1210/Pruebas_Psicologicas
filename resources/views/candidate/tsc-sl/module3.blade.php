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
        <p class="text-sm text-slate-500 mt-1">Para cada situación, redacte su respuesta de la forma más completa y detallada posible. No hay extensión mínima — lo que importa es la calidad de su razonamiento y la respuesta propuesta.</p>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-xs text-amber-800">
        <strong>Importante:</strong> Una vez que envíe este módulo, no podrá modificar sus respuestas. Tómese el tiempo necesario para elaborar cada respuesta antes de enviar.
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
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">Escenario 1</span>
                        <span class="text-[10px] text-rose-500 border border-rose-200 rounded-full px-2 py-0.5">Manejo de clientes difíciles · Empatía · Comunicación</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">
                        Usted trabaja en el área de atención al cliente de una empresa de telecomunicaciones. <strong>Laura Gómez</strong> la llama por quinta vez en el mismo mes. En llamadas anteriores se le prometió que su servicio de internet sería estabilizado en máximo 72 horas. Ya pasaron dos semanas y el problema persiste. Laura está al límite: su voz transmite agotamiento y frustración contenida. Dice: <em>"Ya no sé qué más hacer. Cada persona que me atiende me da una respuesta diferente. Siento que a nadie le importa realmente resolver esto."</em> Su sistema muestra que hay 4 tickets abiertos por el mismo problema, todos cerrados sin resolución documentada, y que el área técnica tiene un backlog de 10 días para su zona.
                    </p>
                    <p class="text-xs font-semibold text-slate-500 mb-2">Su respuesta:</p>
                    <p class="text-xs text-slate-400 mb-3">Redacte la respuesta completa que le daría a Laura en esa llamada. Incluya cómo abre la conversación, cómo maneja su frustración, qué información le transmite sobre el estado real de su caso y qué compromiso concreto le ofrece.</p>
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
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-violet-700 uppercase tracking-wider">Escenario 2</span>
                        <span class="text-[10px] text-violet-500 border border-violet-200 rounded-full px-2 py-0.5">Resolución de problemas · Proactividad · Comunicación</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">
                        Usted trabaja en una sucursal bancaria. <strong>Ramón Torres</strong>, un señor de 68 años, llega visiblemente desorientado. Pregunta por <em>"el trámite para el dinero de mi esposa que falleció"</em>. No trae ningún papel, no sabe exactamente qué trámite necesita, y cuando usted le pregunta si tiene turno, él le dice que "no sabía que tenía que pedir turno". La sucursal tiene mucha gente, hay 40 minutos de espera para cualquier asesor, y usted está encargado del módulo de información, no de trámites.
                    </p>
                    <p class="text-xs font-semibold text-slate-500 mb-2">Su respuesta:</p>
                    <p class="text-xs text-slate-400 mb-3">Describa paso a paso qué haría usted desde el momento en que Ramón se acerca a su módulo. Incluya cómo lo orienta, qué información le solicita, cómo gestiona la situación con el sistema de turnos y qué hace para asegurarse de que reciba la atención correcta.</p>
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
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-orange-700 uppercase tracking-wider">Escenario 3</span>
                        <span class="text-[10px] text-orange-500 border border-orange-200 rounded-full px-2 py-0.5">Regulación emocional · Clientes difíciles · Empatía</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">
                        Son las 5:45 pm del último día hábil del mes. Su turno termina a las 6:00 pm. El sistema lleva 30 minutos lento. Usted acaba de darle a un cliente (<strong>Carlos Medina</strong>) información errónea sobre una fecha de vencimiento de su póliza: le dijo que vencía el 30, cuando en realidad vence el 25 de este mes — es decir, <strong>en 5 días</strong>. Carlos ya salió de la sucursal. Usted tiene el teléfono de Carlos porque lo registró en el sistema. Si no renueva en 5 días, perderá la cobertura. Además, hay dos clientes esperando ser atendidos y su supervisor no está disponible.
                    </p>
                    <p class="text-xs font-semibold text-slate-500 mb-2">Su respuesta:</p>
                    <p class="text-xs text-slate-400 mb-3">Describa exactamente qué haría en los próximos 15 minutos. ¿Cómo maneja los dos clientes en espera? ¿Cómo resuelve el error con Carlos? ¿Cómo gestiona su propio estado emocional ante el error y la presión?</p>
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
                <button type="button" id="btn-confirm"
                        class="btn-primary btn-sm flex-shrink-0">
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

<script>
(function() {
    // Contadores de caracteres
    [1,2,3].forEach(n => {
        const ta = document.getElementById(`sc${n}`);
        const cnt = document.getElementById(`cnt${n}`);
        if (ta && cnt) {
            cnt.textContent = ta.value.length;
            ta.addEventListener('input', () => cnt.textContent = ta.value.length);
        }
    });

    // Modal de confirmación
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
