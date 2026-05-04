@extends('layouts.admin')

@section('title', 'Editar usuario — ' . $user->name)
@section('header', 'Editar usuario')

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">← Volver</a>
@endsection

@section('content')

<div class="max-w-lg">
    <div class="card">
        <div class="card-body">

            {{-- Info del usuario --}}
            <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-100">
                <div class="w-10 h-10 rounded-full bg-brand-700 flex items-center justify-center text-white font-bold flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                    <p class="text-xs text-slate-400">Registrado el {{ $user->created_at->format('d/m/Y') }}</p>
                </div>
                @if($user->hasTwoFactor())
                    <span class="ml-auto inline-flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-200">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                        2FA activo
                    </span>
                @endif
            </div>

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nombre completo <span class="form-required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="input {{ $errors->has('name') ? 'border-red-400' : '' }}">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Correo electrónico <span class="form-required">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="input {{ $errors->has('email') ? 'border-red-400' : '' }}">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Rol <span class="form-required">*</span></label>
                    <select name="role" required class="select {{ $errors->has('role') ? 'border-red-400' : '' }}">
                        <option value="admin"     {{ old('role', $user->role) === 'admin'     ? 'selected' : '' }}>Administrador</option>
                        <option value="psicologo" {{ old('role', $user->role) === 'psicologo' ? 'selected' : '' }}>Psicólogo</option>
                        <option value="hr"        {{ old('role', $user->role) === 'hr'        ? 'selected' : '' }}>Recursos Humanos</option>
                    </select>
                    @error('role')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-brand-700 focus:ring-brand-500">
                    <label for="is_active" class="text-sm text-slate-700 select-none cursor-pointer">
                        Usuario activo
                    </label>
                </div>

                {{-- Cambio de contraseña (opcional) --}}
                <div class="border-t border-slate-100 pt-5">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">
                        Nueva contraseña — dejar en blanco para no cambiar
                    </p>
                    <div class="space-y-4">
                        <div class="form-group">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password" name="password"
                                   class="input {{ $errors->has('password') ? 'border-red-400' : '' }}"
                                   placeholder="Mínimo 8 caracteres"
                                   autocomplete="new-password">
                            @error('password')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" name="password_confirmation"
                                   class="input"
                                   placeholder="Repite la nueva contraseña"
                                   autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Guardar cambios</button>
                    <a href="{{ route('admin.users.index') }}" class="btn-ghost">Cancelar</a>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
