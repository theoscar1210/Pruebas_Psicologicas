@extends('layouts.admin')

@section('title', 'Editar Candidato')
@section('header', 'Editar: ' . $candidate->name)

@section('content')

<div class="max-w-2xl">
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.candidates.update', $candidate) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="col-span-2 form-group">
                        <label class="form-label">Nombre completo <span class="form-required">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $candidate->name) }}" required
                               class="input @error('name') input-error @enderror">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $candidate->email) }}"
                               class="input @error('email') input-error @enderror">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone', $candidate->phone) }}"
                               class="input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Número de documento</label>
                        <input type="text" name="document_number" value="{{ old('document_number', $candidate->document_number) }}"
                               class="input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cargo</label>
                        <x-form-select
                            name="position_id"
                            :options="$positions->map(fn($p) => ['value' => $p->id, 'label' => $p->name])->values()->toArray()"
                            :selected="old('position_id', $candidate->position_id ?? '')"
                            placeholder="— Sin cargo —"
                        />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <x-form-select
                            name="status"
                            :options="[
                                ['value' => 'active',    'label' => 'Activo'],
                                ['value' => 'completed', 'label' => 'Completado'],
                                ['value' => 'inactive',  'label' => 'Inactivo'],
                            ]"
                            :selected="old('status', $candidate->status)"
                            :required="true"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Actualizar candidato</button>
                    <a href="{{ route('admin.candidates.show', $candidate) }}" class="btn-ghost">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
