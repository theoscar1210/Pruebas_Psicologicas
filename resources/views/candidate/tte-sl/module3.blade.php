@extends('layouts.candidate')

@section('title', 'TTE-SL — Módulo 3')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">

    {{-- Progreso --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-brand-700">Módulo 3 de 3 — Escenarios grupales de respuesta abierta</span>
            <span class="text-xs text-slate-400">3 escenarios</span>
        </div>
        <div class="progress-track h-2">
            <div class="progress-bar bg-brand-600" style="width: 90%"></div>
        </div>
    </div>

    <div class="mb-5">
        <h1 class="text-lg font-bold text-slate-900">Módulo 3: Escenarios Grupales</h1>
        <p class="text-sm text-slate-500 mt-1">Para cada situación, describa en detalle cómo actuaría usted. Lo que importa es la calidad de su razonamiento, cómo gestiona las dinámicas grupales y cómo equilibra el resultado del equipo con las relaciones interpersonales.</p>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-xs text-amber-800">
        <strong>Importante:</strong> Una vez que envíe este módulo, sus respuestas no podrán modificarse. Tómese el tiempo necesario para elaborar cada respuesta antes de enviar.
    </div>

    <form action="{{ route('candidate.tte-sl.module3.store', $assignment) }}" method="POST" id="form-m3">
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
                        <span class="text-[10px] text-rose-500 border border-rose-200 rounded-full px-2 py-0.5">Gestión del conflicto · Responsabilidad · Objetivo colectivo</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">
                        Su equipo de 5 personas debe entregar un informe ejecutivo mañana a las 9:00 am. Son las 6:00 pm. Al revisar el documento final, usted descubre que la sección de análisis financiero tiene errores graves. Esa sección la elaboró <strong>Camila</strong>, quien ya salió de la oficina. Además, descubre que <strong>Felipe</strong>, encargado de la revisión final, no la revisó — algo que sí era su responsabilidad. El equipo sabe de la entrega pero nadie más ha detectado el error. Usted tiene el conocimiento para corregirlo, pero tomaría al menos 2–3 horas y no era su tarea.
                    </p>
                    <p class="text-xs font-semibold text-slate-500 mb-2">Su respuesta:</p>
                    <p class="text-xs text-slate-400 mb-3">Describa exactamente qué haría desde este momento. ¿Cómo gestiona el error? ¿Cómo involucra a Camila y a Felipe? ¿Cómo maneja su propio rol ante algo que no era su responsabilidad directa?</p>
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
                <div class="px-5 py-3 bg-sky-50 border-b border-sky-200">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-sky-700 uppercase tracking-wider">Escenario 2</span>
                        <span class="text-[10px] text-sky-500 border border-sky-200 rounded-full px-2 py-0.5">Escucha activa · Adaptabilidad · Apoyo interpersonal</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">
                        <strong>Andrés</strong> lleva 3 semanas en el equipo. Es muy técnico y detallista, pero tiene un estilo de comunicación muy directo que varios colegas perciben como frío o incluso agresivo. En una reunión de ayer, Andrés interrumpió a dos personas para señalar errores en sus argumentos. Las interrupciones fueron factualmente correctas, pero el tono generó incomodidad visible. El líder del equipo está viajando esta semana. Usted tiene buena relación con Andrés y también con los colegas afectados.
                    </p>
                    <p class="text-xs font-semibold text-slate-500 mb-2">Su respuesta:</p>
                    <p class="text-xs text-slate-400 mb-3">Explique paso a paso qué haría. ¿Hablaría con alguien? ¿Con quién primero? ¿Qué le diría a Andrés? ¿Cómo equilibraría defender a los colegas afectados con integrar a Andrés sin excluirlo?</p>
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
                <div class="px-5 py-3 bg-amber-50 border-b border-amber-200">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Escenario 3</span>
                        <span class="text-[10px] text-amber-500 border border-amber-200 rounded-full px-2 py-0.5">Orientación al objetivo · Responsabilidad · Participación activa</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">
                        Su equipo debatió durante dos semanas la metodología para un proyecto importante. Usted defendió con datos el <strong>Enfoque A</strong>, pero la decisión final del equipo fue el <strong>Enfoque B</strong>, apoyado por la mayoría. Usted cree honestamente que el Enfoque B tendrá problemas en la fase de implementación. Tres semanas después, están en medio de la implementación y efectivamente surgen los problemas que usted anticipó. El proyecto está en riesgo. El líder convoca una reunión de emergencia para identificar soluciones.
                    </p>
                    <p class="text-xs font-semibold text-slate-500 mb-2">Su respuesta:</p>
                    <p class="text-xs text-slate-400 mb-3">¿Cómo actúa en esa reunión de emergencia? ¿Menciona que lo advirtió? ¿Cómo prioriza entre defender su criterio original y contribuir a salvar el proyecto? Describa qué haría y qué no haría.</p>
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

<script>
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
