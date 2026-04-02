@extends('layouts.admin')

@section('title', 'Preguntas — ' . $test->name)
@section('header', 'Preguntas: ' . $test->name)

@section('header-actions')
    <a href="{{ route('admin.tests.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700">← Volver a pruebas</a>
@endsection

@section('content')

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- ── Columna izquierda: lista de preguntas ───────────────────────── --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between mb-1">
            <h2 class="font-semibold text-gray-700">
                Preguntas ({{ $test->questions->count() }})
            </h2>
            @if($test->questions->isNotEmpty())
                <span class="text-xs text-gray-400">
                    Puntaje total: {{ $test->questions->sum('points') }} pts
                </span>
            @endif
        </div>

        @forelse($test->questions as $question)
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center flex-shrink-0">
                            {{ $question->order }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            @if($question->type === 'multiple_choice') bg-blue-100 text-blue-700
                            @elseif($question->type === 'likert') bg-purple-100 text-purple-700
                            @else bg-gray-100 text-gray-600 @endif">
                            @if($question->type === 'multiple_choice') Opción múltiple
                            @elseif($question->type === 'likert') Escala Likert
                            @else Abierta @endif
                        </span>
                        <span class="text-xs text-gray-400 ml-auto">{{ $question->points }} pts</span>
                    </div>
                    <p class="text-sm text-gray-800 leading-relaxed">{{ $question->text }}</p>

                    @if($question->options->isNotEmpty())
                    <ul class="mt-2 space-y-1">
                        @foreach($question->options as $option)
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="w-4 h-4 rounded border
                                @if($option->is_correct) border-green-400 bg-green-50
                                @else border-gray-200 bg-gray-50 @endif
                                flex items-center justify-center text-xs">
                                @if($option->is_correct) ✓ @endif
                            </span>
                            {{ $option->text }}
                            <span class="ml-auto text-gray-400">({{ $option->value }} pts)</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>

                <form action="{{ route('admin.tests.questions.destroy', [$test, $question]) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar esta pregunta?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 mt-0.5 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400 text-sm border border-dashed border-gray-200">
            Aún no hay preguntas. Agrega la primera usando el formulario.
        </div>
        @endforelse
    </div>

    {{-- ── Columna derecha: formulario nueva pregunta ───────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 h-fit" x-data="questionForm()">

        <h2 class="font-semibold text-gray-700 mb-4">Agregar pregunta</h2>

        <form action="{{ route('admin.tests.questions.store', $test) }}" method="POST" class="space-y-4">
            @csrf

            {{-- Texto de la pregunta --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Pregunta <span class="text-red-500">*</span>
                </label>
                <textarea name="text" rows="2" required x-model="text"
                          placeholder="Escribe aquí la pregunta…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none
                                 @error('text') border-red-400 @enderror">{{ old('text') }}</textarea>
                @error('text') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Tipo y puntaje --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <select name="type" required x-model="type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="multiple_choice">Opción múltiple</option>
                        <option value="likert">Escala Likert</option>
                        <option value="open">Pregunta abierta</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Puntaje <span class="text-red-500">*</span></label>
                    <input type="number" name="points" :value="type === 'likert' ? 5 : 3"
                           min="1" max="100" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Opciones para múltiple / Likert --}}
            <div x-show="type !== 'open'" x-cloak>

                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-gray-700">Opciones de respuesta</label>
                    <button type="button" @click="addOption()"
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">+ Agregar opción</button>
                </div>

                {{-- Opciones Likert predeterminadas --}}
                <template x-if="type === 'likert'">
                    <div class="space-y-1.5">
                        <template x-for="(opt, i) in likertOptions" :key="i">
                            <div class="flex items-center gap-2">
                                <input type="hidden" :name="`options[${i}][text]`" :value="opt.text">
                                <input type="hidden" :name="`options[${i}][value]`" :value="opt.value">
                                <input type="hidden" :name="`options[${i}][is_correct]`" value="0">
                                <span class="w-5 h-5 rounded-full bg-purple-100 text-purple-700 text-xs flex items-center justify-center font-bold" x-text="opt.value"></span>
                                <span class="text-sm text-gray-600" x-text="opt.text"></span>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Opciones múltiple personalizadas --}}
                <template x-if="type === 'multiple_choice'">
                    <div class="space-y-2">
                        <template x-for="(opt, i) in options" :key="i">
                            <div class="flex items-center gap-2">
                                <input type="text" :name="`options[${i}][text]`" x-model="opt.text"
                                       placeholder="Texto de la opción"
                                       class="flex-1 border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400">
                                <input type="number" :name="`options[${i}][value]`" x-model="opt.value"
                                       placeholder="Pts" step="0.5" min="0"
                                       class="w-16 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-1 focus:ring-indigo-400">
                                <label class="flex items-center gap-1 text-xs text-gray-500 whitespace-nowrap">
                                    <input type="checkbox" :name="`options[${i}][is_correct]`" :value="1"
                                           x-model="opt.is_correct"
                                           class="w-3.5 h-3.5 text-green-500 rounded">
                                    Correcta
                                </label>
                                <button type="button" @click="removeOption(i)"
                                        class="text-red-400 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <p class="text-xs text-gray-400 mt-1">Mínimo 2 opciones. Marca "Correcta" si hay una respuesta esperada.</p>
                    </div>
                </template>

            </div>

            @error('options') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

            {{-- Obligatoria --}}
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_required" value="0">
                <input type="checkbox" name="is_required" id="is_required" value="1" checked
                       class="w-4 h-4 text-indigo-600 rounded">
                <label for="is_required" class="text-sm text-gray-700">Pregunta obligatoria</label>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 rounded-lg transition">
                Agregar pregunta
            </button>
        </form>
    </div>

</div>

<script>
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
