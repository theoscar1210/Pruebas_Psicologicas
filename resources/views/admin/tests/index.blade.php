@extends('layouts.admin')

@section('title', 'Pruebas Psicológicas')
@section('header', 'Pruebas Psicológicas')

@section('header-actions')
    <a href="{{ route('admin.tests.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva Prueba
    </a>
@endsection

@section('content')

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-xs text-gray-500 uppercase tracking-wide bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-3 text-left">Prueba</th>
                <th class="px-6 py-3 text-center">Preguntas</th>
                <th class="px-6 py-3 text-center">Tiempo límite</th>
                <th class="px-6 py-3 text-center">Puntaje aprobado</th>
                <th class="px-6 py-3 text-center">Estado</th>
                <th class="px-6 py-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($tests as $test)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <p class="font-medium text-gray-900">{{ $test->name }}</p>
                    @if($test->description)
                        <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $test->description }}</p>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-violet-100 text-violet-700 text-xs font-bold">
                        {{ $test->questions_count }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center text-gray-600">
                    {{ $test->time_limit ? $test->time_limit . ' min' : 'Sin límite' }}
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm font-semibold text-gray-700">{{ $test->passing_score }}%</span>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($test->is_active)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Activa</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactiva</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.tests.questions.index', $test) }}"
                           class="text-violet-600 hover:text-violet-800 text-xs font-medium">Preguntas</a>
                        <a href="{{ route('admin.tests.edit', $test) }}"
                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Editar</a>
                        <form action="{{ route('admin.tests.destroy', $test) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar la prueba {{ addslashes($test->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                    No hay pruebas registradas.
                    <a href="{{ route('admin.tests.create') }}" class="text-indigo-600 hover:underline ml-1">Crear la primera</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($tests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $tests->links() }}
        </div>
    @endif
</div>

@endsection
