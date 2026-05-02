@extends('layouts.candidate')

@section('title', 'TTE-SL — Completado')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-xl mx-auto px-4 py-10 sm:py-14 text-center">

    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5">
        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-slate-900 mb-2">¡Prueba enviada!</h1>
    <p class="text-slate-500 text-sm mb-8">Sus respuestas fueron registradas exitosamente. El equipo de Recursos Humanos revisará sus resultados y se comunicará con usted si es necesario.</p>

    <div class="card border-slate-100 mb-6 text-left">
        <div class="card-body">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Resumen de la prueba</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Prueba</dt>
                    <dd class="font-medium text-slate-800">TTE-SL — Test de Trabajo en Equipo</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Módulo 1 (SJT)</dt>
                    <dd class="font-medium text-emerald-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Completado
                    </dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Módulo 2 (Actitudes)</dt>
                    <dd class="font-medium text-emerald-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Completado
                    </dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Módulo 3 (Escenarios)</dt>
                    <dd class="font-medium text-emerald-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enviado — pendiente de revisión
                    </dd>
                </div>
                @if($session->m3_submitted_at)
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Fecha de envío</dt>
                    <dd class="font-medium text-slate-700">{{ $session->m3_submitted_at->format('d/m/Y H:i') }}</dd>
                </div>
                @endif
                @if($session->status === 'completed' && $session->total_score !== null)
                <div class="pt-2 border-t border-slate-100">
                    <div class="flex justify-between gap-2">
                        <dt class="text-slate-500">Estado</dt>
                        <dd>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold border rounded-full px-2.5 py-0.5 {{ $session->performanceLevelColor() }}">
                                {{ $session->performanceLevelLabel() }}
                            </span>
                        </dd>
                    </div>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="bg-brand-50 border border-brand-200 rounded-xl p-4 text-left mb-8">
        <p class="text-xs text-brand-700 leading-relaxed">
            <strong>Confidencialidad:</strong> Sus respuestas son datos personales de naturaleza laboral protegidos por la <strong>Ley 1581 de 2012</strong>. Solo el equipo de selección autorizado tendrá acceso a ellos. Tiene derecho a solicitar retroalimentación orientada al desarrollo al concluir el proceso.
        </p>
    </div>

    <a href="{{ route('candidate.dashboard') }}" class="btn-ghost btn-sm justify-center w-full sm:w-auto">
        ← Volver a mis pruebas
    </a>

</div>
@endsection
