<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-md text-center">

    {{-- Logo --}}
    <div class="flex items-center justify-center gap-3 mb-10">
        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm"
             style="border: 2px solid #0F766E; background: #F0FDFA; color: #0F766E">MC</div>
        <div class="text-left">
            <div class="font-bold text-base leading-none" style="color: #0D3330">MenteClara</div>
            <div class="text-xs tracking-wide" style="color: #0D9488">by Emma Naranjo</div>
        </div>
    </div>

    {{-- Icono y código --}}
    <div class="mb-6">
        @yield('icon')
        <p class="text-7xl font-extrabold tracking-tight mt-4" style="color: #0F766E; opacity: 0.15">
            @yield('code')
        </p>
    </div>

    {{-- Mensaje principal --}}
    <h1 class="text-2xl font-bold text-slate-900 mb-2">@yield('heading')</h1>
    <p class="text-slate-500 text-sm leading-relaxed mb-8">@yield('description')</p>

    {{-- Acciones --}}
    <div class="flex items-center justify-center gap-3 flex-wrap">
        @auth
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition"
               style="background: #0F766E;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
                Ir al dashboard
            </a>
        @else
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition"
               style="background: #0F766E;">
                Iniciar sesión
            </a>
        @endauth

        <button onclick="history.back()"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:border-slate-300 transition">
            Volver atrás
        </button>
    </div>

</div>

</body>
</html>
