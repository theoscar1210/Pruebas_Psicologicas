@extends('layouts.candidate')
@section('title', 'Acceso')

@section('content')

<div class="min-h-screen flex">

    {{-- Panel izquierdo: decorativo --}}
    <div class="hidden lg:flex lg:w-1/2 bg-brand-950 flex-col justify-between p-12">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                </svg>
            </div>
            <span class="text-white font-semibold">{{ config('app.name') }}</span>
        </div>

        <div>
            <h2 class="text-3xl font-bold text-white leading-tight mb-4">
                Evalúa tu potencial,<br>
                <span class="text-brand-400">no hay respuestas incorrectas.</span>
            </h2>
            <p class="text-brand-300/70 text-sm leading-relaxed max-w-sm">
                Responde con honestidad y tranquilidad.
                Tus respuestas nos ayudan a conocerte mejor.
            </p>

            {{-- Features --}}
            <div class="mt-10 space-y-4">
                @foreach([
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Tus respuestas se guardan automáticamente'],
                    ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Puedes tomarte el tiempo que necesitas'],
                    ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'text' => 'Tu información es confidencial'],
                ] as $f)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="text-sm text-brand-200/80">{{ $f['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <p class="text-brand-500 text-xs">© {{ date('Y') }} {{ config('app.name') }}</p>
    </div>

    {{-- Panel derecho: formulario --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">

            {{-- Logo mobile --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-9 h-9 bg-brand-700 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                    </svg>
                </div>
                <span class="font-semibold text-slate-800">{{ config('app.name') }}</span>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 mb-1">Ingresa tu código</h1>
            <p class="text-slate-500 text-sm mb-8">
                El área de RRHH te proporcionó un código de 8 caracteres.
            </p>

            <form
                method="POST"
                action="{{ route('candidate.access.post') }}"
                x-data="{ code: '{{ old('access_code', '') }}', loading: false }"
                @submit="loading = true">
                @csrf

                <div class="form-group mb-5">
                    <label class="form-label">Código de acceso</label>
                    <input
                        type="text"
                        name="access_code"
                        x-model="code"
                        maxlength="8"
                        autocomplete="off"
                        autofocus
                        placeholder="XXXXXXXX"
                        @input="code = code.toUpperCase()"
                        class="w-full text-center text-2xl font-mono font-bold tracking-[0.5em] uppercase py-4
                               border-2 rounded-xl
                               placeholder:text-slate-300 placeholder:tracking-[0.3em]
                               focus:outline-none focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10
                               transition-all duration-150
                               @error('access_code') border-red-400 bg-red-50 @else border-slate-200 @enderror">
                    @error('access_code')
                        <p class="form-error text-center">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    :disabled="code.length < 8 || loading"
                    class="btn-primary btn-lg w-full justify-center">
                    <span x-show="!loading">Ingresar a mis pruebas</span>
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Verificando…
                    </span>
                </button>

            </form>

            <p class="text-center text-xs text-slate-400 mt-6">
                ¿No tienes tu código?
                Comunícate con el área de Recursos Humanos.
            </p>

        </div>
    </div>

</div>

@endsection
