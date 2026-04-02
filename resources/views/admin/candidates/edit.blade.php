@extends('layouts.admin')

@section('title', 'Editar Candidato')
@section('header', 'Editar: ' . $candidate->name)

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm p-6">

        <form action="{{ route('admin.candidates.update', $candidate) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $candidate->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('name') border-red-400 @enderror">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $candidate->email) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $candidate->phone) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número de documento</label>
                    <input type="text" name="document_number" value="{{ old('document_number', $candidate->document_number) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                    <select name="position_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Sin cargo —</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}"
                                {{ old('position_id', $candidate->position_id) == $position->id ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="active"    {{ old('status', $candidate->status) === 'active'    ? 'selected' : '' }}>Activo</option>
                        <option value="completed" {{ old('status', $candidate->status) === 'completed' ? 'selected' : '' }}>Completado</option>
                        <option value="inactive"  {{ old('status', $candidate->status) === 'inactive'  ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    Actualizar candidato
                </button>
                <a href="{{ route('admin.candidates.show', $candidate) }}"
                   class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
            </div>

        </form>
    </div>
</div>

@endsection
