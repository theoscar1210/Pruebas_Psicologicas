@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('header-actions')
    <a href="{{ route('admin.candidates.create') }}" class="btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo candidato
    </a>
@endsection

@section('content')

{{-- KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">

    <div class="kpi-card border-l-4 border-l-brand-500">
        <span class="kpi-label">Cargos activos</span>
        <span class="kpi-value text-brand-700">{{ $stats['positions'] }}</span>
    </div>

    <div class="kpi-card border-l-4 border-l-violet-500">
        <span class="kpi-label">Pruebas activas</span>
        <span class="kpi-value text-violet-700">{{ $stats['tests'] }}</span>
    </div>

    <div class="kpi-card border-l-4 border-l-slate-400">
        <span class="kpi-label">Candidatos</span>
        <span class="kpi-value">{{ $stats['candidates'] }}</span>
    </div>

    <div class="kpi-card border-l-4 border-l-emerald-500">
        <span class="kpi-label">Completadas</span>
        <span class="kpi-value text-emerald-700">{{ $stats['completed'] }}</span>
    </div>

    <div class="kpi-card border-l-4 border-l-amber-400">
        <span class="kpi-label">En progreso</span>
        <span class="kpi-value text-amber-600">{{ $stats['in_progress'] }}</span>
    </div>

    <div class="kpi-card border-l-4 border-l-slate-200">
        <span class="kpi-label">Pendientes</span>
        <span class="kpi-value text-slate-500">{{ $stats['pending'] }}</span>
    </div>

</div>

{{-- Actividad reciente --}}
<div class="table-wrapper">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="font-semibold text-slate-800 text-sm">Actividad reciente</h2>
        <a href="{{ route('admin.candidates.index') }}" class="btn-ghost btn-sm text-xs">
            Ver todos →
        </a>
    </div>

    @if($recentAssignments->isEmpty())
        <div class="px-5 py-14 text-center">
            <p class="text-slate-400 text-sm">Aún no hay actividad registrada.</p>
        </div>
    @else
        <table class="table-base">
            <thead>
                <tr>
                    <th>Candidato</th>
                    <th>Prueba</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Resultado</th>
                    <th class="text-right">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentAssignments as $assignment)
                <tr>
                    <td>
                        <a href="{{ route('admin.candidates.show', $assignment->candidate) }}"
                           class="font-medium text-slate-900 hover:text-brand-700 transition-colors">
                            {{ $assignment->candidate->name }}
                        </a>
                    </td>
                    <td class="text-slate-500">{{ $assignment->test->name }}</td>
                    <td class="text-center">
                        @if($assignment->status === 'completed')
                            <span class="badge-success">Completada</span>
                        @elseif($assignment->status === 'in_progress')
                            <span class="badge-warning">En progreso</span>
                        @else
                            <span class="badge-neutral">Pendiente</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($assignment->result)
                            <span class="font-semibold text-sm {{ $assignment->result->passed ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $assignment->result->percentage }}%
                            </span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="text-right text-slate-400 text-xs">
                        {{ $assignment->updated_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection
