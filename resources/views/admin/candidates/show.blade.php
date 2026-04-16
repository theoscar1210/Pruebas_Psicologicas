@extends('layouts.admin')

@section('title', $candidate->name)
@section('header', $candidate->name)

@section('header-actions')
    <a href="{{ route('admin.profile.show', $candidate) }}" class="btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <span class="hidden sm:inline">Perfil Psicológico</span>
        <span class="sm:hidden">Perfil</span>
    </a>
    <a href="{{ route('admin.reports.candidate.pdf', $candidate) }}" class="btn-danger btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        PDF
    </a>
    <a href="{{ route('admin.candidates.edit', $candidate) }}" class="btn-secondary btn-sm hidden sm:inline-flex">Editar</a>
    <a href="{{ route('admin.candidates.index') }}" class="btn-ghost btn-sm">← Volver</a>
@endsection

@section('content')

{{-- Layout: 1 col mobile | 2 col md | sidebar+main lg --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

    {{-- ══════════════════════════════════════════════════════════════
         COLUMNA IZQUIERDA — Info + Código + Asignar + Evaluaciones
    ══════════════════════════════════════════════════════════════ --}}
    <div class="space-y-4 min-w-0">

        {{-- Info personal --}}
        <div class="card">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Información</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-2 min-w-0">
                        <dt class="text-slate-500 flex-shrink-0">Cargo</dt>
                        <dd class="font-medium text-slate-800 truncate text-right">{{ $candidate->position?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2 min-w-0">
                        <dt class="text-slate-500 flex-shrink-0">Documento</dt>
                        <dd class="font-medium text-slate-800 truncate text-right">{{ $candidate->document_number ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2 min-w-0">
                        <dt class="text-slate-500 flex-shrink-0">Email</dt>
                        <dd class="font-medium text-slate-800 truncate text-right min-w-0">{{ $candidate->email ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2 min-w-0">
                        <dt class="text-slate-500 flex-shrink-0">Teléfono</dt>
                        <dd class="font-medium text-slate-800 text-right">{{ $candidate->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <dt class="text-slate-500 flex-shrink-0">Estado</dt>
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
        <div class="card-info p-5 min-w-0 overflow-hidden">
            <p class="text-xs text-brand-600 mb-3 font-medium text-center">Código de acceso al portal</p>
            <div class="bg-white/60 rounded-xl py-4 px-3 text-center mb-3">
                <p class="font-mono text-2xl sm:text-3xl font-bold tracking-[0.2em] text-brand-700 break-all">
                    {{ $candidate->access_code }}
                </p>
            </div>
            <p class="text-xs text-brand-600/70 text-center">
                Comparte este código. URL de acceso:
            </p>
            <p class="text-xs font-semibold text-brand-700 text-center break-all mt-1">
                {{ url('/candidato') }}
            </p>
        </div>

        {{-- Asignar prueba --}}
        <div class="card">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Asignar prueba</h3>
                <form action="{{ route('admin.candidates.assign-test', $candidate) }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="form-group">
                        <select name="test_id" required
                                class="select {{ $errors->has('test_id') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
                            <option value="">Selecciona una prueba…</option>
                            @foreach(\App\Models\Test::where('is_active', true)->orderBy('name')->get() as $test)
                                <option value="{{ $test->id }}" {{ old('test_id') == $test->id ? 'selected' : '' }}>
                                    {{ $test->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('test_id')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            Fecha límite <span class="form-hint">(opcional)</span>
                        </label>
                        <input type="datetime-local"
                               name="expires_at"
                               value="{{ old('expires_at') }}"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="input {{ $errors->has('expires_at') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
                        @error('expires_at')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">Asignar prueba</button>
                </form>
            </div>
        </div>

        {{-- Perfil psicológico --}}
        <div class="card border-brand-200 min-w-0">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-brand-700 uppercase tracking-wider mb-2">Perfil Psicológico</h3>
                <p class="text-xs text-slate-500 mb-4">Genera el perfil unificando todos los resultados.</p>
                <a href="{{ route('admin.profile.show', $candidate) }}" class="btn-primary w-full justify-center text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Ver / Generar Perfil
                </a>
            </div>
        </div>

        {{-- Evaluaciones clínicas --}}
        <div class="card min-w-0">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Evaluaciones clínicas</h3>
                <div class="space-y-2">

                    {{-- Wartegg --}}
                    @php $wartegg = $candidate->evaluatorAssessments->firstWhere('assessment_type', 'wartegg'); @endphp
                    <a href="{{ route('admin.assessments.create', ['candidate' => $candidate, 'type' => 'wartegg']) }}"
                       class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-brand-300 hover:bg-brand-50/50 transition-all group min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 group-hover:text-brand-700 truncate">Wartegg</p>
                            <p class="text-xs text-slate-400 truncate">Proyectivo — 8 cajas</p>
                        </div>
                        <span class="flex-shrink-0 {{ $wartegg ? 'badge-success' : 'badge-neutral' }} text-xs">
                            {{ $wartegg ? 'Listo' : 'Pendiente' }}
                        </span>
                    </a>

                    {{-- STAR --}}
                    @php $star = $candidate->evaluatorAssessments->firstWhere('assessment_type', 'star_interview'); @endphp
                    <a href="{{ route('admin.assessments.create', ['candidate' => $candidate, 'type' => 'star_interview']) }}"
                       class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-brand-300 hover:bg-brand-50/50 transition-all group min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 group-hover:text-brand-700 truncate">Entrevista STAR</p>
                            <p class="text-xs text-slate-400 truncate">10 competencias</p>
                        </div>
                        <span class="flex-shrink-0 {{ $star ? 'badge-success' : 'badge-neutral' }} text-xs">
                            {{ $star ? 'Listo' : 'Pendiente' }}
                        </span>
                    </a>

                    {{-- Assessment Center --}}
                    @php $ac = $candidate->evaluatorAssessments->firstWhere('assessment_type', 'assessment_center'); @endphp
                    <a href="{{ route('admin.assessments.create', ['candidate' => $candidate, 'type' => 'assessment_center']) }}"
                       class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-brand-300 hover:bg-brand-50/50 transition-all group min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 group-hover:text-brand-700 truncate">Assessment Center</p>
                            <p class="text-xs text-slate-400 truncate">5 escenarios escritos</p>
                        </div>
                        <span class="flex-shrink-0 {{ $ac ? 'badge-success' : 'badge-neutral' }} text-xs">
                            {{ $ac ? 'Listo' : 'Pendiente' }}
                        </span>
                    </a>

                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         COLUMNA DERECHA — Pruebas asignadas (span 2 en lg)
    ══════════════════════════════════════════════════════════════ --}}
    <div class="md:col-span-1 lg:col-span-2 space-y-3 min-w-0">
        <h3 class="font-semibold text-slate-700 text-sm">
            Pruebas asignadas ({{ $candidate->assignments->count() }})
        </h3>

        @forelse($candidate->assignments as $assignment)
        <div class="card min-w-0">
            <div class="card-body">
                <div class="flex items-start justify-between gap-3 flex-wrap sm:flex-nowrap">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <p class="font-medium text-slate-900 truncate">{{ $assignment->test->name }}</p>
                            @if($assignment->status === 'completed')
                                <span class="badge-success flex-shrink-0">Completada</span>
                            @elseif($assignment->status === 'in_progress')
                                <span class="badge-warning flex-shrink-0">En progreso</span>
                            @elseif($assignment->status === 'expired')
                                <span class="badge-danger flex-shrink-0">Expirada</span>
                            @else
                                <span class="badge-neutral flex-shrink-0">Pendiente</span>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                            <div>
                                <span class="text-slate-400">Asignada: </span>
                                {{ $assignment->created_at->format('d/m/Y') }}
                            </div>
                            @if($assignment->started_at)
                            <div>
                                <span class="text-slate-400">Iniciada: </span>
                                {{ $assignment->started_at->format('d/m/Y H:i') }}
                            </div>
                            @endif
                            @if($assignment->completed_at)
                            <div>
                                <span class="text-slate-400">Finalizada: </span>
                                {{ $assignment->completed_at->format('d/m/Y H:i') }}
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Resultado --}}
                    @if($assignment->result)
                    <div class="text-center flex-shrink-0 bg-slate-50 rounded-xl px-4 py-3 min-w-24">
                        <p class="text-2xl font-bold {{ $assignment->result->passed ? 'text-emerald-600' : 'text-red-500' }}">
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
                No hay pruebas asignadas aún. Usa el formulario de la izquierda para asignar una.
            </div>
        </div>
        @endforelse
    </div>

</div>

@endsection
