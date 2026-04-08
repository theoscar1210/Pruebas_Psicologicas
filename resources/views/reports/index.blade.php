@extends('layouts.admin')

@section('title', 'Reportes')
@section('header', 'Reportes y Exportaciones')

@section('content')

{{-- Estadísticas rápidas --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Total candidatos</p>
        <p class="text-3xl font-bold text-indigo-700 mt-1">{{ $stats['total_candidates'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Pruebas completadas</p>
        <p class="text-3xl font-bold text-blue-700 mt-1">{{ $stats['completed'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Aprobadas</p>
        <p class="text-3xl font-bold text-green-700 mt-1">{{ $stats['passed'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-400">
        <p class="text-xs text-gray-500 uppercase tracking-wide">No aprobadas</p>
        <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['failed'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── Reporte individual ────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-gray-900">Reporte individual PDF</h2>
                <p class="text-sm text-gray-500 mt-0.5">Genera un PDF con todos los resultados de un candidato específico, incluyendo el detalle de cada respuesta.</p>
            </div>
        </div>

        <div x-data="{ candidate: '' }">
            <label class="block text-sm font-medium text-gray-700 mb-1">Selecciona el candidato</label>
            <select x-model="candidate"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-3">
                <option value="">— Elige un candidato —</option>
                @foreach(\App\Models\Candidate::with('position')->orderBy('name')->get() as $candidate)
                    <option value="{{ $candidate->id }}">
                        {{ $candidate->name }}
                        @if($candidate->position) — {{ $candidate->position->name }} @endif
                    </option>
                @endforeach
            </select>

            <a :href="candidate ? `/admin/reportes/candidato/${candidate}/pdf` : '#'"
               :class="candidate ? 'bg-red-600 hover:bg-red-700 cursor-pointer' : 'bg-gray-200 cursor-not-allowed pointer-events-none'"
               class="inline-flex items-center gap-2 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Descargar PDF
            </a>
        </div>
    </div>

    {{-- ── Ranking por cargo PDF ─────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-gray-900">Ranking por cargo PDF</h2>
                <p class="text-sm text-gray-500 mt-0.5">Ranking de candidatos ordenados por puntaje promedio para un cargo específico.</p>
            </div>
        </div>

        <form action="{{ route('admin.reports.ranking.pdf') }}" method="GET" x-data="{ pos: '' }">
            <label class="block text-sm font-medium text-gray-700 mb-1">Selecciona el cargo</label>
            <select name="position_id" x-model="pos"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-3">
                <option value="">— Elige un cargo —</option>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}">
                        {{ $position->name }} ({{ $position->candidates_count }} candidatos)
                    </option>
                @endforeach
            </select>

            <button type="submit" :disabled="!pos"
                    class="inline-flex items-center gap-2 text-white text-sm font-medium px-4 py-2 rounded-lg transition
                           bg-orange-500 hover:bg-orange-600 disabled:bg-gray-200 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Descargar Ranking PDF
            </button>
        </form>
    </div>

    {{-- ── Excel general ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-gray-900">Exportar resultados Excel</h2>
                <p class="text-sm text-gray-500 mt-0.5">Exporta todos los resultados de pruebas completadas. Puedes filtrar por cargo.</p>
            </div>
        </div>

        <form action="{{ route('admin.reports.export.excel') }}" method="GET">
            <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por cargo (opcional)</label>
            <select name="position_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-3">
                <option value="">Todos los cargos</option>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                @endforeach
            </select>

            <button type="submit"
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar Excel
            </button>
        </form>
    </div>

    {{-- ── Ranking Excel ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-gray-900">Ranking por cargo Excel</h2>
                <p class="text-sm text-gray-500 mt-0.5">Ranking de candidatos con puntajes ordenado por cargo, listo para compartir.</p>
            </div>
        </div>

        <form action="{{ route('admin.reports.ranking.excel') }}" method="GET" x-data="{ pos: '' }">
            <label class="block text-sm font-medium text-gray-700 mb-1">Selecciona el cargo <span class="text-red-500">*</span></label>
            <select name="position_id" x-model="pos" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-3">
                <option value="">— Elige un cargo —</option>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                @endforeach
            </select>

            <button type="submit" :disabled="!pos"
                    class="inline-flex items-center gap-2 text-white text-sm font-medium px-4 py-2 rounded-lg transition
                           bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-200 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar Ranking Excel
            </button>
        </form>
    </div>

</div>

{{-- Acceso rápido desde la vista de candidatos --}}
<div class="mt-6 bg-indigo-50 border border-indigo-100 rounded-xl p-4 text-sm text-indigo-700">
    <strong>Tip:</strong> También puedes descargar el PDF individual de cada candidato directamente desde
    <a href="{{ route('admin.candidates.index') }}" class="underline font-medium">la vista de candidatos</a>
    → Ver detalle → botón PDF.
</div>

@endsection
