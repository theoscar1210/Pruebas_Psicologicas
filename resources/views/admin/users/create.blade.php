@extends('layouts.admin')

@section('title', 'Nuevo usuario')
@section('header', 'Nuevo usuario')

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">← Volver</a>
@endsection

@section('content')

<div class="max-w-lg">
    <div class="card">
        <div class="card-body">

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nombre completo <span class="form-required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="input {{ $errors->has('name') ? 'border-red-400' : '' }}"
                           placeholder="Ej. María García">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Correo electrónico <span class="form-required">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="input {{ $errors->has('email') ? 'border-red-400' : '' }}"
                           placeholder="usuario@empresa.com">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Rol <span class="form-required">*</span></label>
                    <x-form-select
                        name="role"
                        :options="[
                            ['value' => 'admin',     'label' => 'Administrador'],
                            ['value' => 'psicologo', 'label' => 'Psicólogo'],
                            ['value' => 'hr',        'label' => 'Recursos Humanos'],
                        ]"
                        :selected="old('role', '')"
                        placeholder="— Selecciona un rol —"
                        :required="true"
                        :error="$errors->has('role')"
                    />
                    @error('role')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-400 mt-1">
                        <strong>Admin:</strong> acceso total ·
                        <strong>Psicólogo:</strong> evaluaciones y perfiles ·
                        <strong>RRHH:</strong> candidatos y reportes
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña <span class="form-required">*</span></label>
                    <input type="password" name="password" required
                           class="input {{ $errors->has('password') ? 'border-red-400' : '' }}"
                           placeholder="Mínimo 8 caracteres"
                           autocomplete="new-password">
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmar contraseña <span class="form-required">*</span></label>
                    <input type="password" name="password_confirmation" required
                           class="input"
                           placeholder="Repite la contraseña"
                           autocomplete="new-password">
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', '1') === '1' ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-brand-700 focus:ring-brand-500">
                    <label for="is_active" class="text-sm text-slate-700 select-none cursor-pointer">
                        Usuario activo (puede iniciar sesión)
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Crear usuario</button>
                    <a href="{{ route('admin.users.index') }}" class="btn-ghost">Cancelar</a>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
