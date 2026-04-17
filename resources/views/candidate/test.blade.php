@extends('layouts.candidate')
@section('title', $assignment->test->name)

@php
    $isRaven   = $assignment->test->test_type === 'raven';
    $totalQ    = $assignment->test->questions->count();
    $savedAnswers = $assignment->answers->mapWithKeys(
        fn($a) => [$a->question_id => $a->question_option_id ?? $a->text_answer]
    );

    // Metadatos para el renderer Raven: IDs de preguntas y opciones en orden
    $ravenMeta = [];
    if ($isRaven) {
        foreach ($assignment->test->questions->sortBy('order') as $q) {
            $ravenMeta[] = [
                'qId'       => $q->id,
                'optionIds' => $q->options->sortBy('order')->pluck('id')->values()->toArray(),
            ];
        }
    }
@endphp

@section('content')

@if($isRaven)
{{-- ════════════════════════════════════════════════════════════════════
     MODO RAVEN — Renderer SVG ítem a ítem
     Motor gráfico portado de raven_progressive_matrices_test.html
════════════════════════════════════════════════════════════════════ --}}

<style>
.raven-grid{display:grid;grid-template-columns:repeat(3,90px);grid-template-rows:repeat(3,90px);border:2.5px solid #0F766E;width:fit-content;margin:0 auto;border-radius:4px;overflow:hidden}
.raven-cell{width:90px;height:90px;border:0.5px solid #cbd5e1;display:flex;align-items:center;justify-content:center;background:#fff}
.raven-cell.raven-empty{background:#f0fdfa;position:relative}
.raven-q{position:absolute;font-size:28px;font-weight:700;color:#0F766E;opacity:.45}
.raven-opts{display:grid;grid-template-columns:repeat(6,1fr);gap:6px}
.raven-opt{border:1.5px solid #e2e8f0;border-radius:10px;padding:5px 3px;cursor:pointer;display:flex;flex-direction:column;align-items:center;background:#fff;transition:border-color .1s,background .1s;width:100%}
.raven-opt:hover{border-color:#0F766E;background:#f0fdfa}
.raven-opt.sel{border-color:#0F766E;background:rgba(15,118,110,.09)}
.raven-opt-key{font-size:10px;font-weight:700;color:#64748b;margin-top:2px}
.raven-opt.sel .raven-opt-key{color:#0F766E}
</style>

<div
    class="min-h-screen bg-slate-50"
    x-data="ravenApp({
        saveUrl: '{{ route('candidate.answer', $assignment) }}',
        timeRemaining: {{ $assignment->time_remaining ?? ($assignment->test->time_limit ? $assignment->test->time_limit * 60 : 'null') }},
        ravenQuestions: {!! json_encode($ravenMeta) !!},
        savedAnswers: {!! json_encode($savedAnswers) !!},
        totalQuestions: {{ $totalQ }}
    })"
    x-init="init()"
>

    {{-- ── Topbar ────────────────────────────────────────────────────── --}}
    <div class="sticky top-0 z-30 bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-2xl mx-auto px-4 py-3">
            <div class="flex items-center gap-4">

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $assignment->test->name }}</p>
                    <p class="text-xs text-slate-400">{{ $assignment->candidate->name }}</p>
                </div>

                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="font-semibold text-slate-700" x-text="current + 1"></span>
                    <span class="text-slate-300">/</span>
                    <span>{{ $totalQ }}</span>
                    <div class="progress-track w-16 h-1.5 ml-1">
                        <div class="progress-bar bg-brand-500"
                             :style="`width:${Math.round(((current+1)/{{ $totalQ }})*100)}%`"></div>
                    </div>
                </div>

                @if($assignment->test->time_limit)
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-mono font-semibold transition-all"
                     :class="{
                         'bg-amber-50 text-amber-600': timeRemaining <= 300 && timeRemaining > 60,
                         'bg-red-50 text-red-600 animate-pulse': timeRemaining <= 60,
                         'bg-slate-100 text-slate-600': timeRemaining > 300
                     }">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="formatTime(timeRemaining)"></span>
                </div>
                @endif

                <div class="hidden sm:flex items-center gap-1 text-xs transition-all"
                     :class="saving ? 'text-amber-500' : 'text-emerald-500'">
                    <svg x-show="!saving" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="saving" x-cloak class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </div>

            </div>

            {{-- Progress bar total --}}
            <div class="progress-track h-1 mt-2.5 -mx-4 px-0 rounded-none">
                <div class="progress-bar bg-brand-500 h-1 rounded-none"
                     :style="`width:${Math.round((answeredCount/{{ $totalQ }})*100)}%`"></div>
            </div>
        </div>
    </div>

    {{-- ── Contenido principal ───────────────────────────────────────── --}}
    <div class="max-w-xl mx-auto px-4 py-6 pb-32">

        {{-- Set label + mini-mapa de ítems --}}
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold tracking-widest text-slate-400 uppercase" x-text="setLabel"></span>
            <div class="flex gap-1">
                @for($i = 0; $i < $totalQ; $i++)
                <button
                    @click="goTo({{ $i }})"
                    type="button"
                    class="w-5 h-5 rounded-full text-[9px] font-bold transition-all"
                    :class="{
                        'bg-brand-600 text-white scale-110': current === {{ $i }},
                        'bg-emerald-400 text-white': current !== {{ $i }} && isAnswered({{ $i }}),
                        'bg-slate-200 text-slate-400': current !== {{ $i }} && !isAnswered({{ $i }})
                    }"
                    title="Ítem {{ $i + 1 }}">{{ $i + 1 }}</button>
                @endfor
            </div>
        </div>

        {{-- Matriz SVG --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-4 flex flex-col items-center">
            <div id="raven-grid" class="raven-grid"></div>
        </div>

        {{-- Opciones A–F --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <p class="text-xs font-medium text-slate-500 mb-3 text-center">
                ¿Cuál de las seis opciones completa correctamente el patrón?
            </p>
            <div id="raven-opts" class="raven-opts"></div>

            {{-- Indicador respondida --}}
            <div class="flex justify-center mt-3">
                <span class="text-xs font-medium transition-colors"
                      :class="isAnswered(current) ? 'text-emerald-500' : 'text-slate-300'">
                    <template x-if="isAnswered(current)">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Respondida
                        </span>
                    </template>
                    <template x-if="!isAnswered(current)">
                        <span>Sin responder</span>
                    </template>
                </span>
            </div>
        </div>

        {{-- Navegación --}}
        <div class="flex justify-between items-center mt-4 gap-3">
            <button
                @click="prev()"
                :disabled="current === 0"
                type="button"
                class="btn-secondary btn-sm"
                :class="{ 'opacity-40 cursor-not-allowed': current === 0 }">
                ← Anterior
            </button>
            <button
                @click="next()"
                type="button"
                class="btn-primary btn-sm"
                x-text="current === {{ $totalQ - 1 }} ? 'Ver resultados →' : 'Siguiente →'">
            </button>
        </div>

    </div>

    {{-- ── Barra inferior fija ───────────────────────────────────────── --}}
    <div class="fixed bottom-0 inset-x-0 z-30 bg-white border-t border-slate-100 shadow-card-lg">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <div class="text-sm text-slate-500">
                <span class="font-semibold text-slate-800" x-text="answeredCount"></span>
                de {{ $totalQ }} respondidas
            </div>
            <button @click="showModal = true" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Finalizar prueba
            </button>
        </div>
    </div>

    {{-- ── Modal confirmación ───────────────────────────────────────── --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 pb-4 sm:pb-0"
         @keydown.escape.window="showModal = false">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
             @click="showModal = false"
             x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"></div>
        <div class="relative bg-white rounded-modal shadow-modal w-full max-w-sm p-7 text-center animate-slide-up">
            <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">¿Finalizar la prueba?</h3>
            <p class="text-sm text-slate-500 mb-1">
                Has respondido <strong x-text="answeredCount" class="text-slate-800"></strong>
                de <strong class="text-slate-800">{{ $totalQ }}</strong> preguntas.
            </p>
            <p class="text-xs text-slate-400 mb-6">Esta acción no se puede deshacer.</p>
            <div class="flex gap-3">
                <button @click="showModal = false" class="btn-secondary flex-1">Seguir revisando</button>
                <form id="finish-form" method="POST"
                      action="{{ route('candidate.finish', $assignment) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn-primary w-full justify-center">Sí, finalizar</button>
                </form>
            </div>
        </div>
    </div>

</div>

@else
{{-- ════════════════════════════════════════════════════════════════════
     MODO ESTÁNDAR — Big Five, 16PF, Assessment Center, etc.
════════════════════════════════════════════════════════════════════ --}}

<div
    class="min-h-screen bg-slate-50"
    x-data="testApp({
        assignmentId: {{ $assignment->id }},
        saveUrl: '{{ route('candidate.answer', $assignment) }}',
        timeRemaining: {{ $assignment->time_remaining ?? 'null' }},
        totalQuestions: {{ $totalQ }},
        savedAnswers: {!! json_encode($savedAnswers) !!}
    })"
    x-init="init()"
>

    {{-- ── Topbar fija ─────────────────────────────────────────────────── --}}
    <div class="sticky top-0 z-30 bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-2xl mx-auto px-4 py-3">
            <div class="flex items-center gap-4">

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 truncate">
                        {{ $assignment->test->name }}
                    </p>
                    <p class="text-xs text-slate-400">{{ $assignment->candidate->name }}</p>
                </div>

                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
                    <span>
                        <span x-text="answeredCount" class="font-semibold text-slate-700"></span>
                        / {{ $totalQ }}
                    </span>
                    <div class="progress-track w-20 h-1.5">
                        <div class="progress-bar bg-brand-500"
                             :style="`width:${Math.round((answeredCount/{{ $totalQ }})*100)}%`">
                        </div>
                    </div>
                </div>

                @if($assignment->test->time_limit)
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-mono font-semibold transition-all"
                     :class="{
                         'bg-amber-50 text-amber-600': timeRemaining <= 300 && timeRemaining > 60,
                         'bg-red-50 text-red-600 animate-pulse': timeRemaining <= 60,
                         'bg-slate-100 text-slate-600': timeRemaining > 300
                     }">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="formatTime(timeRemaining)"></span>
                </div>
                @endif

                <div class="hidden sm:flex items-center gap-1 text-xs transition-all"
                     :class="saving ? 'text-amber-500' : 'text-emerald-500'">
                    <svg x-show="!saving" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="saving" x-cloak class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="saving ? 'Guardando…' : 'Guardado'" x-cloak class="hidden sm:inline"></span>
                </div>

            </div>

            <div class="progress-track h-1 mt-2.5 -mx-4 px-0 rounded-none">
                <div class="progress-bar bg-brand-500 h-1 rounded-none"
                     :style="`width:${Math.round((answeredCount/{{ $totalQ }})*100)}%`">
                </div>
            </div>
        </div>
    </div>

    {{-- ── Instrucciones ───────────────────────────────────────────────── --}}
    @if($assignment->test->instructions)
    <div class="max-w-2xl mx-auto px-4 mt-6">
        <div class="card-info p-4">
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-brand-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-xs font-semibold text-brand-700 mb-1">Instrucciones</p>
                    <p class="text-sm text-brand-800/80 leading-relaxed whitespace-pre-line">{{ $assignment->test->instructions }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Preguntas ───────────────────────────────────────────────────── --}}
    <div class="max-w-2xl mx-auto px-4 py-6 space-y-5 pb-32">

        @foreach($assignment->test->questions as $question)
        <div class="card p-6 animate-fade-in" id="q-{{ $question->id }}">

            <div class="flex items-start gap-3 mb-5">
                <span class="w-7 h-7 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center
                             text-xs font-bold flex-shrink-0 mt-0.5">
                    {{ $question->order }}
                </span>
                <div class="flex-1">
                    <p class="text-[15px] font-medium text-slate-800 leading-relaxed">
                        {{ $question->text }}
                    </p>
                    @if($question->is_required)
                        <p class="text-[11px] text-slate-400 mt-1">Obligatoria</p>
                    @endif
                </div>
            </div>

            {{-- ── OPCIÓN MÚLTIPLE ─────────────────────────────────────── --}}
            @if($question->type === 'multiple_choice')
            <div class="space-y-2 sm:ml-10">
                @foreach($question->options as $option)
                <label
                    class="flex items-center gap-3 px-4 py-3 rounded-xl border-[1.5px] cursor-pointer transition-all duration-150"
                    :class="answers[{{ $question->id }}] == {{ $option->id }}
                        ? 'border-brand-500 bg-brand-50 shadow-sm'
                        : 'border-slate-200 bg-slate-50/50 hover:border-brand-300 hover:bg-brand-50/40'">
                    <input
                        type="radio"
                        name="q{{ $question->id }}"
                        value="{{ $option->id }}"
                        class="sr-only"
                        :checked="answers[{{ $question->id }}] == {{ $option->id }}"
                        @change="saveAnswer({{ $question->id }}, {{ $option->id }}, null)">
                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                         :class="answers[{{ $question->id }}] == {{ $option->id }}
                            ? 'border-brand-500 bg-brand-500'
                            : 'border-slate-300'">
                        <div class="w-1.5 h-1.5 rounded-full bg-white transition-all"
                             :class="answers[{{ $question->id }}] == {{ $option->id }} ? 'opacity-100' : 'opacity-0'">
                        </div>
                    </div>
                    <span class="text-sm text-slate-700 leading-relaxed">{{ $option->text }}</span>
                </label>
                @endforeach
            </div>

            {{-- ── ESCALA LIKERT ───────────────────────────────────────── --}}
            @elseif($question->type === 'likert')
            <div class="sm:ml-10">
                <div class="likert-group">
                    @foreach($question->options as $option)
                    <label
                        class="likert-option"
                        :class="answers[{{ $question->id }}] == {{ $option->id }} ? 'likert-{{ $option->value }}' : ''">
                        <input
                            type="radio"
                            name="q{{ $question->id }}"
                            value="{{ $option->id }}"
                            class="sr-only"
                            :checked="answers[{{ $question->id }}] == {{ $option->id }}"
                            @change="saveAnswer({{ $question->id }}, {{ $option->id }}, null)">
                        <div class="likert-card">
                            <div class="likert-circle">{{ $option->value }}</div>
                            <span class="likert-label">{{ $option->text }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                <div class="hidden sm:flex justify-between text-[10px] text-slate-400 mt-1 px-1">
                    <span>← Menos</span>
                    <span>Más →</span>
                </div>
            </div>

            {{-- ── PREGUNTA ABIERTA ────────────────────────────────────── --}}
            @elseif($question->type === 'open')
            <div class="sm:ml-10">
                <textarea
                    name="q{{ $question->id }}_text"
                    rows="4"
                    placeholder="Escribe tu respuesta aquí…"
                    x-model="textAnswers[{{ $question->id }}]"
                    @input.debounce.800ms="saveAnswer({{ $question->id }}, null, textAnswers[{{ $question->id }}])"
                    class="textarea">{{ $assignment->answers->firstWhere('question_id', $question->id)?->text_answer }}</textarea>
            </div>
            @endif

            <div class="flex justify-end mt-4 sm:ml-10">
                <span class="text-xs font-medium transition-colors"
                      :class="answers[{{ $question->id }}] || textAnswers[{{ $question->id }}]
                        ? 'text-emerald-500' : 'text-slate-300'">
                    <template x-if="answers[{{ $question->id }}] || textAnswers[{{ $question->id }}]">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Respondida
                        </span>
                    </template>
                    <template x-if="!answers[{{ $question->id }}] && !textAnswers[{{ $question->id }}]">
                        <span>Sin responder</span>
                    </template>
                </span>
            </div>

        </div>
        @endforeach
    </div>

    {{-- ── Barra inferior fija ─────────────────────────────────────────── --}}
    <div class="fixed bottom-0 inset-x-0 z-30 bg-white border-t border-slate-100 shadow-card-lg">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <div class="text-sm text-slate-500">
                <span class="font-semibold text-slate-800" x-text="answeredCount"></span>
                de {{ $totalQ }} respondidas
            </div>
            <button
                @click="showModal = true"
                class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Finalizar prueba
            </button>
        </div>
    </div>

    {{-- ── Modal de confirmación ───────────────────────────────────────── --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 pb-4 sm:pb-0"
         @keydown.escape.window="showModal = false">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
             @click="showModal = false"
             x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"></div>
        <div class="relative bg-white rounded-modal shadow-modal w-full max-w-sm p-7 text-center animate-slide-up">
            <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">¿Finalizar la prueba?</h3>
            <p class="text-sm text-slate-500 mb-1">
                Has respondido <strong x-text="answeredCount" class="text-slate-800"></strong>
                de <strong class="text-slate-800">{{ $totalQ }}</strong> preguntas.
            </p>
            <p class="text-xs text-slate-400 mb-6">Esta acción no se puede deshacer.</p>
            <div class="flex gap-3">
                <button @click="showModal = false" class="btn-secondary flex-1">
                    Seguir revisando
                </button>
                <form method="POST"
                      action="{{ route('candidate.finish', $assignment) }}"
                      class="flex-1">
                    @csrf
                    <button type="submit" class="btn-primary w-full justify-center">
                        Sí, finalizar
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endif

{{-- ════════════════════════════════════════════════════════════════════
     SCRIPTS — Motor SVG Raven + Alpine components
════════════════════════════════════════════════════════════════════ --}}
<script>
// ─── Motor gráfico SVG (portado de raven_progressive_matrices_test.html) ───
const _=(t,f=3,s=26,x=50,y=50,r=0)=>({t,f,s,x,y,r});
const FILLS=['#fff','#ccc','#888','#1a1a1a'];
const SK='#1a1a1a',SW=2.2;

function shp(sh,K){
  const{t,f=3,s=26,x=50,y=50,r=0}=sh;
  const cx=x*K,cy=y*K,sz=s*K,fill=FILLS[Math.min(f,3)];
  const tr=r?` transform="rotate(${r},${cx},${cy})"`:'';
  if(t==='C')return`<circle cx="${cx}" cy="${cy}" r="${sz}" fill="${fill}" stroke="${SK}" stroke-width="${SW}"/>`;
  if(t==='S')return`<rect x="${cx-sz}" y="${cy-sz}" width="${sz*2}" height="${sz*2}" fill="${fill}" stroke="${SK}" stroke-width="${SW}"${tr}/>`;
  if(t==='T'){const p=`${cx},${cy-sz} ${cx+sz*.866},${cy+sz*.5} ${cx-sz*.866},${cy+sz*.5}`;return`<polygon points="${p}" fill="${fill}" stroke="${SK}" stroke-width="${SW}"${tr}/>`;}
  if(t==='D'){const p=`${cx},${cy-sz} ${cx+sz*.68},${cy} ${cx},${cy+sz} ${cx-sz*.68},${cy}`;return`<polygon points="${p}" fill="${fill}" stroke="${SK}" stroke-width="${SW}"${tr}/>`;}
  if(t==='P'){const pts=Array.from({length:5},(_,i)=>{const a=(i*72-90)*Math.PI/180;return`${cx+sz*Math.cos(a)},${cy+sz*Math.sin(a)}`;}).join(' ');return`<polygon points="${pts}" fill="${fill}" stroke="${SK}" stroke-width="${SW}"${tr}/>`;}
  if(t==='H'){const pts=Array.from({length:6},(_,i)=>{const a=(i*60-30)*Math.PI/180;return`${cx+sz*Math.cos(a)},${cy+sz*Math.sin(a)}`;}).join(' ');return`<polygon points="${pts}" fill="${fill}" stroke="${SK}" stroke-width="${SW}"${tr}/>`;}
  if(t==='X'){const w=sz*.32;return`<g${tr}><rect x="${cx-w}" y="${cy-sz}" width="${w*2}" height="${sz*2}" fill="${fill}" stroke="${SK}" stroke-width="${SW*.4}"/><rect x="${cx-sz}" y="${cy-w}" width="${sz*2}" height="${w*2}" fill="${fill}" stroke="${SK}" stroke-width="${SW*.4}"/></g>`;}
  if(t==='A'){const bw=sz*.35,hw=sz*.65,bl=sz*1.1,tl=sz*1.7,h=tl/2;const p=`${cx-h},${cy-bw} ${cx+bl-h},${cy-bw} ${cx+bl-h},${cy-hw} ${cx+h},${cy} ${cx+bl-h},${cy+hw} ${cx+bl-h},${cy+bw} ${cx-h},${cy+bw}`;return`<polygon points="${p}" fill="${fill}" stroke="${SK}" stroke-width="${SW}"${tr}/>`;}
  if(t==='L'){const lc=f===0?'#fff':f===1?'#bbb':f===2?'#777':'#1a1a1a';return`<line x1="${cx}" y1="${cy-sz}" x2="${cx}" y2="${cy+sz}" stroke="${lc}" stroke-width="${SW*1.8}" stroke-linecap="round"${tr}/>`;}
  return'';
}

function cellSVG(shapes,px){
  if(!shapes)return'';
  const K=px/100;
  return`<svg width="${px}" height="${px}" viewBox="0 0 ${px} ${px}" style="display:block">${shapes.map(s=>shp(s,K)).join('')}</svg>`;
}

// ─── Datos de los 20 ítems MPR-SL (matrices + opciones) ─────────────────────
const ITEMS=[
{id:1,set:'A',label:'A-1',matrix:[[[_('T',0)],[_('T',2)],[_('T',3)]],[[_('S',0)],[_('S',2)],[_('S',3)]],[[_('C',0)],[_('C',2)],null]],ans:0,opts:[[_('C',3)],[_('C',2)],[_('C',0)],[_('S',3)],[_('T',3)],[_('D',3)]]},
{id:2,set:'A',label:'A-2',matrix:[[[_('C',3,13)],[_('C',3,22)],[_('C',3,34)]],[[_('S',3,13)],[_('S',3,22)],[_('S',3,34)]],[[_('D',3,13)],[_('D',3,22)],null]],ans:0,opts:[[_('D',3,34)],[_('D',3,22)],[_('D',3,13)],[_('C',3,34)],[_('S',3,34)],[_('T',3,34)]]},
{id:3,set:'A',label:'A-3',matrix:[[[_('C',3,17,50,50)],[_('C',3,14,32,50),_('C',3,14,68,50)],[_('C',3,12,20,50),_('C',3,12,50,50),_('C',3,12,80,50)]],[[_('S',3,17,50,50)],[_('S',3,14,32,50),_('S',3,14,68,50)],[_('S',3,12,20,50),_('S',3,12,50,50),_('S',3,12,80,50)]],[[_('T',3,17,50,50)],[_('T',3,14,32,50),_('T',3,14,68,50)],null]],ans:2,opts:[[_('T',3,17,50,50)],[_('T',3,14,32,50),_('T',3,14,68,50)],[_('T',3,12,20,50),_('T',3,12,50,50),_('T',3,12,80,50)],[_('C',3,12,20,50),_('C',3,12,50,50),_('C',3,12,80,50)],[_('S',3,12,20,50),_('S',3,12,50,50),_('S',3,12,80,50)],[_('D',3,12,20,50),_('D',3,12,50,50),_('D',3,12,80,50)]]},
{id:4,set:'A',label:'A-4',matrix:[[[_('T',3,26,50,50,0)],[_('T',3,26,50,50,90)],[_('T',3,26,50,50,180)]],[[_('T',3,26,50,50,90)],[_('T',3,26,50,50,180)],[_('T',3,26,50,50,270)]],[[_('T',3,26,50,50,180)],[_('T',3,26,50,50,270)],null]],ans:0,opts:[[_('T',3,26,50,50,0)],[_('T',3,26,50,50,90)],[_('T',3,26,50,50,180)],[_('T',3,26,50,50,270)],[_('C',3,26)],[_('S',3,26)]]},
{id:5,set:'A',label:'A-5',matrix:[[[_('C',3,11,25,25)],[_('C',3,11,50,25)],[_('C',3,11,75,25)]],[[_('C',3,11,25,50)],[_('C',3,11,50,50)],[_('C',3,11,75,50)]],[[_('C',3,11,25,75)],[_('C',3,11,50,75)],null]],ans:1,opts:[[_('C',3,11,25,75)],[_('C',3,11,75,75)],[_('C',3,11,50,50)],[_('C',3,11,75,25)],[_('C',3,11,25,25)],[_('C',3,11,75,50)]]},
{id:6,set:'A',label:'A-6',matrix:[[[_('C',3)],[_('S',3)],[_('T',3)]],[[_('S',3)],[_('T',3)],[_('C',3)]],[[_('T',3)],[_('C',3)],null]],ans:2,opts:[[_('T',3)],[_('C',3)],[_('S',3)],[_('D',3)],[_('P',3)],[_('H',3)]]},
{id:7,set:'A',label:'A-7',matrix:[[[_('A',3,22,50,50,0)],[_('A',3,22,50,50,90)],[_('A',3,22,50,50,180)]],[[_('A',3,22,50,50,90)],[_('A',3,22,50,50,180)],[_('A',3,22,50,50,270)]],[[_('A',3,22,50,50,180)],[_('A',3,22,50,50,270)],null]],ans:3,opts:[[_('A',3,22,50,50,90)],[_('A',3,22,50,50,180)],[_('A',3,22,50,50,270)],[_('A',3,22,50,50,0)],[_('T',3,22,50,50,0)],[_('A',3,22,50,50,45)]]},
{id:8,set:'B',label:'B-1',matrix:[[[_('C',0,14)],[_('C',2,24)],[_('C',3,36)]],[[_('P',0,14)],[_('P',2,24)],[_('P',3,36)]],[[_('H',0,14)],[_('H',2,24)],null]],ans:4,opts:[[_('H',0,36)],[_('H',2,36)],[_('H',3,24)],[_('H',0,14)],[_('H',3,36)],[_('C',3,36)]]},
{id:9,set:'B',label:'B-2',matrix:[[[_('C',3,20)],[_('S',0,20)],[_('C',3,18,33,33),_('S',0,18,67,67)]],[[_('T',3,20)],[_('D',0,20)],[_('T',3,18,33,33),_('D',0,18,67,67)]],[[_('P',3,20)],[_('H',0,20)],null]],ans:0,opts:[[_('P',3,18,33,33),_('H',0,18,67,67)],[_('P',3,20)],[_('H',0,20)],[_('H',3,18,33,33),_('P',0,18,67,67)],[_('P',0,18,33,33),_('H',3,18,67,67)],[_('C',3,18,33,33),_('H',0,18,67,67)]]},
{id:10,set:'B',label:'B-3',matrix:[[[_('T',3,14,28,28),_('C',0,14,72,28),_('S',2,14,50,73)],[_('T',3,17,36,36),_('C',0,17,64,64)],[_('T',3,22)]],[[_('C',0,14,28,28),_('S',2,14,72,28),_('D',3,14,50,73)],[_('C',0,17,36,36),_('S',2,17,64,64)],[_('C',0,22)]],[[_('S',2,14,28,28),_('D',3,14,72,28),_('T',0,14,50,73)],[_('S',2,17,36,36),_('D',3,17,64,64)],null]],ans:2,opts:[[_('D',3,22)],[_('T',0,22)],[_('S',2,22)],[_('C',0,22)],[_('S',3,22)],[_('S',2,17,36,36),_('D',3,17,64,64)]]},
{id:11,set:'B',label:'B-4',matrix:[[[_('L',3,30,50,50,0)],[_('L',3,30,50,50,30)],[_('L',3,30,50,50,60)]],[[_('L',3,30,50,50,30)],[_('L',3,30,50,50,60)],[_('L',3,30,50,50,90)]],[[_('L',3,30,50,50,60)],[_('L',3,30,50,50,90)],null]],ans:0,opts:[[_('L',3,30,50,50,120)],[_('L',3,30,50,50,90)],[_('L',3,30,50,50,60)],[_('L',3,30,50,50,0)],[_('L',3,30,50,50,150)],[_('C',3,26)]]},
{id:12,set:'B',label:'B-5',matrix:[[[_('C',0,26)],[_('S',3,26)],[_('C',3,26)]],[[_('T',0,26)],[_('D',3,26)],[_('T',3,26)]],[[_('P',0,26)],[_('H',3,26)],null]],ans:1,opts:[[_('H',3,26)],[_('P',3,26)],[_('P',0,26)],[_('D',3,26)],[_('P',2,26)],[_('H',0,26)]]},
{id:13,set:'B',label:'B-6',matrix:[[[_('C',3)],[_('C',0)],[_('C',2)]],[[_('C',0)],[_('C',2)],[_('C',3)]],[[_('C',2)],[_('C',3)],null]],ans:2,opts:[[_('C',3)],[_('C',2)],[_('C',0)],[_('C',1)],[_('S',0)],[_('D',0)]]},
{id:14,set:'B',label:'B-7',matrix:[[[_('C',3,11)],[_('C',3,11,32,50),_('C',3,11,68,50)],[_('C',3,11,20,50),_('C',3,11,50,50),_('C',3,11,80,50)]],[[_('C',3,11,50,33),_('C',3,11,50,67)],[_('C',3,11,32,33),_('C',3,11,68,33),_('C',3,11,32,67),_('C',3,11,68,67)],[_('C',3,10,20,33),_('C',3,10,50,33),_('C',3,10,80,33),_('C',3,10,20,67),_('C',3,10,50,67),_('C',3,10,80,67)]],[[_('C',3,11,50,25),_('C',3,11,50,50),_('C',3,11,50,75)],[_('C',3,10,32,25),_('C',3,10,68,25),_('C',3,10,32,50),_('C',3,10,68,50),_('C',3,10,32,75),_('C',3,10,68,75)],null]],ans:4,opts:[[_('C',3,11,20,50),_('C',3,11,50,50),_('C',3,11,80,50)],[_('C',3,10,32,25),_('C',3,10,68,25),_('C',3,10,32,50),_('C',3,10,68,50),_('C',3,10,32,75),_('C',3,10,68,75)],[_('C',3,12,32,33),_('C',3,12,68,33),_('C',3,12,32,67),_('C',3,12,68,67)],[_('C',3,10,25,25),_('C',3,10,50,25),_('C',3,10,75,25),_('C',3,10,25,50),_('C',3,10,75,50),_('C',3,10,25,75),_('C',3,10,50,75),_('C',3,10,75,75)],[_('C',3,9,25,25),_('C',3,9,50,25),_('C',3,9,75,25),_('C',3,9,25,50),_('C',3,9,50,50),_('C',3,9,75,50),_('C',3,9,25,75),_('C',3,9,50,75),_('C',3,9,75,75)],[_('C',3,8,20,25),_('C',3,8,40,25),_('C',3,8,60,25),_('C',3,8,80,25),_('C',3,8,20,50),_('C',3,8,40,50),_('C',3,8,60,50),_('C',3,8,80,50),_('C',3,8,20,75),_('C',3,8,40,75),_('C',3,8,60,75),_('C',3,8,80,75)]]},
{id:15,set:'C',label:'C-1',matrix:[[[_('C',3)],[_('S',0)],[_('T',2)]],[[_('S',0)],[_('T',2)],[_('C',3)]],[[_('T',2)],[_('C',3)],null]],ans:2,opts:[[_('C',0)],[_('T',3)],[_('S',0)],[_('S',3)],[_('T',0)],[_('S',2)]]},
{id:16,set:'C',label:'C-2',matrix:[[[_('C',0,33),_('S',3,8)],[_('C',0,24),_('S',3,16)],[_('C',0,16),_('S',0,22)]],[[_('S',0,33),_('T',3,8)],[_('S',0,24),_('T',3,16)],[_('S',0,16),_('T',0,22)]],[[_('T',0,33),_('D',3,8)],[_('T',0,24),_('D',3,16)],null]],ans:3,opts:[[_('T',0,33),_('D',3,8)],[_('T',0,24),_('D',3,16)],[_('T',3,16),_('D',0,22)],[_('T',0,16),_('D',0,22)],[_('T',0,16),_('D',3,22)],[_('C',0,16),_('D',0,22)]]},
{id:17,set:'C',label:'C-3',matrix:[[[_('C',3,14,28,28),_('S',3,14,72,28),_('T',3,14,50,73)],[_('S',3,14,72,28),_('T',3,14,50,73)],[_('C',3,14,28,28)]],[[_('T',3,14,28,28),_('C',3,14,72,28),_('D',3,14,50,73)],[_('C',3,14,72,28),_('D',3,14,50,73)],[_('T',3,14,28,28)]],[[_('S',3,14,28,28),_('D',3,14,72,28),_('T',3,14,50,73)],[_('D',3,14,72,28),_('T',3,14,50,73)],null]],ans:1,opts:[[_('D',3,14,28,28)],[_('S',3,14,28,28)],[_('T',3,14,50,73)],[_('D',3,14,72,28)],[_('D',3,14,72,28),_('T',3,14,50,73)],[_('S',3,22)]]},
{id:18,set:'C',label:'C-4',matrix:[[[_('C',0,14)],[_('S',2,24)],[_('T',3,36)]],[[_('S',0,14)],[_('T',2,24)],[_('C',3,36)]],[[_('T',0,14)],[_('C',2,24)],null]],ans:3,opts:[[_('T',3,36)],[_('C',3,36)],[_('S',0,36)],[_('S',3,36)],[_('S',2,24)],[_('D',3,36)]]},
{id:19,set:'C',label:'C-5',matrix:[[[_('C',0,18,30,72),_('D',3,18,70,28)],[_('C',2,18,30,72),_('P',3,18,70,28)],[_('C',3,18,30,72),_('H',3,18,70,28)]],[[_('S',0,18,30,72),_('D',2,18,70,28)],[_('S',2,18,30,72),_('P',2,18,70,28)],[_('S',3,18,30,72),_('H',2,18,70,28)]],[[_('T',0,18,30,72),_('D',0,18,70,28)],[_('T',2,18,30,72),_('P',0,18,70,28)],null]],ans:0,opts:[[_('T',3,18,30,72),_('H',0,18,70,28)],[_('T',0,18,30,72),_('H',3,18,70,28)],[_('T',3,18,30,72),_('H',3,18,70,28)],[_('C',3,18,30,72),_('H',0,18,70,28)],[_('T',3,18,30,72),_('P',0,18,70,28)],[_('S',3,18,30,72),_('H',0,18,70,28)]]},
{id:20,set:'C',label:'C-6',matrix:[[[_('C',3,17,28,28),_('D',0,17,60,62)],[_('C',3,17,28,28),_('P',2,17,60,62)],[_('C',3,17,28,28),_('H',3,17,60,62)]],[[_('S',0,17,28,28),_('D',0,17,60,62)],[_('S',0,17,28,28),_('P',2,17,60,62)],[_('S',0,17,28,28),_('H',3,17,60,62)]],[[_('T',2,17,28,28),_('D',0,17,60,62)],[_('T',2,17,28,28),_('P',2,17,60,62)],null]],ans:4,opts:[[_('T',0,17,28,28),_('H',3,17,60,62)],[_('T',3,17,28,28),_('H',3,17,60,62)],[_('S',2,17,28,28),_('H',3,17,60,62)],[_('T',2,17,28,28),_('H',0,17,60,62)],[_('T',2,17,28,28),_('H',3,17,60,62)],[_('C',2,17,28,28),_('H',3,17,60,62)]]},
];
const KEYS=['A','B','C','D','E','F'];

// ─── Alpine: modo Raven ──────────────────────────────────────────────────────
function ravenApp({ saveUrl, timeRemaining, ravenQuestions, savedAnswers, totalQuestions }) {
    return {
        current: 0,
        answers: {},
        saving: false,
        showModal: false,
        timeRemaining,
        timerInterval: null,
        saveTimeout: null,

        get answeredCount() {
            return Object.values(this.answers).filter(v => v != null).length;
        },
        get setLabel() {
            const s = ITEMS[this.current]?.set;
            return s === 'A' ? 'SET A · FÁCIL' : s === 'B' ? 'SET B · MEDIO' : 'SET C · DIFÍCIL';
        },

        init() {
            // Restaurar respuestas previas
            for (const [qId, val] of Object.entries(savedAnswers)) {
                if (val != null) this.answers[qId] = val;
            }
            // Timer
            if (this.timeRemaining !== null && this.timeRemaining > 0) {
                this.timerInterval = setInterval(() => {
                    this.timeRemaining--;
                    if (this.timeRemaining % 30 === 0) this.syncTime();
                    if (this.timeRemaining <= 0) {
                        clearInterval(this.timerInterval);
                        document.getElementById('finish-form')?.submit();
                    }
                }, 1000);
            }
            this.$nextTick(() => this.renderMatrix());
        },

        isAnswered(idx) {
            const qId = ravenQuestions[idx]?.qId;
            return qId != null && this.answers[qId] != null;
        },

        selectedIdx() {
            const meta = ravenQuestions[this.current];
            if (!meta) return -1;
            const saved = this.answers[meta.qId];
            if (saved == null) return -1;
            return meta.optionIds.indexOf(saved);
        },

        selectOption(optIdx) {
            const meta = ravenQuestions[this.current];
            const optId = meta.optionIds[optIdx];
            this.answers[meta.qId] = optId;
            this.persistAnswer(meta.qId, optId);
            this.renderOptions(); // re-paint selection
        },

        goTo(idx) {
            this.current = idx;
            this.$nextTick(() => this.renderMatrix());
        },

        next() {
            if (this.current < totalQuestions - 1) {
                this.current++;
                this.$nextTick(() => this.renderMatrix());
            } else {
                this.showModal = true;
            }
        },

        prev() {
            if (this.current > 0) {
                this.current--;
                this.$nextTick(() => this.renderMatrix());
            }
        },

        renderMatrix() {
            const item = ITEMS[this.current];
            if (!item) return;

            // Pintar cuadrícula 3×3
            const grid = document.getElementById('raven-grid');
            if (!grid) return;
            grid.innerHTML = '';
            for (let r = 0; r < 3; r++) {
                for (let c = 0; c < 3; c++) {
                    const el = document.createElement('div');
                    const shapes = item.matrix[r][c];
                    if (shapes === null) {
                        el.className = 'raven-cell raven-empty';
                        el.innerHTML = '<div class="raven-q">?</div>';
                    } else {
                        el.className = 'raven-cell';
                        el.innerHTML = cellSVG(shapes, 90);
                    }
                    grid.appendChild(el);
                }
            }
            this.renderOptions();
        },

        renderOptions() {
            const item = ITEMS[this.current];
            if (!item) return;
            const og = document.getElementById('raven-opts');
            if (!og) return;
            const selIdx = this.selectedIdx();
            og.innerHTML = '';
            item.opts.forEach((shapes, i) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'raven-opt' + (i === selIdx ? ' sel' : '');
                btn.innerHTML = cellSVG(shapes, 58) + `<div class="raven-opt-key">${KEYS[i]}</div>`;
                btn.onclick = () => this.selectOption(i);
                og.appendChild(btn);
            });
        },

        persistAnswer(qId, optId) {
            this.saving = true;
            clearTimeout(this.saveTimeout);
            this.saveTimeout = setTimeout(() => {
                fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        question_id: qId,
                        question_option_id: optId,
                        text_answer: null,
                        time_remaining: this.timeRemaining,
                    }),
                })
                .then(r => r.json())
                .finally(() => { this.saving = false; });
            }, 400);
        },

        syncTime() {
            const firstQ = Object.keys(this.answers)[0];
            if (!firstQ) return;
            fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ question_id: parseInt(firstQ), time_remaining: this.timeRemaining }),
            }).catch(() => {});
        },

        formatTime(s) {
            if (s === null || s === undefined) return '';
            const m = Math.floor(s / 60).toString().padStart(2, '0');
            const sec = (s % 60).toString().padStart(2, '0');
            return `${m}:${sec}`;
        },
    };
}

