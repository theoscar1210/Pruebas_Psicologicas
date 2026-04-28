@extends('layouts.candidate')
@section('title', 'Test de Wartegg — Completado')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-xl mx-auto px-4 py-10 text-center">

    {{-- Ícono de éxito --}}
    <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-slate-900 mb-2">¡Test completado!</h1>
    <p class="text-slate-500 mb-8">Tus dibujos han sido enviados correctamente y serán analizados por el psicólogo evaluador.</p>

    {{-- Resumen --}}
    @if($session)
    <div class="card mb-6 text-left">
        <div class="card-body">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Resumen de la sesión</h2>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $session->completedBoxesCount() }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">campos dibujados</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">
                        @php
                            $mins = $session->total_seconds ? intdiv($session->total_seconds, 60) : 0;
                        @endphp
                        {{ $mins }}<span class="text-base font-normal text-slate-400">min</span>
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">tiempo total</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">
                        {{ $session->completed_at?->format('H:i') ?? '—' }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">hora de envío</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Miniaturas de las cajas completadas --}}
    @if($session && !empty($session->boxes))
    <div class="card mb-8">
        <div class="card-body">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Tus dibujos</p>
            <div class="grid grid-cols-4 gap-3">
                @foreach(range(1,8) as $n)
                @php $box = $session->getBox($n); @endphp
                <div class="text-center">
                    @if(!empty($box['drawing_data']))
                        <img src="{{ $box['drawing_data'] }}"
                             alt="Campo {{ ['I','II','III','IV','V','VI','VII','VIII'][$n-1] }}"
                             class="w-full aspect-square object-cover rounded-lg border border-slate-200 mb-1">
                        @if($box['title'])
                            <p class="text-[10px] text-slate-500 truncate">{{ $box['title'] }}</p>
                        @endif
                    @else
                        <div class="w-full aspect-square rounded-lg border-2 border-dashed border-slate-200 flex items-center justify-center mb-1">
                            <span class="text-xs font-bold text-slate-300 font-mono">{{ ['I','II','III','IV','V','VI','VII','VIII'][$n-1] }}</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Sin dibujo</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Aviso confidencialidad --}}
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-8 text-left flex gap-3">
        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
        </svg>
        <p class="text-xs text-slate-500 leading-relaxed">
            Tus dibujos son confidenciales y solo serán accesibles para el psicólogo evaluador.
            Los resultados serán comunicados por el área de Recursos Humanos.
        </p>
    </div>

    <a href="{{ route('candidate.dashboard') }}" class="btn-primary inline-flex">
        ← Volver a mis pruebas
    </a>
</div>
@endsection
