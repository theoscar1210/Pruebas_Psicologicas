@extends('layouts.candidate')

@section('title', $assignment->test->name)

@section('content')

<div
    class="min-h-screen bg-slate-50"
    x-data="testApp({
        assignmentId: {{ $assignment->id }},
        saveUrl: '{{ route('candidate.answer', $assignment) }}',
        finishUrl: '{{ route('candidate.finish', $assignment) }}',
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
    <div class="sticky top-0 z-30 bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-4">

            {{-- Nombre de la prueba --}}
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 text-sm truncate">{{ $assignment->test->name }}</p>
                <p class="text-xs text-gray-400">{{ $assignment->candidate->name }}</p>
            </div>

            {{-- Progreso --}}
            <div class="hidden sm:flex items-center gap-2 text-xs text-gray-500">
                <span x-text="answeredCount"></span>/<span>{{ $assignment->test->questions->count() }}</span>
                <div class="w-24 bg-gray-100 rounded-full h-1.5">
                    <div class="bg-indigo-500 h-1.5 rounded-full transition-all"
                         :style="`width:${(answeredCount/{{ $assignment->test->questions->count() }})*100}%`"></div>
                </div>
            </div>

            {{-- Timer --}}
            @if($assignment->test->time_limit)
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-mono font-bold text-sm transition"
                 :class="timeRemaining <= 300 ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-700'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-text="formatTime(timeRemaining)">--:--</span>
            </div>
            @endif

            {{-- Autoguardado --}}
            <div class="flex items-center gap-1 text-xs" :class="saving ? 'text-yellow-500' : 'text-green-500'">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span x-text="saving ? 'Guardando…' : 'Guardado'" x-cloak></span>
            </div>

        </div>
    </div>

    {{-- ── Instrucciones ───────────────────────────────────────────────── --}}
    @if($assignment->test->instructions)
    <div class="max-w-3xl mx-auto px-4 mt-6">
        <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-5 py-4 text-sm text-indigo-800">
            <p class="font-semibold mb-1 flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                Instrucciones
            </p>
            {{ $assignment->test->instructions }}
        </div>
    </div>
    @endif

    {{-- ── Preguntas ───────────────────────────────────────────────────── --}}
    <div class="max-w-3xl mx-auto px-4 py-6 space-y-6 pb-32">

        @foreach($assignment->test->questions as $question)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
             id="q-{{ $question->id }}">

            {{-- Cabecera de pregunta --}}
            <div class="flex items-start gap-3 mb-5">
                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                    {{ $question->order }}
                </span>
                <div class="flex-1">
                    <p class="text-gray-900 font-medium leading-relaxed">{{ $question->text }}</p>
                    @if($question->is_required)
                        <span class="text-xs text-red-400">* Obligatoria</span>
                    @endif
                </div>
            </div>

            {{-- Opciones múltiple --}}
            @if($question->type === 'multiple_choice')
            <div class="space-y-2 ml-11">
                @foreach($question->options as $option)
                <label
                    class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition
                           hover:border-indigo-300 hover:bg-indigo-50"
                    :class="answers[{{ $question->id }}] == {{ $option->id }}
                        ? 'border-indigo-500 bg-indigo-50'
                        : 'border-gray-100 bg-gray-50'">
                    <input
                        type="radio"
                        name="q{{ $question->id }}"
                        value="{{ $option->id }}"
                        class="w-4 h-4 text-indigo-600 flex-shrink-0"
                        :checked="answers[{{ $question->id }}] == {{ $option->id }}"
                        @change="saveAnswer({{ $question->id }}, {{ $option->id }}, null)">
                    <span class="text-sm text-gray-700">{{ $option->text }}</span>
                </label>
                @endforeach
            </div>

            {{-- Escala Likert --}}
            @elseif($question->type === 'likert')
            <div class="ml-11">
                <div class="flex items-center justify-between gap-2">
                    @foreach($question->options as $option)
                    <label class="flex-1 text-center cursor-pointer group">
                        <input
                            type="radio"
                            name="q{{ $question->id }}"
                            value="{{ $option->id }}"
                            class="sr-only"
                            :checked="answers[{{ $question->id }}] == {{ $option->id }}"
                            @change="saveAnswer({{ $question->id }}, {{ $option->id }}, null)">
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold text-sm transition
                                        group-hover:border-indigo-400 group-hover:bg-indigo-50"
                                 :class="answers[{{ $question->id }}] == {{ $option->id }}
                                    ? 'border-indigo-500 bg-indigo-500 text-white'
                                    : 'border-gray-200 text-gray-500'">
                                {{ $option->value }}
                            </div>
                            <span class="text-xs text-gray-400 leading-tight text-center hidden sm:block">
                                {{ $option->text }}
                            </span>
                        </div>
                    </label>
                    @endforeach
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-2 sm:hidden">
                    <span>{{ $question->options->first()->text }}</span>
                    <span>{{ $question->options->last()->text }}</span>
                </div>
            </div>

            {{-- Pregunta abierta --}}
            @elseif($question->type === 'open')
            <div class="ml-11">
                <textarea
                    name="q{{ $question->id }}_text"
                    rows="4"
                    placeholder="Escribe tu respuesta aquí…"
                    class="w-full border-2 border-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-400 transition resize-none"
                    x-model="textAnswers[{{ $question->id }}]"
                    @input.debounce.800ms="saveAnswer({{ $question->id }}, null, textAnswers[{{ $question->id }}])">{{ $assignment->answers->firstWhere('question_id', $question->id)?->text_answer }}</textarea>
            </div>
            @endif

            {{-- Indicador respondida --}}
            <div class="flex justify-end mt-3">
                <span class="text-xs transition"
                      :class="answers[{{ $question->id }}] || textAnswers[{{ $question->id }}]
                        ? 'text-green-500' : 'text-gray-300'">
                    <template x-if="answers[{{ $question->id }}] || textAnswers[{{ $question->id }}]">
                        <span>✓ Respondida</span>
                    </template>
                    <template x-if="!answers[{{ $question->id }}] && !textAnswers[{{ $question->id }}]">
                        <span>Sin responder</span>
                    </template>
                </span>
            </div>

        </div>
        @endforeach

    </div>

    {{-- ── Barra inferior fija: Finalizar ─────────────────────────────── --}}
    <div class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-100 shadow-lg">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between gap-4">

            <div class="text-sm text-gray-500">
                <span x-text="answeredCount" class="font-bold text-gray-800"></span>
                de {{ $assignment->test->questions->count() }} preguntas respondidas
                <template x-if="{{ $assignment->test->questions->where('is_required', true)->count() }} > answeredCount">
                    <span class="text-yellow-500 ml-2 text-xs">— Hay preguntas obligatorias sin responder</span>
                </template>
            </div>

            <button
                @click="confirmFinish()"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Finalizar prueba
            </button>
        </div>
    </div>

    {{-- Modal confirmación finalizar --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full text-center"
             @click.stop>
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">¿Finalizar la prueba?</h3>
            <p class="text-sm text-gray-500 mb-6">
                Has respondido <strong x-text="answeredCount"></strong> de
                <strong>{{ $assignment->test->questions->count() }}</strong> preguntas.
                Esta acción no se puede deshacer.
            </p>
            <div class="flex gap-3">
                <button @click="showModal = false"
                        class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">
                    Continuar revisando
                </button>
                <form method="POST" action="{{ route('candidate.finish', $assignment) }}" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                        Sí, finalizar
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function testApp({ assignmentId, saveUrl, finishUrl, timeRemaining, totalQuestions, savedAnswers }) {
    return {
        answers: {},         // question_id → option_id
        textAnswers: {},     // question_id → text
        saving: false,
        showModal: false,
        timeRemaining,
        timerInterval: null,
        saveTimeout: null,

        get answeredCount() {
            const optionAnswered = Object.values(this.answers).filter(v => v !== null && v !== undefined).length;
            const textAnswered = Object.values(this.textAnswers).filter(v => v && v.trim() !== '').length;
            return optionAnswered + textAnswered;
        },

        init() {
            // Cargar respuestas guardadas
            for (const [qId, val] of Object.entries(savedAnswers)) {
                if (typeof val === 'number') {
                    this.answers[qId] = val;
                } else if (val) {
                    this.textAnswers[qId] = val;
                }
            }

            // Iniciar timer si aplica
            if (this.timeRemaining !== null) {
                this.timerInterval = setInterval(() => {
                    if (this.timeRemaining > 0) {
                        this.timeRemaining--;
                        // Guardar tiempo cada 30 segundos
                        if (this.timeRemaining % 30 === 0) {
                            this.persistTime();
                        }
                        // Tiempo agotado → envío automático
                        if (this.timeRemaining === 0) {
                            clearInterval(this.timerInterval);
                            document.querySelector('form[action="{{ route('candidate.finish', $assignment) }}"]').submit();
                        }
                    }
                }, 1000);
            }
        },

        formatTime(seconds) {
            if (seconds === null) return '';
            const m = Math.floor(seconds / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        saveAnswer(questionId, optionId, textAnswer) {
            // Actualizar estado local
            if (optionId !== null) {
                this.answers[questionId] = optionId;
            } else {
                this.textAnswers[questionId] = textAnswer;
            }

            this.saving = true;

            // Debounce: espera 600ms antes de enviar
            clearTimeout(this.saveTimeout);
            this.saveTimeout = setTimeout(() => {
                const body = {
                    _token: document.querySelector('meta[name="csrf-token"]').content,
                    question_id: questionId,
                    question_option_id: optionId,
                    text_answer: textAnswer,
                    time_remaining: this.timeRemaining,
                };

                fetch(saveUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(body),
                })
                .then(r => r.json())
                .then(() => { this.saving = false; })
                .catch(() => { this.saving = false; });
            }, 600);
        },

        persistTime() {
            fetch(saveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    _token: document.querySelector('meta[name="csrf-token"]').content,
                    question_id: Object.keys(this.answers)[0] ?? null,
                    time_remaining: this.timeRemaining,
                }),
            }).catch(() => {});
        },

        confirmFinish() {
            this.showModal = true;
        },
    };
}
</script>

@endsection
