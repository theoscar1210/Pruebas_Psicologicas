@extends('layouts.admin')

@section('title', 'Reportes')
@section('header', 'Reportes y Exportaciones')

@section('content')

{{-- KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="kpi-card border-l-4 border-l-slate-400">
        <span class="kpi-label">Total candidatos</span>
        <span class="kpi-value">{{ $stats['total_candidates'] }}</span>
    </div>
    <div class="kpi-card border-l-4 border-l-brand-500">
        <span class="kpi-label">Pruebas completadas</span>
        <span class="kpi-value text-brand-700">{{ $stats['completed'] }}</span>
    </div>
    <div class="kpi-card border-l-4 border-l-emerald-500">
        <span class="kpi-label">Aprobadas</span>
        <span class="kpi-value text-emerald-700">{{ $stats['passed'] }}</span>
    </div>
    <div class="kpi-card border-l-4 border-l-red-400">
        <span class="kpi-label">No aprobadas</span>
        <span class="kpi-value text-red-600">{{ $stats['failed'] }}</span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── Reporte individual PDF ──────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-slate-900 text-sm">Reporte individual PDF</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Genera un PDF con todos los resultados de un candidato específico.</p>
                </div>
            </div>

            <div x-data="{ candidate: '' }" class="space-y-3">
                <div class="form-group">
                    <label class="form-label">Selecciona el candidato</label>
                    <select x-model="candidate" class="select">
                        <option value="">— Elige un candidato —</option>
                        @foreach(\App\Models\Candidate::with('position')->orderBy('name')->get() as $candidate)
                            <option value="{{ $candidate->id }}">
                                {{ $candidate->name }}
                                @if($candidate->position) — {{ $candidate->position->name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <a :href="candidate ? `/admin/reportes/candidato/${candidate}/pdf` : '#'"
                   :class="candidate ? '' : 'opacity-40 pointer-events-none'"
                   class="btn-danger btn-sm inline-flex">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ── Ranking por cargo PDF ───────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-slate-900 text-sm">Ranking por cargo PDF</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Ranking de candidatos ordenados por puntaje promedio para un cargo.</p>
                </div>
            </div>

            <form action="{{ route('admin.reports.ranking.pdf') }}" method="GET" x-data="{ pos: '' }" class="space-y-3">
                <div class="form-group">
                    <label class="form-label">Selecciona el cargo</label>
                    <select name="position_id" x-model="pos" class="select">
                        <option value="">— Elige un cargo —</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}">
                                {{ $position->name }} ({{ $position->candidates_count }} candidatos)
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" :disabled="!pos"
                        class="btn-sm inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl px-3 py-1.5 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar Ranking PDF
                </button>
            </form>
        </div>
    </div>

    {{-- ── Excel general ───────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-slate-900 text-sm">Exportar resultados Excel</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Exporta todos los resultados completados. Puedes filtrar por cargo.</p>
                </div>
            </div>

            <form action="{{ route('admin.reports.export.excel') }}" method="GET" class="space-y-3">
                <div class="form-group">
                    <label class="form-label">Filtrar por cargo <span class="form-hint">(opcional)</span></label>
                    <select name="position_id" class="select">
                        <option value="">Todos los cargos</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-3 py-1.5 rounded-xl transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exportar Excel
                </button>
            </form>
        </div>
    </div>

    {{-- ── Ranking Excel ───────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-slate-900 text-sm">Ranking por cargo Excel</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Ranking de candidatos con puntajes ordenado por cargo, listo para compartir.</p>
                </div>
            </div>

            <form action="{{ route('admin.reports.ranking.excel') }}" method="GET" x-data="{ pos: '' }" class="space-y-3">
                <div class="form-group">
                    <label class="form-label">Selecciona el cargo <span class="form-required">*</span></label>
                    <select name="position_id" x-model="pos" required class="select">
                        <option value="">— Elige un cargo —</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" :disabled="!pos"
                        class="inline-flex items-center gap-1.5 bg-brand-700 hover:bg-brand-800 text-white text-sm font-medium px-3 py-1.5 rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exportar Ranking Excel
                </button>
            </form>
        </div>
    </div>

</div>

@if(auth()->user()->role !== 'hr')
<div class="mt-6 card-info p-4 text-sm text-brand-700">
    <strong>Tip:</strong> También puedes descargar el PDF individual de cada candidato desde
    <a href="{{ route('admin.candidates.index') }}" class="underline font-medium">la vista de candidatos</a>
    → Ver detalle → botón PDF.
</div>
@endif

@endsection
