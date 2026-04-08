@extends('layouts.candidate')

@section('title', 'Resultado — ' . $assignment->test->name)
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-gray-600 font-medium">{{ $assignment->candidate->name }}</span>
@endsection

@section('content')

@php
    $result   = $assignment->result;
    $passed   = $result?->passed;
    $pct      = $result?->percentage ?? 0;
@endphp

<div class="max-w-2xl mx-auto px-4 py-10">

    {{-- Tarjeta principal de resultado --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">

        {{-- Banner de color --}}
        <div class="h-2 w-full {{ $passed ? 'bg-gradient-to-r from-green-400 to-emerald-500' : 'bg-gradient-to-r from-red-400 to-rose-500' }}"></div>

        <div class="p-8 text-center">

            {{-- Ícono --}}
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-5
                        {{ $passed ? 'bg-green-100' : 'bg-red-100' }}">
                @if($passed)
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @else
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">
                {{ $passed ? '¡Felicitaciones!' : 'Prueba completada' }}
            </h1>

            <p class="text-gray-500 text-sm mb-8">
                {{ $assignment->test->name }}
            </p>

            {{-- Porcentaje grande --}}
            <div class="mb-6">
                <span class="text-7xl font-extrabold {{ $passed ? 'text-green-600' : 'text-red-500' }}">
                    {{ number_format($pct, 1) }}%
                </span>
                <p class="text-sm font-semibold mt-2 {{ $passed ? 'text-green-600' : 'text-red-500' }}">
                    {{ $passed ? '✓ Aprobado' : '✗ No aprobado' }}
                </p>
            </div>

            {{-- Barra de progreso --}}
            <div class="w-full bg-gray-100 rounded-full h-3 mb-6 max-w-xs mx-auto">
                <div class="h-3 rounded-full transition-all duration-700
                            {{ $passed ? 'bg-green-500' : 'bg-red-400' }}"
                     style="width: {{ $pct }}%">
                </div>
            </div>

            {{-- Desglose de puntaje --}}
            <div class="grid grid-cols-3 gap-4 py-5 border-t border-b border-gray-100 mb-6">
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($result?->total_score, 1) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Tu puntaje</p>
                </div>
                <div class="text-center border-x border-gray-100">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($result?->max_score, 1) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Puntaje máximo</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $assignment->test->passing_score }}%</p>
                    <p class="text-xs text-gray-400 mt-0.5">Mínimo requerido</p>
                </div>
            </div>

            {{-- Mensaje personalizado --}}
            <div class="text-sm text-gray-600 bg-gray-50 rounded-xl px-5 py-4">
                @if($passed)
                    <p>Has superado el puntaje mínimo requerido para esta prueba. El equipo de Recursos Humanos revisará tu resultado y se pondrá en contacto contigo.</p>
                @else
                    <p>Tu resultado no alcanzó el puntaje mínimo requerido ({{ $assignment->test->passing_score }}%). El equipo de Recursos Humanos revisará tu resultado.</p>
                @endif
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
