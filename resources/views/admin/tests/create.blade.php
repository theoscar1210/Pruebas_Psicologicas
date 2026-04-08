@extends('layouts.admin')

@section('title', 'Nueva Prueba')
@section('header', 'Nueva Prueba Psicológica')

@section('content')

<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.tests.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nombre de la prueba <span class="form-required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Ej: Test de Servicio al Cliente"
                           class="input @error('name') input-error @enderror">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" rows="2"
                              placeholder="Objetivo de esta prueba…"
                              class="textarea">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Instrucciones para el candidato</label>
                    <textarea name="instructions" rows="3"
                              placeholder="Texto que verá el candidato antes de iniciar la prueba…"
                              class="textarea">{{ old('instructions') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">
                            Tiempo límite (minutos)
                            <span class="form-hint ml-1">— vacío = sin límite</span>
                        </label>
                        <input type="number" name="time_limit" value="{{ old('time_limit') }}"
                               min="1" max="300" placeholder="30"
                               class="input @error('time_limit') input-error @enderror">
                        @error('time_limit') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Puntaje mínimo para aprobar (%) <span class="form-required">*</span></label>
                        <input type="number" name="passing_score" value="{{ old('passing_score', 65) }}"
                               min="1" max="100" required
                               class="input @error('passing_score') input-error @enderror">
                        @error('passing_score') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 rounded accent-brand-600">
                    <label for="is_active" class="text-sm text-slate-700">Prueba activa</label>
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Guardar y agregar preguntas →</button>
                    <a href="{{ route('admin.tests.index') }}" class="btn-ghost">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
