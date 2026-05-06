@extends('layouts.admin')

@section('title', 'Preguntas — ' . $test->name)
@section('header', 'Preguntas: ' . $test->name)

@section('header-actions')
    <a href="{{ route('admin.tests.index') }}" class="btn-ghost btn-sm">← Volver a pruebas</a>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── Columna izquierda: lista de preguntas ───────────────────────── --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between mb-1">
            <h2 class="font-semibold text-slate-700 text-sm">
                Preguntas ({{ $test->questions->count() }})
            </h2>
            @if($test->questions->isNotEmpty())
                <span class="text-xs text-slate-400">
                    Puntaje total: {{ $test->questions->sum('points') }} pts
                </span>
            @endif
        </div>

        @forelse($test->questions as $question)
        <div class="card">
            <div class="card-body py-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 text-xs font-bold flex items-center justify-center flex-shrink-0">
                                {{ $question->order }}
                            </span>
                            @if($question->type === 'multiple_choice')
                                <span class="badge-info">Opción múltiple</span>
                            @elseif($question->type === 'likert')
                                <span class="badge-purple">Escala Likert</span>
                            @else
                                <span class="badge-neutral">Abierta</span>
                            @endif
                            <span class="text-xs text-slate-400 ml-auto">{{ $question->points }} pts</span>
                        </div>
                        <p class="text-sm text-slate-800 leading-relaxed">{{ $question->text }}</p>

                        @if($question->options->isNotEmpty())
                        <ul class="mt-2 space-y-1">
                            @foreach($question->options as $option)
                            <li class="flex items-center gap-2 text-xs text-slate-600">
                                <span class="w-4 h-4 rounded border flex items-center justify-center text-xs
                                    {{ $option->is_correct ? 'border-emerald-400 bg-emerald-50 text-emerald-600' : 'border-slate-200 bg-slate-50' }}">
                                    @if($option->is_correct) ✓ @endif
                                </span>
                                {{ $option->text }}
                                <span class="ml-auto text-slate-400">({{ $option->value }} pts)</span>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    <form action="{{ route('admin.tests.questions.destroy', [$test, $question]) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar esta pregunta?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 mt-0.5 flex-shrink-0 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="card border-dashed">
            <div class="card-body py-10 text-center text-slate-400 text-sm">
                Aún no hay preguntas. Agrega la primera usando el formulario.
            </div>
        </div>
        @endforelse
    </div>

    {{-- ── Columna derecha: formulario nueva pregunta ───────────────────── --}}
    <div class="card h-fit" x-data="questionForm()">
        <div class="card-body">
            <h2 class="font-semibold text-slate-700 mb-4 text-sm">Agregar pregunta</h2>

            <form action="{{ route('admin.tests.questions.store', $test) }}" method="POST" class="space-y-4">
                @csrf

                <div class="form-group">
                    <label class="form-label">Pregunta <span class="form-required">*</span></label>
                    <textarea name="text" rows="2" required x-model="text"
                              placeholder="Escribe aquí la pregunta…"
                              class="textarea @error('text') input-error @enderror">{{ old('text') }}</textarea>
                    @error('text') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="form-group">
                        <label class="form-label">Tipo <span class="form-required">*</span></label>
                        <select name="type" required x-model="type" class="select">
                            <option value="multiple_choice">Opción múltiple</option>
                            <option value="likert">Escala Likert</option>
                            <option value="open">Pregunta abierta</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Puntaje <span class="form-required">*</span></label>
                        <input type="number" name="points" :value="type === 'likert' ? 5 : 3"
                               min="1" max="100" required class="input">
                    </div>
                </div>

                <div x-show="type !== 'open'" x-cloak>

                    <div class="flex items-center justify-between mb-2">
                        <label class="form-label mb-0">Opciones de respuesta</label>
                        <button type="button" @click="addOption()"
                                class="text-xs text-brand-600 hover:text-brand-800 font-medium transition-colors">+ Agregar</button>
                    </div>

                    <template x-if="type === 'likert'">
                        <div class="space-y-1.5">
                            <template x-for="(opt, i) in likertOptions" :key="i">
                                <div class="flex items-center gap-2">
                                    <input type="hidden" :name="`options[${i}][text]`" :value="opt.text">
                                    <input type="hidden" :name="`options[${i}][value]`" :value="opt.value">
                                    <input type="hidden" :name="`options[${i}][is_correct]`" value="0">
                                    <span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 text-xs flex items-center justify-center font-bold" x-text="opt.value"></span>
                                    <span class="text-sm text-slate-600" x-text="opt.text"></span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="type === 'multiple_choice'">
                        <div class="space-y-2">
                            <template x-for="(opt, i) in options" :key="i">
                                <div class="flex items-center gap-2">
                                    <input type="text" :name="`options[${i}][text]`" x-model="opt.text"
                                           placeholder="Texto de la opción"
                                           class="flex-1 border border-slate-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                                    <input type="number" :name="`options[${i}][value]`" x-model="opt.value"
                                           placeholder="Pts" step="0.5" min="0"
                                           class="w-16 border border-slate-200 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                                    <label class="flex items-center gap-1 text-xs text-slate-500 whitespace-nowrap">
                                        <input type="checkbox" :name="`options[${i}][is_correct]`" :value="1"
                                               x-model="opt.is_correct"
                                               class="w-3.5 h-3.5 rounded accent-emerald-600">
                                        Correcta
                                    </label>
                                    <button type="button" @click="removeOption(i)"
                                            class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            <p class="form-hint">Mínimo 2 opciones. Marca "Correcta" si hay una respuesta esperada.</p>
                        </div>
                    </template>

                </div>

                @error('options') <p class="form-error">{{ $message }}</p> @enderror

                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_required" value="0">
                    <input type="checkbox" name="is_required" id="is_required" value="1" checked
                           class="w-4 h-4 rounded accent-brand-600">
                    <label for="is_required" class="text-sm text-slate-700">Pregunta obligatoria</label>
                </div>

                <button type="submit" class="btn-primary w-full justify-center">Agregar pregunta</button>
            </form>
        </div>
    </div>

</div>

<script nonce="{{ app('csp-nonce') }}">
function questionForm() {
    return {
        type: 'multiple_choice',
        text: '',
        options: [
            { text: '', value: 0, is_correct: false },
            { text: '', value: 0, is_correct: false },
            { text: '', value: 0, is_correct: false },
            { text: '', value: 0, is_correct: false },
        ],
        likertOptions: [
            { text: 'Nunca',         value: 1 },
            { text: 'Casi nunca',    value: 2 },
            { text: 'A veces',       value: 3 },
            { text: 'Casi siempre',  value: 4 },
            { text: 'Siempre',       value: 5 },
        ],
        addOption() {
            this.options.push({ text: '', value: 0, is_correct: false });
        },
        removeOption(i) {
            if (this.options.length > 2) this.options.splice(i, 1);
        },
    }
}
</script>

@endsection
