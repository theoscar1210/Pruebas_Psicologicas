<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recuperar contraseña — MenteClara</title>
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
                Tu acceso,<br>seguro y<br>protegido.
            </h1>
            <p class="login-hero-desc text-base leading-relaxed mb-10 max-w-xs">
                Restablece tu contraseña de forma segura. Te enviaremos un enlace a tu correo registrado.
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
                    ¿Olvidaste tu contraseña?
                </h2>
                <p class="text-gray-500 text-sm mt-1.5">
                    Ingresa tu correo y te enviaremos un enlace para restablecerla.
                </p>
            </div>

            {{-- Mensaje de éxito --}}
            @if (session('status'))
                <div class="mb-5 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-sm font-medium text-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill="currentColor"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Correo electrónico
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="email"
                           placeholder="usuario@empresa.com"
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

                <button type="submit"
                        class="w-full py-2.5 px-4 rounded-lg text-sm font-semibold text-white bg-brand-700
                               hover:bg-brand-800 active:bg-brand-900 active:scale-[0.99]
                               transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600
                               shadow-sm hover:shadow-md">
                    Enviar enlace de recuperación
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
