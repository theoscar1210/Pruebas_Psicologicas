@extends('layouts.candidate')

@section('title', 'Resultado — ' . $assignment->test->name)
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-gray-600 font-medium">{{ $assignment->candidate->name }}</span>
@endsection

@section('content')

@php
    $result = $assignment->result;
@endphp

<div class="max-w-2xl mx-auto px-4 py-10">

    {{-- Tarjeta principal de resultado --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">

        {{-- Banner de color teal (siempre igual — no revela si aprobó o no) --}}
        <div class="h-2 w-full bg-gradient-to-r from-brand-500 to-brand-400"></div>

        <div class="p-8 text-center">

            {{-- Ícono de confirmación neutro --}}
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-5 bg-brand-50">
                <svg class="w-10 h-10 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">
                ¡Evaluación registrada!
            </h1>

            <p class="text-gray-500 text-sm mb-8">
                {{ $assignment->test->name }}
            </p>

            {{-- Mensaje amigable — sin puntaje ni aprobado/reprobado --}}
            <div class="text-sm text-gray-600 bg-brand-50 border border-brand-100 rounded-xl px-5 py-5 mb-6 text-left">
                <p class="font-semibold text-brand-800 mb-2">Gracias por completar esta evaluación</p>
                <p class="leading-relaxed text-gray-600">
                    Tus respuestas han sido registradas exitosamente. El equipo de Recursos Humanos analizará
                    los resultados y se pondrá en contacto contigo para informarte sobre los próximos pasos
                    del proceso de selección.
                </p>
            </div>

            {{-- Nota informativa --}}
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 text-left">
                <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-amber-700 leading-relaxed">
                    Los resultados de las evaluaciones son analizados por el equipo de psicología.
                    No se proporcionan puntajes individuales durante el proceso de selección.
                </p>
            </div>

        </div>
    </div>

    {{-- Info adicional --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Detalles</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500">Candidato</dt>
                <dd class="font-medium text-gray-800">{{ $assignment->candidate->name }}</dd>
            </div>
            @if($assignment->position)
            <div class="flex justify-between">
                <dt class="text-gray-500">Cargo</dt>
                <dd class="font-medium text-gray-800">{{ $assignment->position->name }}</dd>
            </div>
            @endif
            <div class="flex justify-between">
                <dt class="text-gray-500">Iniciada</dt>
                <dd class="text-gray-700">{{ $assignment->started_at?->format('d/m/Y H:i') ?? '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Finalizada</dt>
                <dd class="text-gray-700">{{ $assignment->completed_at?->format('d/m/Y H:i') ?? '—' }}</dd>
            </div>
            @if($assignment->started_at && $assignment->completed_at)
            <div class="flex justify-between">
                <dt class="text-gray-500">Duración</dt>
                <dd class="text-gray-700">{{ $assignment->started_at->diffForHumans($assignment->completed_at, true) }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Botón volver --}}
    <div class="text-center">
        <a href="{{ route('candidate.dashboard') }}"
           class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a mis pruebas
        </a>
    </div>

</div>

@endsection
