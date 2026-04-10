@extends('layouts.admin')

@section('title', $candidate->name)
@section('header', $candidate->name)

@section('header-actions')
    <a href="{{ route('admin.profile.show', $candidate) }}" class="btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Perfil Psicológico
    </a>
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

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

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

    {{-- ── Evaluaciones clínicas + Perfil ─────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Perfil psicológico --}}
        <div class="card bg-gradient-to-br from-brand-50 to-teal-50 border-brand-200">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-brand-700 uppercase tracking-wider mb-3">Perfil Psicológico</h3>
                <p class="text-xs text-slate-500 mb-4">Genera el perfil unificando todos los resultados del candidato.</p>
                <a href="{{ route('admin.profile.show', $candidate) }}" class="btn-primary w-full justify-center text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Ver / Generar Perfil
                </a>
            </div>
        </div>

        {{-- Evaluaciones clínicas --}}
        <div class="card">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Evaluaciones clínicas</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.assessments.create', ['candidate' => $candidate, 'type' => 'wartegg']) }}"
                       class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-brand-300 hover:bg-brand-50/50 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-800 group-hover:text-brand-700">Wartegg</p>
                            <p class="text-xs text-slate-400">Test proyectivo — 8 cajas</p>
                        </div>
                        @php $wartegg = $candidate->evaluatorAssessments->firstWhere('assessment_type', 'wartegg'); @endphp
                        @if($wartegg)
                            <span class="badge-success text-xs">Evaluado</span>
                        @else
                            <span class="badge-neutral text-xs">Pendiente</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.assessments.create', ['candidate' => $candidate, 'type' => 'star_interview']) }}"
                       class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-brand-300 hover:bg-brand-50/50 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-800 group-hover:text-brand-700">Entrevista STAR</p>
                            <p class="text-xs text-slate-400">10 competencias conductuales</p>
                        </div>
                        @php $star = $candidate->evaluatorAssessments->firstWhere('assessment_type', 'star_interview'); @endphp
                        @if($star)
                            <span class="badge-success text-xs">Evaluado</span>
                        @else
                            <span class="badge-neutral text-xs">Pendiente</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.assessments.create', ['candidate' => $candidate, 'type' => 'assessment_center']) }}"
                       class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-brand-300 hover:bg-brand-50/50 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-800 group-hover:text-brand-700">Assessment Center</p>
                            <p class="text-xs text-slate-400">5 escenarios escritos</p>
                        </div>
                        @php $ac = $candidate->evaluatorAssessments->firstWhere('assessment_type', 'assessment_center'); @endphp
                        @if($ac)
                            <span class="badge-success text-xs">Evaluado</span>
                        @else
                            <span class="badge-neutral text-xs">Pendiente</span>
                        @endif
                    </a>
                </div>
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
