@extends('layouts.admin')

@section('title', 'TSC-SL — Resultados')
@section('header', 'TSC-SL · Resultados')

@section('header-actions')
    <a href="{{ route('admin.candidates.show', $session->candidate) }}" class="btn-ghost btn-sm">← Candidato</a>
@endsection

@section('content')

@php
$candidate = $session->candidate;
$dims = $session->dimension_scores ?? [];
$dimMeta = [
    'E1' => ['label'=>'Empatía y Escucha Activa',   'color'=>'teal',   'max'=>40],
    'E2' => ['label'=>'Comunicación Efectiva',        'color'=>'sky',    'max'=>40],
    'P1' => ['label'=>'Resolución de Problemas',      'color'=>'violet', 'max'=>42],
    'P2' => ['label'=>'Manejo de Clientes Difíciles', 'color'=>'rose',   'max'=>44],
    'A1' => ['label'=>'Actitud de Servicio',          'color'=>'amber',  'max'=>37],
    'A2' => ['label'=>'Tolerancia a la Presión',      'color'=>'orange', 'max'=>39],
];
$colorBg = [
    'teal'=>'bg-teal-500','sky'=>'bg-sky-500','violet'=>'bg-violet-500',
    'rose'=>'bg-rose-500','amber'=>'bg-amber-500','orange'=>'bg-orange-500',
];
$colorText = [
    'teal'=>'text-teal-700','sky'=>'text-sky-700','violet'=>'text-violet-700',
    'rose'=>'text-rose-700','amber'=>'text-amber-700','orange'=>'text-orange-700',
];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Columna principal --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Puntaje total --}}
        <div class="card">
            <div class="card-body">
                <div class="flex items-start justify-between flex-wrap gap-4 mb-5">
                    <div>
                        <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Resultado final</h2>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold text-slate-900">{{ $session->total_score ?? '—' }}</span>
                            <span class="text-lg text-slate-400">/ 225</span>
                        </div>
                    </div>
                    @if($session->performance_level)
                    <span class="text-sm font-bold border rounded-xl px-4 py-2 {{ $session->performanceLevelColor() }}">
                        {{ $session->performanceLevelLabel() }}
                    </span>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-4 text-center pt-4 border-t border-slate-100">
                    <div>
                        <p class="text-xl font-bold text-slate-800">{{ $session->m1_score ?? '—' }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">M1 SJT / 60</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-slate-800">{{ $session->m2_score ?? '—' }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">M2 Actitudes / 150</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-slate-800">{{ $session->m3_score ?? '—' }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">M3 Escenarios / 15</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Perfil por dimensión --}}
        <div class="card">
            <div class="card-body">
                <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Perfil por dimensión</h2>
                <div class="space-y-4">
                    @foreach($dimMeta as $code => $meta)
                    @php
                        $score = $dims[$code] ?? 0;
                        $pct   = $meta['max'] > 0 ? min(100, round($score / $meta['max'] * 100)) : 0;
                        $bg    = $colorBg[$meta['color']];
                        $tx    = $colorText[$meta['color']];
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold {{ $tx }}">{{ $code }}</span>
                                <span class="text-sm text-slate-700">{{ $meta['label'] }}</span>
                            </div>
                            <span class="text-sm font-bold text-slate-800">{{ $score }}<span class="text-slate-400 font-normal"> / {{ $meta['max'] }}</span></span>
                        </div>
                        <div class="progress-track h-2">
                            <div class="progress-bar {{ $bg }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Gráfico radar --}}
        @php
        $radarPcts = [];
        $radarLabels = [];
        foreach ($dimMeta as $code => $meta) {
            $radarLabels[] = $code;
            $radarPcts[]   = $meta['max'] > 0 ? round(($dims[$code] ?? 0) / $meta['max'] * 100) : 0;
        }
        $radarFullLabels = array_column($dimMeta, 'label');
        @endphp
        <div class="card">
            <div class="card-body">
                <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Mapa de competencias (% del máximo por dimensión)</h2>
                <div class="relative h-72 sm:h-80">
                    <canvas id="radarChart"></canvas>
                </div>
                <p class="text-[10px] text-slate-400 text-center mt-3">Valores expresados como porcentaje del máximo posible en cada dimensión</p>
            </div>
        </div>

        {{-- Calificación M3 --}}
        @if($session->m3_scores)
        <div class="card">
            <div class="card-body">
                <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Calificación Módulo 3 — Escenarios</h2>
                @php
                $scTitles = [1=>'Queja recurrente',2=>'Necesidad no expresada',3=>'Presión extrema'];
                @endphp
                <div class="space-y-5">
                    @foreach([1,2,3] as $n)
                    <div class="border border-slate-100 rounded-lg overflow-hidden">
                        <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-600">Escenario {{ $n }} — {{ $scTitles[$n] }}</span>
                            <span class="text-sm font-bold text-brand-700">{{ $session->m3_scores[$n] ?? $session->m3_scores[(string)$n] ?? '—' }} / 5</span>
                        </div>
                        <div class="p-4 space-y-3">
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Respuesta del candidato</p>
                                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $session->m3_responses[$n] ?? $session->m3_responses[(string)$n] ?? '—' }}</p>
                            </div>
                            <div class="pt-2 border-t border-slate-100">
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Justificación del evaluador</p>
                                <p class="text-xs text-slate-600">{{ $session->m3_scores["just_$n"] ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- Columna lateral --}}
    <div class="space-y-4">

        {{-- Info del candidato --}}
        <div class="card">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Candidato</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500 flex-shrink-0">Nombre</dt>
                        <dd class="font-medium text-slate-800 text-right">{{ $candidate->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500 flex-shrink-0">Cargo</dt>
                        <dd class="text-slate-700 text-right">{{ $candidate->position?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500 flex-shrink-0">Inicio</dt>
                        <dd class="text-slate-700 text-right">{{ $session->started_at?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500 flex-shrink-0">Completado</dt>
                        <dd class="text-slate-700 text-right">{{ $session->completed_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    @if($session->m3Evaluator)
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500 flex-shrink-0">Evaluador M3</dt>
                        <dd class="font-medium text-slate-800 text-right">{{ $session->m3Evaluator->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Tabla de conversión --}}
        <div class="card border-slate-100">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Niveles de desempeño</h3>
                <div class="space-y-1 text-xs">
                    @foreach([
                        ['Sobresaliente','191–225','text-emerald-700 bg-emerald-50'],
                        ['Alto','158–190','text-brand-700 bg-brand-50'],
                        ['Adecuado','124–157','text-amber-700 bg-amber-50'],
                        ['En desarrollo','90–123','text-orange-700 bg-orange-50'],
                        ['Por debajo','0–89','text-red-700 bg-red-50'],
                    ] as [$label,$range,$cls])
                    <div class="flex items-center justify-between gap-2 px-2 py-1 rounded {{ ($session->performanceLevelLabel() === $label || ($label === 'Por debajo' && $session->performance_level === 'por_debajo') || ($label === 'En desarrollo' && $session->performance_level === 'en_desarrollo')) ? $cls.' font-semibold' : '' }}">
                        <span>{{ $label }}</span>
                        <span class="text-slate-400">{{ $range }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Confidencialidad --}}
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-500">
            Uso exclusivo del evaluador · Ley 1581/2012 · Ley 1090/2006
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script nonce="{{ app('csp-nonce') }}">
(function () {
    const labels    = @json($radarLabels);
    const fullNames = @json($radarFullLabels);
    const data      = @json($radarPcts);

    new Chart(document.getElementById('radarChart'), {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: '{{ addslashes($candidate->name) }}',
                data: data,
                backgroundColor: 'rgba(20, 184, 166, 0.12)',
                borderColor: 'rgba(20, 184, 166, 0.75)',
                pointBackgroundColor: 'rgba(20, 184, 166, 0.9)',
                pointBorderColor: '#fff',
                pointRadius: 5,
                pointHoverRadius: 7,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 25,
                        color: '#94a3b8',
                        font: { size: 9 },
                        backdropColor: 'transparent',
                        callback: v => v + '%',
                    },
                    grid: { color: 'rgba(148,163,184,0.25)' },
                    angleLines: { color: 'rgba(148,163,184,0.35)' },
                    pointLabels: {
                        font: { size: 11, weight: '600' },
                        color: '#475569',
                    },
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: ctx => fullNames[ctx[0].dataIndex],
                        label: ctx => ' ' + ctx.raw + '%',
                    }
                }
            }
        }
    });
})();
</script>
@endpush
