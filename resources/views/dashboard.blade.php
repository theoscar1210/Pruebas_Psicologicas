@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

{{-- Tarjetas de estadísticas --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">

    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Cargos activos</p>
        <p class="text-3xl font-bold text-indigo-700 mt-1">{{ $stats['positions'] }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-violet-500">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Pruebas activas</p>
        <p class="text-3xl font-bold text-violet-700 mt-1">{{ $stats['tests'] }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Candidatos</p>
        <p class="text-3xl font-bold text-blue-700 mt-1">{{ $stats['candidates'] }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Completadas</p>
        <p class="text-3xl font-bold text-green-700 mt-1">{{ $stats['completed'] }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
        <p class="text-xs text-gray-500 uppercase tracking-wide">En progreso</p>
        <p class="text-3xl font-bold text-yellow-700 mt-1">{{ $stats['in_progress'] }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-gray-400">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Pendientes</p>
        <p class="text-3xl font-bold text-gray-700 mt-1">{{ $stats['pending'] }}</p>
    </div>

</div>

{{-- Actividad reciente --}}
<div class="bg-white rounded-xl shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-700">Actividad Reciente</h2>
        <a href="{{ route('admin.candidates.index') }}"
           class="text-sm text-indigo-600 hover:text-indigo-800">Ver todos →</a>
    </div>

    @if($recentAssignments->isEmpty())
        <div class="px-6 py-12 text-center text-gray-400 text-sm">
            Aún no hay actividad registrada.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 uppercase tracking-wide border-b border-gray-100">
                        <th class="px-6 py-3 text-left">Candidato</th>
                        <th class="px-6 py-3 text-left">Prueba</th>
                        <th class="px-6 py-3 text-left">Estado</th>
                        <th class="px-6 py-3 text-left">Resultado</th>
                        <th class="px-6 py-3 text-left">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentAssignments as $assignment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 font-medium text-gray-800">
                            <a href="{{ route('admin.candidates.show', $assignment->candidate) }}"
                               class="hover:text-indigo-600">
                                {{ $assignment->candidate->name }}
                            </a>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $assignment->test->name }}</td>
                        <td class="px-6 py-3">
                            @if($assignment->status === 'completed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completada</span>
                            @elseif($assignment->status === 'in_progress')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">En progreso</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @if($assignment->result)
                                <span class="font-semibold {{ $assignment->result->passed ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $assignment->result->percentage }}%
                                    {{ $assignment->result->passed ? '✓' : '✗' }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-400">{{ $assignment->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
