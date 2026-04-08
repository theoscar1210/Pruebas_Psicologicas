@extends('layouts.admin')

@section('title', 'Nuevo Cargo')
@section('header', 'Nuevo Cargo')

@section('content')

<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.positions.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nombre del cargo <span class="form-required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Ej: Mesero, Auxiliar de Cocina, Caddie…"
                           class="input @error('name') input-error @enderror">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" rows="3"
                              placeholder="Funciones principales del cargo…"
                              class="textarea">{{ old('description') }}</textarea>
                </div>

                @if($tests->isNotEmpty())
                <div class="form-group">
                    <label class="form-label">Pruebas psicológicas</label>
                    <div class="space-y-2 mt-1">
                        @foreach($tests as $test)
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="checkbox" name="tests[]" value="{{ $test->id }}"
                                   {{ in_array($test->id, old('tests', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-brand-600 rounded accent-brand-600">
                            <span class="text-sm text-slate-700">{{ $test->name }}</span>
                            @if($test->time_limit)
                                <span class="ml-auto text-xs text-slate-400">{{ $test->time_limit }} min</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                    @error('tests') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                @endif

                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 rounded accent-brand-600">
                    <label for="is_active" class="text-sm text-slate-700">Cargo activo</label>
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Guardar cargo</button>
                    <a href="{{ route('admin.positions.index') }}" class="btn-ghost">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
