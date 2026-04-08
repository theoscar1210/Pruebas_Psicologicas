@extends('layouts.admin')

@section('title', 'Cargos')
@section('header', 'Cargos')

@section('header-actions')
    <a href="{{ route('admin.positions.create') }}" class="btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo cargo
    </a>
@endsection

@section('content')

<div class="table-wrapper">
    <table class="table-base">
        <thead>
            <tr>
                <th>Cargo</th>
                <th>Descripción</th>
                <th class="text-center">Pruebas</th>
                <th class="text-center">Candidatos</th>
                <th class="text-center">Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($positions as $position)
            <tr>
                <td class="font-medium text-slate-900">{{ $position->name }}</td>
                <td class="text-slate-500 max-w-xs truncate">{{ $position->description ?? '—' }}</td>
                <td class="text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-brand-100 text-brand-700 text-xs font-bold">
                        {{ $position->tests->count() }}
                    </span>
                </td>
                <td class="text-center text-slate-600">{{ $position->candidates_count }}</td>
                <td class="text-center">
                    @if($position->is_active)
                        <span class="badge-success">Activo</span>
                    @else
                        <span class="badge-neutral">Inactivo</span>
                    @endif
                </td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.positions.edit', $position) }}"
                           class="text-brand-600 hover:text-brand-800 text-xs font-medium transition-colors">Editar</a>
                        <form action="{{ route('admin.positions.destroy', $position) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar el cargo {{ addslashes($position->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium transition-colors">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                    No hay cargos registrados.
                    <a href="{{ route('admin.positions.create') }}" class="text-brand-600 hover:underline ml-1">Crear el primero</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($positions->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $positions->links() }}
        </div>
    @endif
</div>

@endsection
