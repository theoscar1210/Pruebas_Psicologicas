@extends('layouts.admin')

@section('title', $candidate->name)
@section('header', $candidate->name)

@section('header-actions')
    <a href="{{ route('admin.reports.candidate.pdf', $candidate) }}" class="btn-danger btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        PDF
    </a>
    <a href="{{ route('admin.candidates.edit', $candidate) }}" class="btn-secondary btn-sm">Editar</a>
    <a href="{{ route('admin.candidates.index') }}" class="btn-ghost btn-sm">← Volver</a>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Datos del candidato ──────────────────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Info personal --}}
        <div class="card">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Información</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Cargo</dt>
                        <dd class="font-medium text-slate-800">{{ $candidate->position?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Documento</dt>
                        <dd class="font-medium text-slate-800">{{ $candidate->document_number ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Email</dt>
                        <dd class="font-medium text-slate-800 truncate max-w-40">{{ $candidate->email ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Teléfono</dt>
                        <dd class="font-medium text-slate-800">{{ $candidate->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-slate-500">Estado</dt>
                        <dd>
                            @if($candidate->status === 'active')
                                <span class="badge-success">Activo</span>
                            @elseif($candidate->status === 'completed')
                                <span class="badge-info">Completado</span>
                            @else
                                <span class="badge-neutral">Inactivo</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Código de acceso --}}
        <div class="card-info text-center p-5">
            <p class="text-xs text-brand-600 mb-2 font-medium">Código de acceso al portal</p>
            <p class="font-mono text-3xl font-bold tracking-[0.3em] text-brand-700">
                {{ $candidate->access_code }}
            </p>
            <p class="text-xs text-brand-500/70 mt-2">
                Comparte este código con el candidato para que acceda en:<br>
                <strong>{{ url('/candidato') }}</strong>
            </p>
        </div>

        {{-- Asignar prueba --}}
        <div class="card">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Asignar prueba</h3>
                <form action="{{ route('admin.candidates.assign-test', $candidate) }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="form-group">
                        <select name="test_id" required class="select">
                            <option value="">Selecciona una prueba…</option>
                            @foreach(\App\Models\Test::where('is_active', true)->orderBy('name')->get() as $test)
                                <option value="{{ $test->id }}">{{ $test->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha límite <span class="form-hint">(opcional)</span></label>
                        <input type="datetime-local" name="expires_at" class="input">
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">Asignar prueba</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Pruebas asignadas ────────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-3">
        <h3 class="font-semibold text-slate-700 text-sm">
            Pruebas asignadas ({{ $candidate->assignments->count() }})
        </h3>

        @forelse($candidate->assignments as $assignment)
        <div class="card">
            <div class="card-body">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <p class="font-medium text-slate-900">{{ $assignment->test->name }}</p>
                            @if($assignment->status === 'completed')
                                <span class="badge-success">Completada</span>
                            @elseif($assignment->status === 'in_progress')
                                <span class="badge-warning">En progreso</span>
                            @elseif($assignment->status === 'expired')
                                <span class="badge-danger">Expirada</span>
                            @else
                                <span class="badge-neutral">Pendiente</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-3 gap-3 text-xs text-slate-500">
                            <div>
                                <span class="block text-slate-400">Asignada</span>
                                <span>{{ $assignment->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($assignment->started_at)
                            <div>
                                <span class="block text-slate-400">Iniciada</span>
                                <span>{{ $assignment->started_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                            @if($assignment->completed_at)
                            <div>
                                <span class="block text-slate-400">Finalizada</span>
                                <span>{{ $assignment->completed_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Resultado --}}
                    @if($assignment->result)
                    <div class="text-center flex-shrink-0 bg-slate-50 rounded-xl px-4 py-3 min-w-28">
                        <p class="text-3xl font-bold {{ $assignment->result->passed ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $assignment->result->percentage }}%
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $assignment->result->total_score }}/{{ $assignment->result->max_score }} pts
                        </p>
                        <p class="text-xs font-semibold mt-1 {{ $assignment->result->passed ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $assignment->result->passed ? '✓ Aprobó' : '✗ No aprobó' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="card border-dashed">
            <div class="card-body py-10 text-center text-slate-400 text-sm">
                No hay pruebas asignadas. Usa el formulario de la izquierda para asignar una.
            </div>
        </div>
        @endforelse
    </div>

</div>

@endsection
