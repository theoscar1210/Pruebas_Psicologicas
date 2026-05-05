@extends('layouts.admin')

@section('title', 'Nuevo Candidato')
@section('header', 'Nuevo Candidato')

@section('content')

<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.candidates.store') }}" method="POST" class="space-y-5">
                @csrf

                @if($errors->has('document_number'))
                <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold">Candidato ya registrado</p>
                        <p class="mt-0.5">{{ $errors->first('document_number') }}</p>
                    </div>
                </div>
                @endif

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
                               class="input @error('document_number') input-error @enderror">
                        @error('document_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cargo al que aplica</label>
                        <x-form-select
                            name="position_id"
                            :options="$positions->map(fn($p) => ['value' => $p->id, 'label' => $p->name])->values()->toArray()"
                            :selected="old('position_id', '')"
                            placeholder="— Sin cargo asignado —"
                            hint="Al asignar un cargo, sus pruebas se asignarán automáticamente."
                        />
                        @error('position_id') <p class="form-error">{{ $message }}</p> @enderror
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
