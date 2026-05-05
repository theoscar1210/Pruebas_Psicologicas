@extends('layouts.candidate')

@section('title', 'Mis Pruebas')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
    @if($candidate->position)
        <span class="hidden sm:inline badge-info">{{ $candidate->position->name }}</span>
    @endif
@endsection

@section('content')

<div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">

    {{-- Bienvenida --}}
    <div class="mb-5">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">
            Bienvenido, {{ explode(' ', $candidate->name)[0] }}
        </h1>
        <p class="text-slate-500 text-sm mt-1">
            @php
                $pending   = $candidate->assignments->whereIn('status', ['pending', 'in_progress'])->count();
                $completed = $candidate->assignments->where('status', 'completed')->count();
                $total     = $candidate->assignments->count();
            @endphp
            @if($total === 0)
                No tienes pruebas asignadas aún.
            @elseif($pending > 0)
                Tienes <strong>{{ $pending }}</strong> prueba{{ $pending > 1 ? 's' : '' }} pendiente{{ $pending > 1 ? 's' : '' }} de completar.
            @else
                ¡Completaste todas tus pruebas! ({{ $completed }}/{{ $total }})
            @endif
        </p>
    </div>

    {{-- Progreso general --}}
    @if($total > 0)
    <div class="card mb-5">
        <div class="card-body py-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-700">Progreso general</span>
                <span class="text-sm font-bold text-brand-700">{{ $completed }}/{{ $total }}</span>
            </div>
            <div class="progress-track h-2.5">
                <div class="progress-bar bg-brand-600"
                     style="width: {{ $total > 0 ? round(($completed/$total)*100) : 0 }}%"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Lista de pruebas --}}
    <div class="space-y-4">

        @forelse($candidate->assignments->sortBy('status') as $assignment)

        @php
            $isExpired    = $assignment->isExpired();
            $isCompleted  = $assignment->isCompleted();
            $isInProgress = $assignment->isInProgress();
            $isPending    = $assignment->isPending();
        @endphp

        <div class="card overflow-hidden {{ $isCompleted ? 'opacity-75' : '' }}">

            {{-- Barra de color superior — completada siempre en teal, no revela aprobado/reprobado --}}
            <div class="h-1 w-full
                @if($isCompleted)      bg-brand-400
                @elseif($isInProgress) bg-amber-400
                @elseif($isExpired)    bg-slate-300
                @else                  bg-brand-300 @endif">
            </div>

            <div class="p-4 sm:p-5 flex items-start justify-between gap-4">

                {{-- Info de la prueba --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h2 class="font-semibold text-slate-900 text-[15px]">{{ $assignment->test->name }}</h2>

                        @if($isCompleted)
                            <span class="badge-success">Completada</span>
                        @elseif($isInProgress)
                            <span class="badge-warning animate-pulse">En progreso</span>
                        @elseif($isExpired)
                            <span class="badge-danger">Expirada</span>
                        @else
                            <span class="badge-info">Pendiente</span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400 mt-1.5">
                        @if($assignment->test->time_limit)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $assignment->test->time_limit }} minutos
                            </span>
                        @else
                            <span>Sin límite de tiempo</span>
                        @endif

                        @if($assignment->expires_at && !$isCompleted)
                            <span class="flex items-center gap-1 {{ $assignment->expires_at->isPast() ? 'text-red-400' : '' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Límite: {{ $assignment->expires_at->format('d/m/Y') }}
                            </span>
                        @endif

                        @if($isInProgress && $assignment->time_remaining)
                            <span class="text-amber-600 font-medium">
                                {{ gmdate('H:i:s', $assignment->time_remaining) }} restantes
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Resultado o botón de acción --}}
                <div class="flex-shrink-0">
                    @if($assignment->test->test_type === 'tsc_sl')
                        {{-- TSC-SL: flujo de 3 módulos --}}
                        @php
                            $tscSession = $candidate->tscSlSessions
                                ->firstWhere('assignment_id', $assignment->id);
                            $tscStatus  = $tscSession?->status ?? 'pending';
                        @endphp
                        @if(in_array($tscStatus, ['completed','m3_submitted']))
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700
                                         bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $tscStatus === 'completed' ? 'Evaluado' : 'Enviado' }}
                            </span>
                        @elseif($tscStatus === 'm2_done')
                            <a href="{{ route('candidate.tsc-sl.module3', $assignment) }}"
                               class="btn-warning btn-sm">
                                Módulo 3
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @elseif($tscStatus === 'm1_done')
                            <a href="{{ route('candidate.tsc-sl.module2', $assignment) }}"
                               class="btn-warning btn-sm">
                                Módulo 2
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('candidate.tsc-sl.start', $assignment) }}"
                               class="{{ $tscSession ? 'btn-warning' : 'btn-primary' }} btn-sm">
                                {{ $tscSession ? 'Continuar' : 'Iniciar' }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    @elseif($assignment->test->test_type === 'tsc_sl_h')
                        {{-- TSC-SL Hospitalidad: flujo de 3 módulos --}}
                        @php
                            $tscHSession = $candidate->tscSlSessions
                                ->firstWhere('assignment_id', $assignment->id);
                            $tscHStatus  = $tscHSession?->status ?? 'pending';
                        @endphp
                        @if(in_array($tscHStatus, ['completed','m3_submitted']))
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700
                                         bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $tscHStatus === 'completed' ? 'Evaluado' : 'Enviado' }}
                            </span>
                        @elseif($tscHStatus === 'm2_done')
                            <a href="{{ route('candidate.tsc-sl-h.module3', $assignment) }}"
                               class="btn-warning btn-sm">
                                Módulo 3
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @elseif($tscHStatus === 'm1_done')
                            <a href="{{ route('candidate.tsc-sl-h.module2', $assignment) }}"
                               class="btn-warning btn-sm">
                                Módulo 2
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('candidate.tsc-sl-h.start', $assignment) }}"
                               class="{{ $tscHSession ? 'btn-warning' : 'btn-primary' }} btn-sm">
                                {{ $tscHSession ? 'Continuar' : 'Iniciar' }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    @elseif($assignment->test->test_type === 'tte_sl')
                        {{-- TTE-SL: flujo de 3 módulos --}}
                        @php
                            $tteSession = $candidate->tteSlSessions
                                ->firstWhere('assignment_id', $assignment->id);
                            $tteStatus  = $tteSession?->status ?? 'pending';
                        @endphp
                        @if(in_array($tteStatus, ['completed','m3_submitted']))
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700
                                         bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $tteStatus === 'completed' ? 'Evaluado' : 'Enviado' }}
                            </span>
                        @elseif($tteStatus === 'm2_done')
                            <a href="{{ route('candidate.tte-sl.module3', $assignment) }}"
                               class="btn-warning btn-sm">
                                Módulo 3
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @elseif($tteStatus === 'm1_done')
                            <a href="{{ route('candidate.tte-sl.module2', $assignment) }}"
                               class="btn-warning btn-sm">
                                Módulo 2
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('candidate.tte-sl.start', $assignment) }}"
                               class="{{ $tteSession ? 'btn-warning' : 'btn-primary' }} btn-sm">
                                {{ $tteSession ? 'Continuar' : 'Iniciar' }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    @elseif($assignment->test->test_type === 'wartegg')
                        {{-- Wartegg digital: el candidato dibuja --}}
                        @php
                            $wSession = $candidate->warteggSessions
                                ->firstWhere('assignment_id', $assignment->id);
                            $wDone = $wSession && $wSession->status === 'completed';
                            $evalDoneW = $candidate->evaluatorAssessments
                                ->firstWhere('assessment_type', 'wartegg');
                        @endphp
                        @if($evalDoneW)
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700
                                         bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Evaluado
                            </span>
                        @elseif($wDone)
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700
                                         bg-brand-50 border border-brand-200 rounded-full px-3 py-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Dibujos enviados
                            </span>
                        @else
                            <a href="{{ route('candidate.wartegg.start', $assignment) }}"
                               class="btn-primary btn-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                {{ $wSession && $wSession->status === 'in_progress' ? 'Continuar' : 'Iniciar' }}
                            </a>
                        @endif
                    @elseif($assignment->test->evaluator_scored)
                        @php
                            $evalDone = $candidate->evaluatorAssessments
                                ->firstWhere('assessment_type', $assignment->test->test_type);
                        @endphp
                        @if($evalDone)
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700
                                     bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Evaluado
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-violet-700
                                     bg-violet-50 border border-violet-200 rounded-full px-3 py-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Administrada por el evaluador
                        </span>
                        @endif

                    @elseif($isCompleted && $assignment->result)
                        <div class="text-center">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700
                                         bg-brand-50 border border-brand-200 rounded-full px-3 py-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                Entregada
                            </span>
                            <a href="{{ route('candidate.result', $assignment) }}"
                               class="text-xs text-brand-600 hover:underline mt-2 block">Ver confirmación</a>
                        </div>

                    @elseif(!$isExpired && ($isPending || $isInProgress))
                        <a href="{{ route('candidate.start', $assignment) }}"
                           class="{{ $isInProgress ? 'btn-warning' : 'btn-primary' }} btn-sm">
                            {{ $isInProgress ? 'Retomar' : 'Iniciar' }}
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                    @elseif($isExpired)
                        <span class="text-xs text-slate-400">No disponible</span>
                    @endif
                </div>

            </div>
        </div>

        @empty
        <div class="card border-dashed">
            <div class="card-body py-12 text-center">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-slate-500 text-sm font-medium">No tienes pruebas asignadas</p>
                <p class="text-slate-400 text-xs mt-1">El área de Recursos Humanos te asignará las pruebas pronto.</p>
            </div>
        </div>
        @endforelse

    </div>

    {{-- Footer legal --}}
    <div class="mt-10 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
        <p>Uso confidencial · Ley 1581 de 2012 · Ley 1090 de 2006</p>
        <div class="flex items-center gap-4">
            <a href="{{ route('privacy') }}" target="_blank" class="hover:text-slate-600 underline">Política de privacidad</a>
            <a href="{{ route('candidate.data-deletion') }}" class="hover:text-red-600 text-red-400">Solicitar eliminación de mis datos</a>
        </div>
    </div>

</div>

@endsection
