@extends('layouts.admin')

@section('title', 'Nuevo Cargo')
@section('header', 'Nuevo Cargo')

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm p-6">

        <form action="{{ route('admin.positions.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nombre --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre del cargo <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Ej: Mesero, Auxiliar de Cocina, Caddie…"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                              @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" rows="3"
                          placeholder="Funciones principales del cargo…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Pruebas asociadas --}}
            @if($tests->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pruebas psicológicas
                    <span class="text-xs text-gray-400 font-normal ml-1">(arrastra para ordenar)</span>
                </label>
                <div class="space-y-2">
                    @foreach($tests as $test)
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 transition">
                        <input type="checkbox" name="tests[]" value="{{ $test->id }}"
                               {{ in_array($test->id, old('tests', [])) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 rounded">
                        <span class="text-sm text-gray-700">{{ $test->name }}</span>
                        @if($test->time_limit)
                            <span class="ml-auto text-xs text-gray-400">{{ $test->time_limit }} min</span>
                        @endif
                    </label>
                    @endforeach
                </div>
                @error('tests')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            @endif

            {{-- Estado --}}
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 rounded">
                <label for="is_active" class="text-sm text-gray-700">Cargo activo</label>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    Guardar cargo
                </button>
                <a href="{{ route('admin.positions.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
            </div>

        </form>
    </div>
</div>

@endsection
