@extends('layouts.admin')

@section('title', $candidate->name)
@section('header', $candidate->name)

@section('header-actions')
    <a href="{{ route('admin.reports.candidate.pdf', $candidate) }}"
       class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        PDF
    </a>
    <a href="{{ route('admin.candidates.edit', $candidate) }}"
       class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Editar</a>
    <a href="{{ route('admin.candidates.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 ml-3">← Volver</a>
@endsection

@section('content')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── Datos del candidato ──────────────────────────────────────────── --}}
    <div class="space-y-4">

        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Información</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Cargo</dt>
                    <dd class="font-medium text-gray-800">{{ $candidate->position?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Documento</dt>
                    <dd class="font-medium text-gray-800">{{ $candidate->document_number ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="font-medium text-gray-800 truncate max-w-40">{{ $candidate->email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Teléfono</dt>
                    <dd class="font-medium text-gray-800">{{ $candidate->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-gray-500">Estado</dt>
                    <dd>
                        @if($candidate->status === 'active')
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Activo</span>
                        @elseif($candidate->status === 'completed')
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Completado</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Inactivo</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Código de acceso --}}
        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-5 text-center">
            <p class="text-xs text-indigo-500 mb-2">Código de acceso al portal</p>
            <p class="font-mono text-3xl font-bold tracking-[0.3em] text-indigo-700">
                {{ $candidate->access_code }}
            </p>
            <p class="text-xs text-indigo-400 mt-2">
                Comparte este código con el candidato para que acceda en:<br>
                <strong>{{ url('/candidato') }}</strong>
            </p>
        </div>

        {{-- Asignar prueba manualmente --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Asignar prueba</h3>
            <form action="{{ route('admin.candidates.assign-test', $candidate) }}" method="POST" class="space-y-3">
                @csrf
                <select name="test_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecciona una prueba…</option>
                    @foreach(\App\Models\Test::where('is_active', true)->orderBy('name')->get() as $test)
                        <option value="{{ $test->id }}">{{ $test->name }}</option>
                    @endforeach
                </select>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Fecha límite (opcional)</label>
                    <input type="datetime-local" name="expires_at"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 rounded-lg transition">
                    Asignar prueba
                </button>
            </form>
        </div>
    </div>

    {{-- ── Pruebas asignadas ────────────────────────────────────────────── --}}
    <div class="xl:col-span-2 space-y-3">
        <h3 class="font-semibold text-gray-700">Pruebas asignadas ({{ $candidate->assignments->count() }})</h3>

        @forelse($candidate->assignments as $assignment)
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <p class="font-medium text-gray-900">{{ $assignment->test->name }}</p>
                        @if($assignment->status === 'completed')
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Completada</span>
                        @elseif($assignment->status === 'in_progress')
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">En progreso</span>
                        @elseif($assignment->status === 'expired')
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Expirada</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Pendiente</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-3 text-xs text-gray-500">
                        <div>
                            <span class="block text-gray-400">Asignada</span>
                            <span>{{ $assignment->created_at->format('d/m/Y') }}</span>
                        </div>
                        @if($assignment->started_at)
                        <div>
                            <span class="block text-gray-400">Iniciada</span>
                            <span>{{ $assignment->started_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                        @if($assignment->completed_at)
                        <div>
                            <span class="block text-gray-400">Finalizada</span>
                            <span>{{ $assignment->completed_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Resultado --}}
                @if($assignment->result)
                <div class="text-center flex-shrink-0 bg-gray-50 rounded-lg px-4 py-3 min-w-28">
                    <p class="text-3xl font-bold {{ $assignment->result->passed ? 'text-green-600' : 'text-red-500' }}">
                        {{ $assignment->result->percentage }}%
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $assignment->result->total_score }}/{{ $assignment->result->max_score }} pts
                    </p>
                    <p class="text-xs font-semibold mt-1 {{ $assignment->result->passed ? 'text-green-600' : 'text-red-500' }}">
                        {{ $assignment->result->passed ? '✓ Aprobó' : '✗ No aprobó' }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400 text-sm border border-dashed border-gray-200">
            No hay pruebas asignadas. Usa el formulario de la izquierda para asignar una.
        </div>
        @endforelse
    </div>

</div>

@endsection
