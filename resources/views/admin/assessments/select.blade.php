@extends('layouts.admin')
@section('title', 'Evaluaciones — ' . $candidate->name)
@section('header', 'Evaluaciones Clínicas')

@section('header-actions')
    <a href="{{ route('admin.candidates.index', ['filter' => 'evaluacion']) }}" class="btn-ghost btn-sm">← Volver</a>
@endsection

@section('content')

@php
$types = [
    [
        'key'         => 'wartegg',
        'label'       => 'Test de Wartegg',
        'sublabel'    => 'Evaluación proyectiva · 8 cajas',
        'description' => 'Prueba proyectiva gráfica. El candidato completa 8 campos con dibujos; el evaluador registra los indicadores por caja (estabilidad emocional, flexibilidad, logro, vitalidad, etc.).',
        'duration'    => 'Sin límite',
        'icon_bg'     => 'bg-violet-100',
        'icon_color'  => 'text-violet-600',
        'badge_class' => 'bg-violet-100 text-violet-700',
        'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>',
    ],
    [
        'key'         => 'star_interview',
        'label'       => 'Entrevista STAR',
        'sublabel'    => 'Entrevista conductual · 10 competencias',
        'description' => 'Entrevista estructurada basada en el método STAR (Situación, Tarea, Acción, Resultado). El evaluador conduce la entrevista y califica cada competencia del 1 al 5.',
        'duration'    => '45–60 min',
        'icon_bg'     => 'bg-amber-100',
        'icon_color'  => 'text-amber-600',
        'badge_class' => 'bg-amber-100 text-amber-700',
        'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
    ],
    [
        'key'         => 'assessment_center',
        'label'       => 'AC-SL Assessment Center',
        'sublabel'    => '8 competencias · 3 clústeres BARS',
        'description' => 'Evaluación integral de competencias conductuales mediante escala BARS 1-5. Cubre: Liderazgo y Gestión, Relaciones Interpersonales, Desempeño y Resultados. Incluye guía de indicadores y preguntas BEI.',
        'duration'    => 'Jornada AC (4–6 h)',
        'icon_bg'     => 'bg-emerald-100',
        'icon_color'  => 'text-emerald-600',
        'badge_class' => 'bg-emerald-100 text-emerald-700',
        'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
    ],
];
@endphp

{{-- Info del candidato --}}
<div class="card-info p-4 mb-6 flex items-center gap-4">
    <div class="w-10 h-10 rounded-full bg-brand-700 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
        {{ strtoupper(substr($candidate->name, 0, 1)) }}
    </div>
    <div>
        <p class="font-semibold text-slate-900">{{ $candidate->name }}</p>
        <p class="text-xs text-slate-500">
            {{ $candidate->position?->name ?? 'Sin cargo asignado' }}
            @if($candidate->document_number) · Doc: {{ $candidate->document_number }} @endif
        </p>
    </div>
    <a href="{{ route('admin.candidates.show', $candidate) }}"
       class="ml-auto text-xs text-brand-600 hover:underline flex-shrink-0">
        Ver ficha completa →
    </a>
</div>

<p class="text-sm text-slate-500 mb-5">Selecciona la evaluación que deseas aplicar o continuar para este candidato.</p>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach($types as $t)
    @php $existing = $assessments[$t['key']] ?? null; @endphp

    <div class="card hover:shadow-md transition-shadow flex flex-col">
        <div class="card-body flex flex-col flex-1">

            {{-- Icono + estado --}}
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl {{ $t['icon_bg'] }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $t['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $t['icon'] !!}
                    </svg>
                </div>
                @if($existing)
                    <span class="badge-success text-xs flex-shrink-0">Completada</span>
                @else
                    <span class="badge-neutral text-xs flex-shrink-0">Pendiente</span>
                @endif
            </div>

            {{-- Nombre + descripción --}}
            <h3 class="font-semibold text-slate-900 mb-0.5">{{ $t['label'] }}</h3>
            <p class="text-xs text-slate-400 mb-3">{{ $t['sublabel'] }}</p>
            <p class="text-xs text-slate-500 leading-relaxed flex-1">{{ $t['description'] }}</p>

            {{-- Duración --}}
            <div class="flex items-center gap-1.5 mt-4 mb-4 text-xs text-slate-400">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $t['duration'] }}
            </div>

            {{-- Acciones --}}
            <div class="space-y-2 mt-auto">
                @if($existing)
                    {{-- Ya existe → mostrar puntaje + botones editar/nueva --}}
                    <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg mb-2">
                        <div class="flex-1 text-xs text-slate-600">
                            Puntaje global:
                            <span class="font-bold text-slate-800">
                                {{ $existing->overall_score !== null ? number_format($existing->overall_score, 1) . '%' : '—' }}
                            </span>
                        </div>
                        <span class="text-[10px] text-slate-400">
                            {{ $existing->completed_at?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                    <a href="{{ route('admin.assessments.edit', ['assessment' => $existing, 'back' => 'select']) }}"
                       class="btn-primary btn-sm w-full justify-center">
                        Editar evaluación
                    </a>
                    <a href="{{ route('admin.assessments.create', ['candidate' => $candidate, 'type' => $t['key'], 'back' => 'select']) }}"
                       class="btn-ghost btn-sm w-full justify-center text-xs">
                        + Nueva evaluación
                    </a>
                @else
                    <a href="{{ route('admin.assessments.create', ['candidate' => $candidate, 'type' => $t['key'], 'back' => 'select']) }}"
                       class="btn-primary btn-sm w-full justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Iniciar evaluación
                    </a>
                @endif
            </div>

        </div>
    </div>
    @endforeach
</div>

@endsection
