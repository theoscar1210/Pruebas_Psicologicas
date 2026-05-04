@extends('layouts.admin')

@section('title', 'Usuarios del sistema')
@section('header', 'Usuarios del sistema')

@section('header-actions')
    <a href="{{ route('admin.users.create') }}" class="btn-primary btn-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo usuario
    </a>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
        {{ session('error') }}
    </div>
@endif

{{-- Filtros --}}
<form method="GET" class="card mb-4">
    <div class="card-body py-3">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-48 form-group mb-0">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nombre o correo…" class="input">
            </div>
            <div class="min-w-40 form-group mb-0">
                <label class="form-label">Rol</label>
                <select name="role" class="select">
                    <option value="">Todos los roles</option>
                    <option value="admin"     {{ request('role') === 'admin'     ? 'selected' : '' }}>Administrador</option>
                    <option value="psicologo" {{ request('role') === 'psicologo' ? 'selected' : '' }}>Psicólogo</option>
                    <option value="hr"        {{ request('role') === 'hr'        ? 'selected' : '' }}>Recursos Humanos</option>
                </select>
            </div>
            <div class="flex items-center gap-2 pb-px">
                <button type="submit" class="btn-primary btn-sm">Filtrar</button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">Limpiar</a>
                @endif
            </div>
        </div>
    </div>
</form>

<div class="table-wrapper">
    <table class="table-base">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Correo electrónico</th>
                <th class="text-center">Rol</th>
                <th class="text-center">Estado</th>
                <th class="text-center">2FA</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-brand-700 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 text-sm">{{ $user->name }}</p>
                            @if($user->id === auth()->id())
                                <span class="text-[10px] text-brand-600 font-medium">Tú</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="text-slate-600 text-sm">{{ $user->email }}</td>
                <td class="text-center">
                    @php
                        $roleBadge = match($user->role) {
                            'admin'     => 'badge-danger',
                            'psicologo' => 'badge-purple',
                            'hr'        => 'badge-info',
                            default     => 'badge-neutral',
                        };
                        $roleLabel = match($user->role) {
                            'admin'     => 'Administrador',
                            'psicologo' => 'Psicólogo',
                            'hr'        => 'RRHH',
                            default     => $user->role,
                        };
                    @endphp
                    <span class="{{ $roleBadge }}">{{ $roleLabel }}</span>
                </td>
                <td class="text-center">
                    @if($user->is_active)
                        <span class="badge-success">Activo</span>
                    @else
                        <span class="badge-neutral">Inactivo</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($user->hasTwoFactor())
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100" title="2FA activo">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </span>
                    @else
                        <span class="text-slate-300" title="Sin 2FA">—</span>
                    @endif
                </td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-brand-600 hover:text-brand-800 text-xs font-medium transition-colors">Editar</a>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar el usuario {{ addslashes($user->name) }}? Esta acción no se puede deshacer.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium transition-colors">
                                    Eliminar
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                    No hay usuarios registrados.
                    <a href="{{ route('admin.users.create') }}" class="text-brand-600 hover:underline ml-1">Crear el primero</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($users->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    @endif
</div>

@endsection
