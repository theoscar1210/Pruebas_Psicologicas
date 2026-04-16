@extends('layouts.candidate')
@section('title', $assignment->test->name)

@section('content')

<div
    class="min-h-screen bg-slate-50"
    x-data="testApp({
        assignmentId: {{ $assignment->id }},
        saveUrl: '{{ route('candidate.answer', $assignment) }}',
        timeRemaining: {{ $assignment->time_remaining ?? 'null' }},
        totalQuestions: {{ $assignment->test->questions->count() }},
        savedAnswers: {!! json_encode(
            $assignment->answers->mapWithKeys(fn($a) => [
                $a->question_id => $a->question_option_id ?? $a->text_answer
            ])
        ) !!}
    })"
    x-init="init()"
>

    {{-- ── Topbar fija ─────────────────────────────────────────────────── --}}
    <div class="sticky top-0 z-30 bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-2xl mx-auto px-4 py-3">
            <div class="flex items-center gap-4">

                {{-- Info prueba --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 truncate">
                        {{ $assignment->test->name }}
                    </p>
                    <p class="text-xs text-slate-400">{{ $assignment->candidate->name }}</p>
                </div>

                {{-- Progreso compacto --}}
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
                    <span>
                        <span x-text="answeredCount" class="font-semibold text-slate-700"></span>
                        / {{ $assignment->test->questions->count() }}
                    </span>
                    <div class="progress-track w-20 h-1.5">
                        <div class="progress-bar bg-brand-500"
                             :style="`width:${Math.round((answeredCount/{{ $assignment->test->questions->count() }})*100)}%`">
                        </div>
                    </div>
                </div>

                {{-- Timer --}}
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

                {{-- Autoguardado --}}
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

            {{-- Barra de progreso total --}}
            <div class="progress-track h-1 mt-2.5 -mx-4 px-0 rounded-none">
                <div class="progress-bar bg-brand-500 h-1 rounded-none"
                     :style="`width:${Math.round((answeredCount/{{ $assignment->test->questions->count() }})*100)}%`">
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

            {{-- Encabezado de pregunta --}}
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

                    {{-- Círculo radio personalizado --}}
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

                {{-- Leyenda extremos (solo en pantallas grandes donde el Likert es horizontal) --}}
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

            {{-- Indicador respondida --}}
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
                de {{ $assignment->test->questions->count() }} respondidas
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

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
             @click="showModal = false"
             x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
        </div>

        {{-- Dialog --}}
        <div class="relative bg-white rounded-modal shadow-modal w-full max-w-sm p-7 text-center animate-slide-up">
            <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h3 class="text-lg font-bold text-slate-900 mb-2">¿Finalizar la prueba?</h3>
            <p class="text-sm text-slate-500 mb-1">
                Has respondido <strong x-text="answeredCount" class="text-slate-800"></strong>
                de <strong class="text-slate-800">{{ $assignment->test->questions->count() }}</strong> preguntas.
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

{{-- ── Alpine.js component ────────────────────────────────────────────── --}}
<script>
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
            // Cargar respuestas previas
            for (const [qId, val] of Object.entries(savedAnswers)) {
                if (typeof val === 'number') this.answers[qId] = val;
                else if (val)               this.textAnswers[qId] = val;
            }

            // Timer countdown
            if (this.timeRemaining !== null && this.timeRemaining > 0) {
                this.timerInterval = setInterval(() => {
                    this.timeRemaining--;
                    if (this.timeRemaining % 30 === 0) this.syncTime();
                    if (this.timeRemaining <= 0) {
                        clearInterval(this.timerInterval);
                        document.querySelector('form[action="{{ route('candidate.finish', $assignment) }}"]')?.submit();
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
            if (optionId !== null)    this.answers[questionId]     = optionId;
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