// ─── Alpine: modo estándar (Big Five, 16PF, Assessment Center…) ─────────────
function testApp({ assignmentId, saveUrl, timeRemaining, totalQuestions, savedAnswers }) {
    return {
        answers:      {},
        textAnswers:  {},
        saving:       false,
        showModal:    false,
        timeRemaining,
        timerInterval: null,
        saveTimeout:   null,

        get answeredCount() {
            const opts  = Object.values(this.answers).filter(v => v !== null && v !== undefined).length;
            const texts = Object.values(this.textAnswers).filter(v => v?.trim()).length;
            return opts + texts;
        },

        init() {
            for (const [qId, val] of Object.entries(savedAnswers)) {
                if (typeof val === 'number') this.answers[qId] = val;
                else if (val)               this.textAnswers[qId] = val;
            }

            if (this.timeRemaining !== null && this.timeRemaining > 0) {
                this.timerInterval = setInterval(() => {
                    this.timeRemaining--;
                    if (this.timeRemaining % 30 === 0) this.syncTime();
                    if (this.timeRemaining <= 0) {
                        clearInterval(this.timerInterval);
                        document.querySelector('form[action$="/finalizar"]')?.submit();
                    }
                }, 1000);
            }
        },

        formatTime(s) {
            if (s === null || s === undefined) return '';
            const m = Math.floor(s / 60).toString().padStart(2, '0');
            const sec = (s % 60).toString().padStart(2, '0');
            return `${m}:${sec}`;
        },

        saveAnswer(questionId, optionId, textAnswer) {
            if (optionId !== null)        this.answers[questionId]     = optionId;
            else if (textAnswer !== null) this.textAnswers[questionId] = textAnswer;

            this.saving = true;
            clearTimeout(this.saveTimeout);

            this.saveTimeout = setTimeout(() => {
                fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        question_id:        questionId,
                        question_option_id: optionId,
                        text_answer:        textAnswer,
                        time_remaining:     this.timeRemaining,
                    }),
                })
                .then(r => r.json())
                .finally(() => { this.saving = false; });
            }, 500);
        },

        syncTime() {
            const firstQ = Object.keys(this.answers)[0];
            if (!firstQ) return;
            fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ question_id: firstQ, time_remaining: this.timeRemaining }),
            }).catch(() => {});
        },
    };
}
</script>

@endsection
