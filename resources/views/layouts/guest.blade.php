<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MenteClara') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">

<div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">

    {{-- Logo --}}
    <a href="/" class="flex items-center gap-3 mb-8 group">
        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                    transition group-hover:scale-105"
             style="border: 2px solid #0F766E; background: #F0FDFA; color: #0F766E">
            MC
        </div>
        <div>
            <div class="font-bold text-lg leading-none transition group-hover:text-brand-700"
                 style="color: #0D3330">MenteClara</div>
            <div class="text-xs mt-0.5" style="color: #0D9488">by Emma Naranjo</div>
        </div>
    </a>

    {{-- Tarjeta principal --}}
    <div class="w-full max-w-md bg-white rounded-2xl shadow-card-lg border border-gray-100 px-8 py-8">
        {{ $slot }}
    </div>

    {{-- Pie de página --}}
    <p class="mt-6 text-xs text-gray-400 text-center">
        Sistema de uso exclusivo para RRHH · MenteClara
    </p>

</div>
</body>
</html>
