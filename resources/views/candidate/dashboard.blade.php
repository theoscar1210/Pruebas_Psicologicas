@extends('layouts.candidate')

@section('title', 'Mis Pruebas')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-gray-600 font-medium">{{ $candidate->name }}</span>
    @if($candidate->position)
        <span class="hidden sm:inline text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">
            {{ $candidate->position->name }}
        </span>
    @endif
@endsection

@section('content')

<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- Bienvenida --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Bienvenido, {{ explode(' ', $candidate->name)[0] }}</h1>
        <p class="text-gray-500 text-sm mt-1">
            @php
                $pending    = $candidate->assignments->whereIn('status', ['pending', 'in_progress'])->count();
                $completed  = $candidate->assignments->where('status', 'completed')->count();
                $total      = $candidate->assignments->count();
            @endphp
            @if($total === 0)
                No tienes pruebas asignadas aún.
            @elseif($pending > 0)
                Tienes <strong>{{ $pending }}</strong> prueba(s) pendiente(s) de completar.
            @else
                ¡Completaste todas tus pruebas! ({{ $completed }}/{{ $total }})
            @endif
        </p>
    </div>

    {{-- Progreso general --}}
    @if($total > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progreso general</span>
            <span class="text-sm font-bold text-indigo-700">{{ $completed }}/{{ $total }}</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2.5">
            <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500"
                 style="width: {{ $total > 0 ? ($completed/$total)*100 : 0 }}%"></div>
        </div>
    </div>
    @endif

    {{-- Lista de pruebas --}}
    <div class="space-y-4">

        @forelse($candidate->assignments->sortBy('status') as $assignment)

        @php
            $isExpired    = $assignment->isExpired();
            $isCompleted  = $assignment->isCompleted();
            $isInProgress = $assignment->isInProgress();
            $isPending    = $assignment->isPending();
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden
                    {{ $isCompleted ? 'opacity-80' : '' }}">

            {{-- Barra de color superior --}}
            <div class="h-1 w-full
                @if($isCompleted && $assignment->result?->passed)  bg-green-400
                @elseif($isCompleted && !$assignment->result?->passed) bg-red-400
                @elseif($isInProgress) bg-yellow-400
                @elseif($isExpired)    bg-gray-300
                @else                  bg-indigo-400 @endif">
            </div>

            <div class="p-5 flex items-start justify-between gap-4">

                {{-- Info de la prueba --}}
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h2 class="font-semibold text-gray-900">{{ $assignment->test->name }}</h2>

                        @if($isCompleted)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-green-100 text-green-700">Completada</span>
                        @elseif($isInProgress)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-yellow-100 text-yellow-800 animate-pulse">En progreso</span>
                        @elseif($isExpired)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-red-100 text-red-600">Expirada</span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-indigo-100 text-indigo-700">Pendiente</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 text-xs text-gray-400 mt-1">
                        @if($assignment->test->time_limit)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $assignment->test->time_limit }} minutos
                            </span>
                        @else
                            <span>Sin límite de tiempo</span>
                        @endif

                        @if($assignment->expires_at && !$isCompleted)
                            <span class="flex items-center gap-1 {{ $assignment->expires_at->isPast() ? 'text-red-400' : '' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Límite: {{ $assignment->expires_at->format('d/m/Y H:i') }}
                            </span>
                        @endif

                        @if($isInProgress && $assignment->time_remaining)
                            <span class="text-yellow-600 font-medium">
                                Retomar — {{ gmdate('H:i:s', $assignment->time_remaining) }} restantes
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Resultado o botón de acción --}}
                <div class="flex-shrink-0">
                    @if($isCompleted && $assignment->result)
                        <div class="text-center">
                            <p class="text-3xl font-bold {{ $assignment->result->passed ? 'text-green-600' : 'text-red-500' }}">
                                {{ $assignment->result->percentage }}%
                            </p>
                            <p class="text-xs font-semibold {{ $assignment->result->passed ? 'text-green-600' : 'text-red-500' }} mt-0.5">
                                {{ $assignment->result->passed ? '✓ Aprobó' : '✗ No aprobó' }}
                            </p>
                            <a href="{{ route('candidate.result', $assignment) }}"
                               class="text-xs text-indigo-600 hover:underline mt-1 block">Ver detalle</a>
                        </div>

                    @elseif(!$isExpired && ($isPending || $isInProgress))
                        <a href="{{ route('candidate.start', $assignment) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition
                                  {{ $isInProgress ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white' }}">
                            {{ $isInProgress ? 'Retomar' : 'Iniciar' }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                    @elseif($isExpired)
                        <span class="text-xs text-gray-400">No disponible</span>
                    @endif
                </div>

            </div>
        </div>

        @empty
        <div class="bg-white rounded-2xl shadow-sm border border-dashed border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 text-sm font-medium">No tienes pruebas asignadas</p>
            <p class="text-gray-400 text-xs mt-1">El área de Recursos Humanos te asignará las pruebas pronto.</p>
        </div>
        @endforelse

    </div>
</div>

@endsection
