@extends('layouts.candidate')

@section('title', 'TSC-SL — Instrucciones')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">

    {{-- Encabezado --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-semibold text-teal-600 bg-teal-50 border border-teal-200 rounded-full px-2.5 py-0.5">Test de Servicio al Cliente</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">TSC-SL</h1>
        <p class="text-slate-500 text-sm mt-1">Lea atentamente las instrucciones antes de comenzar.</p>
    </div>

    {{-- Info del test --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="card border-slate-100 text-center py-3">
            <p class="text-xl font-bold text-brand-700">3</p>
            <p class="text-xs text-slate-500 mt-0.5">Módulos</p>
        </div>
        <div class="card border-slate-100 text-center py-3">
            <p class="text-xl font-bold text-brand-700">60</p>
            <p class="text-xs text-slate-500 mt-0.5">Ítems + 3 escenarios</p>
        </div>
        <div class="card border-slate-100 text-center py-3">
            <p class="text-xl font-bold text-brand-700">50</p>
            <p class="text-xs text-slate-500 mt-0.5">min aprox.</p>
        </div>
    </div>

    {{-- Instrucciones --}}
    <div class="card mb-5">
        <div class="card-body">
            <h2 class="text-sm font-semibold text-slate-800 mb-4">Instrucciones generales</h2>
            <div class="space-y-3 text-sm text-slate-700">
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                    <p><strong>Módulo 1 — Juicio Situacional (20 ítems):</strong> Situaciones reales de servicio. Seleccione la opción que mejor refleje cómo actuaría usted. No hay respuestas correctas o incorrectas aparentes — lo que importa es su criterio.</p>
                </div>
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                    <p><strong>Módulo 2 — Actitudes (40 ítems):</strong> Afirmaciones sobre su forma de trabajar. Indique qué tan de acuerdo está con cada una en una escala del 1 al 5. Responda con honestidad.</p>
                </div>
                <div class="flex gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    <p><strong>Módulo 3 — Escenarios (3 situaciones):</strong> Describa en detalle cómo respondería ante cada situación. No hay extensión mínima — lo que importa es la calidad del razonamiento.</p>
                </div>
            </div>

            <div class="mt-5 pt-4 border-t border-slate-100 space-y-2 text-sm text-slate-600">
                <p class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Cada módulo debe completarse en una sola sesión antes de pasar al siguiente.
                </p>
                <p class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Asegúrese de tener conexión estable y tiempo suficiente antes de iniciar cada módulo.
                </p>
                <p class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Si cierra la ventana, puede retomar la prueba desde donde la dejó.
                </p>
            </div>
        </div>
    </div>

    {{-- Consentimiento --}}
    <div class="card border-slate-200 mb-6">
        <div class="card-body">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Consentimiento informado</h2>
            <p class="text-xs text-slate-600 leading-relaxed">
                Sus respuestas serán tratadas con estricta confidencialidad, utilizadas exclusivamente para el proceso de selección de personal y protegidas conforme a la <strong>Ley 1581 de 2012</strong> (Habeas Data) y el <strong>Código Deontológico del Psicólogo — Ley 1090 de 2006</strong>. La participación es voluntaria. Tiene derecho a solicitar retroalimentación orientada al desarrollo al finalizar el proceso.
            </p>
            <div class="mt-3 flex items-start gap-2">
                <input type="checkbox" id="consent" class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <label for="consent" class="text-xs text-slate-700 leading-relaxed">
                    He leído y acepto que mis respuestas sean utilizadas para el proceso de selección bajo los términos indicados.
                </label>
            </div>
        </div>
    </div>

    {{-- Botón inicio --}}
    <a id="btn-start"
       href="{{ route('candidate.tsc-sl.module1', $assignment) }}"
       class="btn-primary w-full justify-center text-base py-3 opacity-40 pointer-events-none"
       aria-disabled="true">
        Comenzar — Módulo 1
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

    <p class="text-center text-xs text-slate-400 mt-4">Módulo 1 de 3 · Juicio Situacional · ≈ 15–20 min</p>
</div>

<script>
document.getElementById('consent').addEventListener('change', function() {
    const btn = document.getElementById('btn-start');
    if (this.checked) {
        btn.classList.remove('opacity-40','pointer-events-none');
        btn.removeAttribute('aria-disabled');
    } else {
        btn.classList.add('opacity-40','pointer-events-none');
        btn.setAttribute('aria-disabled','true');
    }
});
</script>
@endsection
