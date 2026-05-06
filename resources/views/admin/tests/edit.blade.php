@extends('layouts.admin')

@section('title', 'Editar Prueba')
@section('header', 'Editar: ' . $test->name)

@section('header-actions')
    <a href="{{ route('admin.tests.questions.index', $test) }}" class="btn-secondary btn-sm">
        Gestionar preguntas ({{ $test->questions()->count() }})
    </a>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.tests.update', $test) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nombre de la prueba <span class="form-required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $test->name) }}" required
                           class="input @error('name') input-error @enderror">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" rows="2"
                              class="textarea">{{ old('description', $test->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Instrucciones para el candidato</label>
                    <textarea name="instructions" rows="3"
                              class="textarea">{{ old('instructions', $test->instructions) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Tiempo límite (minutos)</label>
                        <input type="number" name="time_limit" value="{{ old('time_limit', $test->time_limit) }}"
                               min="1" max="300" placeholder="Sin límite"
                               class="input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Puntaje mínimo (%) <span class="form-required">*</span></label>
                        <input type="number" name="passing_score" value="{{ old('passing_score', $test->passing_score) }}"
                               min="1" max="100" required
                               class="input">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $test->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded accent-brand-600">
                    <label for="is_active" class="text-sm text-slate-700">Prueba activa</label>
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Actualizar prueba</button>
                    <a href="{{ route('admin.tests.index') }}" class="btn-ghost">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
