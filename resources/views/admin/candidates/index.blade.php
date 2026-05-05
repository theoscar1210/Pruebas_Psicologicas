@extends('layouts.admin')

@section('title', $filter === 'evaluacion' ? 'Evaluaciones Clínicas' : 'Candidatos')
@section('header', $filter === 'evaluacion' ? 'Evaluaciones Clínicas' : 'Candidatos')

@section('header-actions')
    <a href="{{ route('admin.candidates.create') }}" class="btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo candidato
    </a>
@endsection

@section('content')

@if($filter === 'evaluacion')
<div class="card-info p-4 mb-4">
    <div class="flex items-center gap-2 text-brand-700 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        Mostrando candidatos con al menos una prueba completada, listos para evaluación clínica.
    </div>
</div>
@endif

{{-- Filtros --}}
<form method="GET" class="card mb-4">
    <div class="card-body py-3">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-48 form-group mb-0">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nombre o documento…"
                       class="input">
            </div>
            <div class="min-w-44 form-group mb-0">
                <label class="form-label">Cargo</label>
                <select name="position_id" class="select">
                    <option value="">Todos los cargos</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                            {{ $position->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-36 form-group mb-0">
                <label class="form-label">Estado</label>
                <select name="status" class="select">
                    <option value="">Todos</option>
                    <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Activo</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completado</option>
                    <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="flex items-center gap-2 pb-px">
                <button type="submit" class="btn-primary btn-sm">Filtrar</button>
                @if(request()->hasAny(['search', 'position_id', 'status']))
                    <a href="{{ route('admin.candidates.index') }}" class="btn-ghost btn-sm">Limpiar</a>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- Tabla --}}
<div class="table-wrapper">
    <table class="table-base">
        <thead>
            <tr>
                <th>Candidato</th>
                <th>Cargo</th>
                <th class="text-center">Código</th>
                <th class="text-center">Pruebas</th>
                <th class="text-center">Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($candidates as $candidate)
            <tr>
                <td>
                    <p class="font-medium text-slate-900">{{ $candidate->name }}</p>
                    <p class="text-xs text-slate-400">{{ $candidate->email ?? $candidate->document_number ?? '—' }}</p>
                </td>
                <td class="text-slate-600">{{ $candidate->position?->name ?? '—' }}</td>
                <td class="text-center">
                    <span class="font-mono text-sm font-bold tracking-widest text-brand-700 bg-brand-50 px-2 py-0.5 rounded-lg">
                        {{ $candidate->access_code }}
                    </span>
                </td>
                <td class="text-center">
                    @php
                        $completed = $candidate->assignments->where('status', 'completed')->count();
                        $total = $candidate->assignments->count();
                    @endphp
                    <span class="text-xs {{ $completed === $total && $total > 0 ? 'text-emerald-600 font-semibold' : 'text-slate-500' }}">
                        {{ $completed }}/{{ $total }}
                    </span>
                </td>
                <td class="text-center">
                    @if($candidate->status === 'active')
                        <span class="badge-success">Activo</span>
                    @elseif($candidate->status === 'completed')
                        <span class="badge-info">Completado</span>
                    @else
                        <span class="badge-neutral">Inactivo</span>
                    @endif
                </td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-3">
                        @if($filter === 'evaluacion')
                        <a href="{{ route('admin.assessments.select', $candidate) }}"
                           class="text-emerald-600 hover:text-emerald-800 text-xs font-medium transition-colors">
                            {{ $candidate->evaluatorAssessments->isNotEmpty() ? '+ Evaluación' : 'Evaluar' }}
                        </a>
                        @endif
                        <a href="{{ route('admin.candidates.show', $candidate) }}"
                           class="text-brand-600 hover:text-brand-800 text-xs font-medium transition-colors">Ver</a>
                        <form method="POST" action="{{ route('admin.candidates.destroy', $candidate) }}" class="inline"
                              onsubmit="return confirm('¿Eliminar a {{ addslashes($candidate->name) }}? Se borrarán todas sus pruebas y resultados.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium transition-colors">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                    No se encontraron candidatos.
                    <a href="{{ route('admin.candidates.create') }}" class="text-brand-600 hover:underline ml-1">Agregar uno</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($candidates->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $candidates->links() }}
        </div>
    @endif
</div>

@endsection
