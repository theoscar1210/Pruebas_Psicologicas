@extends('layouts.admin')

@section('title', 'Nuevo Candidato')
@section('header', 'Nuevo Candidato')

@section('content')

<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.candidates.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 form-group">
                        <label class="form-label">Nombre completo <span class="form-required">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Ej: Carlos Andrés Gómez"
                               class="input @error('name') input-error @enderror">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="correo@ejemplo.com"
                               class="input @error('email') input-error @enderror">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="3001234567"
                               class="input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Número de documento</label>
                        <input type="text" name="document_number" value="{{ old('document_number') }}"
                               placeholder="CC / CE / Pasaporte"
                               class="input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cargo al que aplica</label>
                        <select name="position_id" class="select">
                            <option value="">— Sin cargo asignado —</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="form-hint">Al asignar un cargo, sus pruebas se asignarán automáticamente.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Crear candidato</button>
                    <a href="{{ route('admin.candidates.index') }}" class="btn-ghost">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
