@extends('layouts.admin')

@section('title', 'Cargos')
@section('header', 'Cargos')

@section('header-actions')
    <a href="{{ route('admin.positions.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Cargo
    </a>
@endsection

@section('content')

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-xs text-gray-500 uppercase tracking-wide bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-3 text-left">Cargo</th>
                <th class="px-6 py-3 text-left">Descripción</th>
                <th class="px-6 py-3 text-center">Pruebas asignadas</th>
                <th class="px-6 py-3 text-center">Candidatos</th>
                <th class="px-6 py-3 text-center">Estado</th>
                <th class="px-6 py-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($positions as $position)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $position->name }}</td>
                <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $position->description ?? '—' }}</td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">
                        {{ $position->tests->count() }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center text-gray-600">{{ $position->candidates_count }}</td>
                <td class="px-6 py-4 text-center">
                    @if($position->is_active)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Activo</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactivo</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.positions.edit', $position) }}"
                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Editar</a>

                        <form action="{{ route('admin.positions.destroy', $position) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar el cargo {{ addslashes($position->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                    No hay cargos registrados aún.
                    <a href="{{ route('admin.positions.create') }}" class="text-indigo-600 hover:underline ml-1">Crear el primero</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($positions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $positions->links() }}
        </div>
    @endif
</div>

@endsection
