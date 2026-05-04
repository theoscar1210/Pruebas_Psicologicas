@extends('layouts.admin')

@section('title', 'Pruebas Psicológicas')
@section('header', 'Pruebas Psicológicas')

@section('header-actions')
    <a href="{{ route('admin.tests.create') }}" class="btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva prueba
    </a>
@endsection

@section('content')

<div class="table-wrapper">
    <table class="table-base">
        <thead>
            <tr>
                <th>Prueba</th>
                <th class="text-center">Preguntas</th>
                <th class="text-center">Tiempo</th>
                <th class="text-center">Aprobación</th>
                <th class="text-center">Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tests as $test)
            <tr>
                <td>
                    <p class="font-medium text-slate-900">{{ $test->name }}</p>
                    @if($test->description)
                        <p class="text-xs text-slate-400 mt-0.5 truncate max-w-xs">{{ $test->description }}</p>
                    @endif
                </td>
                <td class="text-center">
                    @php
                    $modularCounts = ['tsc_sl' => '63', 'tte_sl' => '63'];
                    $specialLabels = ['wartegg' => '8 campos', 'star_interview' => '15 STAR'];
                    @endphp
                    @if(isset($modularCounts[$test->test_type]))
                        <span class="inline-flex items-center justify-center px-2 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold"
                              title="Test de módulos: ítems hardcodeados en el flujo">
                            {{ $modularCounts[$test->test_type] }}
                        </span>
                    @elseif(isset($specialLabels[$test->test_type]))
                        <span class="inline-flex items-center justify-center px-2 h-7 rounded-full bg-slate-100 text-slate-500 text-xs font-medium">
                            {{ $specialLabels[$test->test_type] }}
                        </span>
                    @else
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-violet-100 text-violet-700 text-xs font-bold">
                            {{ $test->questions_count }}
                        </span>
                    @endif
                </td>
                <td class="text-center text-slate-600 text-sm">
                    {{ $test->time_limit ? $test->time_limit . ' min' : 'Sin límite' }}
                </td>
                <td class="text-center">
                    <span class="font-semibold text-sm text-slate-700">{{ $test->passing_score }}%</span>
                </td>
                <td class="text-center">
                    @if($test->is_active)
                        <span class="badge-success">Activa</span>
                    @else
                        <span class="badge-neutral">Inactiva</span>
                    @endif
                </td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.tests.questions.index', $test) }}"
                           class="text-violet-600 hover:text-violet-800 text-xs font-medium transition-colors">Preguntas</a>
                        <a href="{{ route('admin.tests.edit', $test) }}"
                           class="text-brand-600 hover:text-brand-800 text-xs font-medium transition-colors">Editar</a>
                        <form action="{{ route('admin.tests.destroy', $test) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar la prueba {{ addslashes($test->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium transition-colors">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                    No hay pruebas registradas.
                    <a href="{{ route('admin.tests.create') }}" class="text-brand-600 hover:underline ml-1">Crear la primera</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($tests->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $tests->links() }}
        </div>
    @endif
</div>

@endsection
