@extends('layouts.admin')

@section('title', 'Nueva Prueba')
@section('header', 'Nueva Prueba Psicológica')

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm p-6">

        <form action="{{ route('admin.tests.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre de la prueba <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Ej: Test de Servicio al Cliente"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                              @error('name') border-red-400 @enderror">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" rows="2"
                          placeholder="Objetivo de esta prueba…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Instrucciones para el candidato
                </label>
                <textarea name="instructions" rows="3"
                          placeholder="Texto que verá el candidato antes de iniciar la prueba…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('instructions') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tiempo límite (minutos)
                        <span class="text-xs text-gray-400 font-normal">— déjalo vacío para sin límite</span>
                    </label>
                    <input type="number" name="time_limit" value="{{ old('time_limit') }}"
                           min="1" max="300" placeholder="30"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                  @error('time_limit') border-red-400 @enderror">
                    @error('time_limit') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Puntaje mínimo para aprobar (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', 65) }}"
                           min="1" max="100" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                  @error('passing_score') border-red-400 @enderror">
                    @error('passing_score') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 rounded">
                <label for="is_active" class="text-sm text-gray-700">Prueba activa</label>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    Guardar y agregar preguntas →
                </button>
                <a href="{{ route('admin.tests.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
            </div>

        </form>
    </div>
</div>

@endsection
