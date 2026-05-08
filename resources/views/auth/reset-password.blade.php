<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nueva contraseña — MenteClara</title>
    <link rel="icon" href="/images/isotipo.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white">

<div class="min-h-screen flex">

    {{-- ══ PANEL IZQUIERDO: Marca ══════════════════════════════════════════════ --}}
    <div class="login-panel-left hidden lg:flex lg:w-3/5 flex-col justify-between relative overflow-hidden">

        <div class="login-decor-sm absolute -top-24 -right-24 w-96 h-96 rounded-full"></div>
        <div class="login-decor-xs absolute top-1/3 -right-16 w-64 h-64 rounded-full"></div>
        <div class="login-decor-sm absolute -bottom-20 -left-20 w-80 h-80 rounded-full"></div>
        <div class="login-decor-xs absolute bottom-1/4 right-10 w-40 h-40 rounded-full"></div>
        <div class="login-dot-pattern absolute inset-0 opacity-10"></div>

        <div class="relative z-10 p-10 flex items-center gap-4">
            <div class="login-logo-circle w-11 h-11 rounded-full flex items-center justify-center font-bold text-sm tracking-tight">MC</div>
            <div>
                <div class="login-brand-name font-bold text-xl leading-none">MenteClara</div>
                <div class="login-brand-sub text-xs mt-0.5 tracking-wide">by Emma Naranjo</div>
            </div>
        </div>

        <div class="relative z-10 px-10 pb-4">
            <h1 class="login-hero-h1 font-extrabold leading-none mb-5">
                Nueva<br>contraseña,<br>nuevo acceso.
            </h1>
            <p class="login-hero-desc text-base leading-relaxed mb-10 max-w-xs">
                Elige una contraseña segura para proteger tu acceso al sistema de evaluación psicológica.
            </p>
        </div>

        <div class="relative z-10 px-10 pb-8">
            <div class="login-divider border-t pt-5">
                <p class="login-quote text-xs italic">"Donde el talento encuentra su medida"</p>
            </div>
        </div>
    </div>

    {{-- ══ PANEL DERECHO: Formulario ═══════════════════════════════════════════ --}}
    <div class="w-full lg:w-2/5 flex flex-col items-center justify-center min-h-screen px-8 py-12 lg:px-14 bg-white">

        {{-- Logo móvil --}}
        <div class="lg:hidden flex items-center gap-3 mb-10">
            <div class="login-mob-circle w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm">MC</div>
            <div>
                <div class="login-mob-brand font-bold text-lg leading-none">MenteClara</div>
                <div class="login-mob-sub text-xs mt-0.5 tracking-wide">by Emma Naranjo</div>
            </div>
        </div>

        <div class="w-full max-w-sm">

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 leading-tight tracking-tight">
                    Restablecer contraseña
                </h2>
                <p class="text-gray-500 text-sm mt-1.5">
                    Ingresa tu nueva contraseña para recuperar el acceso.
                </p>
            </div>

            @if ($errors->any() && !$errors->has('email') && !$errors->has('password') && !$errors->has('password_confirmation'))
                <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5" x-data="{ verPass: false, verConf: false }">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Correo --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Correo electrónico
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email', $request->email) }}"
                           required
                           autofocus
                           autocomplete="username"
                           class="w-full px-3.5 py-2.5 rounded-lg border text-sm bg-white text-gray-900 placeholder-gray-400
                                  transition duration-150 focus:outline-none focus:ring-2
                                  {{ $errors->has('email')
                                      ? 'border-red-400 bg-red-50 focus:ring-red-400/20 focus:border-red-500'
                                      : 'border-gray-300 hover:border-gray-400 focus:ring-brand-500/25 focus:border-brand-600' }}">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 12 12">
                                <path d="M6 1a5 5 0 100 10A5 5 0 006 1zm.5 7.5h-1v-1h1v1zm0-2.5h-1V3.5h1V6z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Nueva contraseña --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nueva contraseña
                    </label>
                    <div class="relative">
                        <input id="password"
                               :type="verPass ? 'text' : 'password'"
                               name="password"
                               required
                               autocomplete="new-password"
                               placeholder="Mínimo 8 caracteres"
                               class="w-full px-3.5 py-2.5 pr-10 rounded-lg border text-sm bg-white text-gray-900 placeholder-gray-400
                                      transition duration-150 focus:outline-none focus:ring-2
                                      {{ $errors->has('password')
                                          ? 'border-red-400 bg-red-50 focus:ring-red-400/20 focus:border-red-500'
                                          : 'border-gray-300 hover:border-gray-400 focus:ring-brand-500/25 focus:border-brand-600' }}">
                        <button type="button" @click="verPass = !verPass"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <svg x-show="!verPass" class="w-4 h-4" fill="none" viewBox="0 0 20 20">
                                <path d="M10 4C5.5 4 2 10 2 10s3.5 6 8 6 8-6 8-6-3.5-6-8-6zm0 10a4 4 0 110-8 4 4 0 010 8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <circle cx="10" cy="10" r="2" fill="currentColor"/>
                            </svg>
                            <svg x-show="verPass" class="w-4 h-4" fill="none" viewBox="0 0 20 20" style="display:none">
                                <path d="M3 3l14 14M8.46 8.52A4 4 0 0013.5 13.5M6.1 6.14C4.37 7.3 2.9 9 2 10c1.74 2.94 4.72 6 8 6a8.42 8.42 0 003.9-.97M10 4c.68 0 1.35.1 2 .27C14.82 5.38 17.22 7.75 18 10c-.56.95-1.37 2-2.35 2.9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 12 12">
                                <path d="M6 1a5 5 0 100 10A5 5 0 006 1zm.5 7.5h-1v-1h1v1zm0-2.5h-1V3.5h1V6z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Confirmar contraseña --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Confirmar contraseña
                    </label>
                    <div class="relative">
                        <input id="password_confirmation"
                               :type="verConf ? 'text' : 'password'"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               placeholder="Repite la contraseña"
                               class="w-full px-3.5 py-2.5 pr-10 rounded-lg border text-sm bg-white text-gray-900 placeholder-gray-400
                                      transition duration-150 focus:outline-none focus:ring-2
                                      {{ $errors->has('password_confirmation')
                                          ? 'border-red-400 bg-red-50 focus:ring-red-400/20 focus:border-red-500'
                                          : 'border-gray-300 hover:border-gray-400 focus:ring-brand-500/25 focus:border-brand-600' }}">
                        <button type="button" @click="verConf = !verConf"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <svg x-show="!verConf" class="w-4 h-4" fill="none" viewBox="0 0 20 20">
                                <path d="M10 4C5.5 4 2 10 2 10s3.5 6 8 6 8-6 8-6-3.5-6-8-6zm0 10a4 4 0 110-8 4 4 0 010 8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <circle cx="10" cy="10" r="2" fill="currentColor"/>
                            </svg>
                            <svg x-show="verConf" class="w-4 h-4" fill="none" viewBox="0 0 20 20" style="display:none">
                                <path d="M3 3l14 14M8.46 8.52A4 4 0 0013.5 13.5M6.1 6.14C4.37 7.3 2.9 9 2 10c1.74 2.94 4.72 6 8 6a8.42 8.42 0 003.9-.97M10 4c.68 0 1.35.1 2 .27C14.82 5.38 17.22 7.75 18 10c-.56.95-1.37 2-2.35 2.9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 12 12">
                                <path d="M6 1a5 5 0 100 10A5 5 0 006 1zm.5 7.5h-1v-1h1v1zm0-2.5h-1V3.5h1V6z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-lg text-sm font-semibold text-white bg-brand-700
                               hover:bg-brand-800 active:bg-brand-900 active:scale-[0.99]
                               transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600
                               shadow-sm hover:shadow-md">
                    Restablecer contraseña
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}"
                   class="text-sm text-brand-700 hover:text-brand-800 font-medium transition duration-150">
                    ← Volver al inicio de sesión
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
