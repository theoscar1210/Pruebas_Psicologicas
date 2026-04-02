@extends('layouts.admin')

@section('title', 'Candidatos')
@section('header', 'Candidatos')

@section('header-actions')
    <a href="{{ route('admin.candidates.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Candidato
    </a>
@endsection

@section('content')

{{-- Filtros --}}
<form method="GET" class="bg-white rounded-xl shadow-sm px-5 py-4 mb-4 flex flex-wrap items-end gap-3">
    <div class="flex-1 min-w-48">
        <label class="block text-xs text-gray-500 mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Nombre o documento…"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div class="min-w-40">
        <label class="block text-xs text-gray-500 mb-1">Cargo</label>
        <select name="position_id"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todos los cargos</option>
            @foreach($positions as $position)
                <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                    {{ $position->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="min-w-36">
        <label class="block text-xs text-gray-500 mb-1">Estado</label>
        <select name="status"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todos</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Activo</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completado</option>
            <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>
    <button type="submit"
            class="bg-gray-800 hover:bg-gray-900 text-white text-sm px-4 py-2 rounded-lg transition">
        Filtrar
    </button>
    @if(request()->hasAny(['search', 'position_id', 'status']))
        <a href="{{ route('admin.candidates.index') }}"
           class="text-sm text-gray-400 hover:text-gray-600 py-2">Limpiar</a>
    @endif
</form>

{{-- Tabla --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-xs text-gray-500 uppercase tracking-wide bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-3 text-left">Candidato</th>
                <th class="px-6 py-3 text-left">Cargo</th>
                <th class="px-6 py-3 text-center">Código acceso</th>
                <th class="px-6 py-3 text-center">Pruebas</th>
                <th class="px-6 py-3 text-center">Estado</th>
                <th class="px-6 py-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($candidates as $candidate)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <p class="font-medium text-gray-900">{{ $candidate->name }}</p>
                    <p class="text-xs text-gray-400">{{ $candidate->email ?? $candidate->document_number ?? '—' }}</p>
                </td>
                <td class="px-6 py-4 text-gray-600">
                    {{ $candidate->position?->name ?? '—' }}
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="font-mono text-sm font-semibold tracking-widest text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">
                        {{ $candidate->access_code }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    @php
                        $completed = $candidate->assignments->where('status', 'completed')->count();
                        $total = $candidate->assignments->count();
                    @endphp
                    <span class="text-xs {{ $completed === $total && $total > 0 ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                        {{ $completed }}/{{ $total }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($candidate->status === 'active')
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Activo</span>
                    @elseif($candidate->status === 'completed')
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Completado</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactivo</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('admin.candidates.show', $candidate) }}"
                       class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Ver detalle</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                    No se encontraron candidatos.
                    <a href="{{ route('admin.candidates.create') }}" class="text-indigo-600 hover:underline ml-1">Agregar uno</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($candidates->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $candidates->links() }}
        </div>
    @endif
</div>

@endsection
