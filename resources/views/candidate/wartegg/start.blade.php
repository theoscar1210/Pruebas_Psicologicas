@extends('layouts.candidate')
@section('title', 'Test de Wartegg — Instrucciones')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-violet-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Test de Wartegg</h1>
        <p class="text-slate-500 text-sm mt-1">Wartegg Zeichen Test · Versión para Selección Laboral</p>
    </div>

    {{-- Aviso ético --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <p class="text-sm text-amber-800 leading-relaxed">
            Esta es una prueba proyectiva. Los resultados son confidenciales y serán interpretados
            exclusivamente por un psicólogo organizacional certificado.
            No hay respuestas correctas ni incorrectas.
        </p>
    </div>

    {{-- Instrucciones --}}
    <div class="card mb-6">
        <div class="card-body space-y-4">
            <h2 class="font-semibold text-slate-800 text-sm uppercase tracking-wider text-slate-400">Instrucciones</h2>

            <div class="space-y-3">
                @foreach([
                    ['Verás 8 recuadros, cada uno con una señal o trazo en su interior.', '1'],
                    ['Completa cada recuadro haciendo un dibujo a partir de esa señal. Puedes dibujar lo que desees.', '2'],
                    ['Cuando termines cada dibujo, escribe un título breve.', '3'],
                    ['Puedes hacer los dibujos en el orden que prefieras. Cuando completes los 8 podrás finalizar.', '4'],
                    ['Usa el lápiz digital. Tienes borrador y botón de deshacer disponibles.', '5'],
                ] as [$text, $num])
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-violet-100 text-violet-700 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">{{ $num }}</span>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $text }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Vista previa de las 8 cajas --}}
    <div class="card mb-8">
        <div class="card-body">
            <p class="text-xs text-slate-500 font-medium mb-3 uppercase tracking-wider">Los 8 campos que completarás</p>
            <div class="grid grid-cols-4 gap-2">
                @foreach(['I','II','III','IV','V','VI','VII','VIII'] as $label)
                <div class="aspect-square rounded-lg border-2 border-slate-200 bg-slate-50 flex items-center justify-center">
                    <span class="text-xs font-bold text-slate-400 font-mono">{{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Botón comenzar --}}
    <a href="{{ route('candidate.wartegg.draw', $assignment) }}"
       class="btn-primary w-full justify-center text-base py-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        </svg>
        Comenzar el test
    </a>

    <p class="text-center text-xs text-slate-400 mt-4">
        Tiempo promedio: 25–40 minutos · Sin límite formal
    </p>

</div>
@endsection
