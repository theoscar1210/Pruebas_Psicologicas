<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verificación en dos pasos — MenteClara</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white">

<div class="min-h-screen flex">

    {{-- Panel izquierdo --}}
    <div class="hidden lg:flex lg:w-3/5 flex-col justify-between relative overflow-hidden"
         style="background: linear-gradient(155deg, #0D3330 0%, #0F766E 65%, #14B8A6 100%)">

        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full" style="background: rgba(255,255,255,0.04)"></div>
        <div class="absolute top-1/3 -right-16 w-64 h-64 rounded-full" style="background: rgba(255,255,255,0.03)"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full" style="background: rgba(255,255,255,0.04)"></div>
        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle, rgba(255,255,255,0.35) 1px, transparent 1px); background-size: 28px 28px;"></div>

        <div class="relative z-10 p-10 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full flex items-center justify-center font-bold text-sm tracking-tight"
                 style="background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.3); color: #ffffff">
                MC
            </div>
            <div>
                <div class="font-bold text-xl leading-none" style="color: #ffffff; letter-spacing: -0.3px">MenteClara</div>
                <div class="text-xs mt-0.5 tracking-wide" style="color: rgba(94,234,212,0.9)">by Emma Naranjo</div>
            </div>
        </div>

        <div class="relative z-10 px-10 pb-4">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-8"
                 style="background: rgba(255,255,255,0.12); border: 1.5px solid rgba(255,255,255,0.2)">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="rgba(94,234,212,0.9)" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                </svg>
            </div>
            <h1 class="font-extrabold leading-none mb-5"
                style="font-size: 38px; color: #ffffff; letter-spacing: -1.2px; line-height: 1.1">
                Acceso<br>seguro en<br>dos pasos
            </h1>
            <p class="text-base leading-relaxed max-w-xs" style="color: rgba(204,251,241,0.8)">
                Tu cuenta está protegida con autenticación de dos factores. Ingresa el código de tu aplicación autenticadora para continuar.
            </p>
        </div>

        <div class="relative z-10 px-10 pb-8">
            <div class="border-t pt-5" style="border-color: rgba(255,255,255,0.1)">
                <p class="text-xs italic" style="color: rgba(204,251,241,0.5)">"Donde el talento encuentra su medida"</p>
            </div>
        </div>
    </div>

    {{-- Panel derecho --}}
    <div class="w-full lg:w-2/5 flex flex-col items-center justify-center min-h-screen px-8 py-12 lg:px-14 bg-white">

        <div class="lg:hidden flex items-center gap-3 mb-10">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm"
                 style="border: 2px solid #0F766E; background: #F0FDFA; color: #0F766E">MC</div>
            <div>
                <div class="font-bold text-lg leading-none" style="color: #0D3330">MenteClara</div>
                <div class="text-xs mt-0.5 tracking-wide" style="color: #0D9488">by Emma Naranjo</div>
            </div>
        </div>

        <div class="w-full max-w-sm" x-data="{ useRecovery: false }">

            {{-- Cabecera dinámica --}}
            <div class="mb-8">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                     style="background: #F0FDFA; border: 1.5px solid #99F6E4">
                    <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight tracking-tight" x-text="useRecovery ? 'Código de recuperación' : 'Verificación en dos pasos'"></h2>
                <p class="text-gray-500 text-sm mt-1.5" x-text="useRecovery ? 'Ingresa uno de tus códigos de recuperación guardados' : 'Abre tu app de autenticación y escribe el código de 6 dígitos'"></p>
            </div>

            @if ($errors->any())
                <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 1a7 7 0 100 14A7 7 0 008 1zm.5 7.5h-1v-1h1v1zm0-2.5h-1V4.5h1V6z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Formulario TOTP --}}
            <form x-show="!useRecovery" method="POST" action="{{ route('two-factor.verify') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1.5">Código de autenticación</label>
                    <input id="code" type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                           required autofocus autocomplete="one-time-code" placeholder="000000"
                           class="w-full px-3.5 py-3 rounded-lg border text-center text-2xl font-mono tracking-widest bg-white text-gray-900
                                  transition duration-150 focus:outline-none focus:ring-2
                                  {{ $errors->has('code') ? 'border-red-400 bg-red-50 focus:ring-red-400/20' : 'border-gray-300 focus:ring-brand-500/25 focus:border-brand-600' }}">
                    @error('code')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-lg text-sm font-semibold text-white bg-brand-700
                               hover:bg-brand-800 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 shadow-sm">
                    Verificar código
                </button>
            </form>

            {{-- Formulario de código de recuperación --}}
            <form x-show="useRecovery" method="POST" action="{{ route('two-factor.recovery') }}" class="space-y-5" x-cloak>
                @csrf
                <div>
                    <label for="recovery_code" class="block text-sm font-medium text-gray-700 mb-1.5">Código de recuperación</label>
                    <input id="recovery_code" type="text" name="recovery_code" maxlength="12"
                           autocomplete="off" placeholder="XXXXX-XXXXX"
                           class="w-full px-3.5 py-3 rounded-lg border text-center text-lg font-mono tracking-widest bg-white text-gray-900
                                  transition duration-150 focus:outline-none focus:ring-2
                                  {{ $errors->has('recovery_code') ? 'border-red-400 bg-red-50 focus:ring-red-400/20' : 'border-gray-300 focus:ring-brand-500/25 focus:border-brand-600' }}">
                    @error('recovery_code')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-lg text-sm font-semibold text-white bg-amber-600
                               hover:bg-amber-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 shadow-sm">
                    Usar código de recuperación
                </button>
            </form>

            {{-- Toggle entre modos --}}
            <div class="mt-5 text-center">
                <button @click="useRecovery = !useRecovery" type="button"
                        class="text-sm text-teal-600 hover:text-teal-800 transition font-medium underline-offset-2 hover:underline">
                    <span x-text="useRecovery ? '← Usar mi app de autenticación' : 'No tengo mi dispositivo'"></span>
                </button>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                    Volver al inicio de sesión
                </a>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400 leading-relaxed">
                    Sistema de uso exclusivo para el equipo de RRHH<br>
                    <span class="font-medium" style="color: #0D9488">MenteClara</span> · Documento confidencial
                </p>
            </div>
        </div>
    </div>

</div>

</body>
</html>
