@extends('layouts.candidate')
@section('title', $assignment->test->name . ' — Instrucciones')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">

    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $assignment->test->name }}</h1>
        <p class="text-slate-500 text-sm mt-1">Lea atentamente las instrucciones antes de comenzar.</p>
    </div>

    {{-- Info del test --}}
    <div class="grid grid-cols-2 gap-3 mb-6 sm:grid-cols-3">
        @if($assignment->test->questions_count)
        <div class="card border-slate-100 text-center py-3">
            <p class="text-xl font-bold text-brand-700">{{ $assignment->test->questions_count }}</p>
            <p class="text-xs text-slate-500 mt-0.5">Ítems</p>
        </div>
        @endif
        @if($assignment->test->time_limit)
        <div class="card border-slate-100 text-center py-3">
            <p class="text-xl font-bold text-brand-700">{{ $assignment->test->time_limit }}</p>
            <p class="text-xs text-slate-500 mt-0.5">min aprox.</p>
        </div>
        @endif
        <div class="card border-slate-100 text-center py-3">
            <p class="text-xl font-bold text-brand-700">1</p>
            <p class="text-xs text-slate-500 mt-0.5">Sesión</p>
        </div>
    </div>

    {{-- Instrucciones específicas del test --}}
    @if($assignment->test->instructions)
    <div class="card mb-5">
        <div class="card-body">
            <h2 class="text-sm font-semibold text-slate-800 mb-3">Instrucciones</h2>
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $assignment->test->instructions }}</p>
        </div>
    </div>
    @else
    <div class="card mb-5">
        <div class="card-body">
            <h2 class="text-sm font-semibold text-slate-800 mb-3">Instrucciones generales</h2>
            <div class="space-y-2 text-sm text-slate-700 mt-2 pt-3 border-t border-slate-100">
                <p class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Responda todos los ítems sin dejar ninguno en blanco.
                </p>
                <p class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Asegúrese de tener conexión estable y tiempo suficiente antes de iniciar.
                </p>
                <p class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Sus respuestas se guardan automáticamente. Si cierra la ventana puede retomar desde donde la dejó.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Consentimiento informado --}}
    <div class="card border-slate-200 mb-6">
        <div class="card-body">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Autorización de tratamiento de datos personales</h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                De conformidad con la <strong>Ley 1581 de 2012</strong> y el <strong>Decreto 1377 de 2013</strong>, le informamos que <strong>{{ config('app.name') }}</strong> (Responsable del tratamiento) recopilará sus datos personales — nombre, documento de identidad, correo, teléfono, cargo aspirado y respuestas a las pruebas psicológicas — con la finalidad de <strong>evaluar sus competencias en el marco del proceso de selección de personal</strong>. Sus datos serán compartidos únicamente con el psicólogo evaluador y el área de Recursos Humanos del empleador, y se conservarán durante la vigencia del proceso y hasta por <strong>2 años</strong> después de su finalización.
            </p>
            <p class="text-xs text-slate-600 leading-relaxed mt-2">
                Conforme al <strong>Código Deontológico del Psicólogo — Ley 1090 de 2006</strong>, los resultados son confidenciales e interpretados exclusivamente por un profesional autorizado. La participación es <strong>voluntaria</strong>. Como titular usted tiene derecho a <strong>conocer, actualizar, rectificar y suprimir</strong> sus datos, y a <strong>revocar esta autorización</strong> en cualquier momento escribiendo a <strong>{{ config('mail.from.address', 'contacto@menteclara.co') }}</strong>.
            </p>
            <div class="mt-3 flex items-start gap-2">
                <input type="checkbox" id="consent" class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <label for="consent" class="text-xs text-slate-700 leading-relaxed">
                    <strong>Autorizo</strong> expresamente el tratamiento de mis datos personales para el proceso de selección, conforme a la Ley 1581 de 2012 y los términos indicados. Puedo consultar la <a href="{{ route('privacy') }}" target="_blank" class="underline text-brand-600 hover:text-brand-800">Política de Privacidad completa</a> en cualquier momento.
                </label>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('candidate.consent.store', $assignment) }}" id="form-consent">
        @csrf
        <button id="btn-start" type="submit" disabled
                class="btn-primary w-full justify-center text-base py-3 opacity-40 cursor-not-allowed">
            Comenzar prueba
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </form>

    @if($assignment->test->time_limit)
    <p class="text-center text-xs text-slate-400 mt-4">Tiempo límite: {{ $assignment->test->time_limit }} minutos</p>
    @else
    <p class="text-center text-xs text-slate-400 mt-4">Sin límite de tiempo</p>
    @endif

</div>

<script>
document.getElementById('consent').addEventListener('change', function() {
    const btn = document.getElementById('btn-start');
    btn.disabled = !this.checked;
    btn.classList.toggle('opacity-40', !this.checked);
    btn.classList.toggle('cursor-not-allowed', !this.checked);
});
</script>
@endsection
