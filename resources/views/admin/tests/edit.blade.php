@extends('layouts.admin')

@section('title', 'Editar Prueba')
@section('header', 'Editar: ' . $test->name)

@section('header-actions')
    <a href="{{ route('admin.tests.questions.index', $test) }}"
       class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        Gestionar preguntas ({{ $test->questions()->count() }})
    </a>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm p-6">

        <form action="{{ route('admin.tests.update', $test) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre de la prueba <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $test->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                              @error('name') border-red-400 @enderror">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('description', $test->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Instrucciones para el candidato</label>
                <textarea name="instructions" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('instructions', $test->instructions) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiempo límite (minutos)</label>
                    <input type="number" name="time_limit" value="{{ old('time_limit', $test->time_limit) }}"
                           min="1" max="300" placeholder="Sin límite"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Puntaje mínimo (%) <span class="text-red-500">*</span></label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', $test->passing_score) }}"
                           min="1" max="100" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $test->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 rounded">
                <label for="is_active" class="text-sm text-gray-700">Prueba activa</label>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    Actualizar prueba
                </button>
                <a href="{{ route('admin.tests.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
            </div>

        </form>
    </div>
</div>

@endsection
