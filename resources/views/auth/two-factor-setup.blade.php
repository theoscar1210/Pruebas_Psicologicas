@extends('layouts.admin')

@section('title', 'Configurar autenticación en dos pasos')

@section('content')
<div class="max-w-lg mx-auto px-4 py-10">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Autenticación en dos pasos</h1>
        <p class="text-gray-500 text-sm mt-1">Vincula tu cuenta con Google Authenticator u otra app TOTP</p>
    </div>

    @if(session('info'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 text-sm text-blue-700">
            {{ session('info') }}
        </div>
    @endif

    {{-- Paso 1: Escanear QR --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-5">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                 style="background: #0F766E">1</div>
            <div>
                <h2 class="font-semibold text-gray-900">Escanea el código QR</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Abre Google Authenticator, Microsoft Authenticator o cualquier app TOTP y escanea este código.
                </p>
            </div>
        </div>

        <div class="flex justify-center mb-4">
            <div class="p-3 bg-white border-2 border-gray-200 rounded-xl inline-block">
                @php
                    $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                        new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                        new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                    );
                    $writer = new \BaconQrCode\Writer($renderer);
                    $qrSvg = $writer->writeString($qrUrl);
                @endphp
                {!! $qrSvg !!}
            </div>
        </div>

        <div class="text-center">
            <p class="text-xs text-gray-400 mb-1">O ingresa la clave manualmente:</p>
            <code class="text-sm font-mono bg-gray-100 px-3 py-1.5 rounded-lg text-gray-700 tracking-widest select-all">
                {{ $secret }}
            </code>
        </div>
    </div>

    {{-- Paso 2: Confirmar código --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start gap-4 mb-5">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                 style="background: #0F766E">2</div>
            <div>
                <h2 class="font-semibold text-gray-900">Confirma con un código</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Ingresa el código de 6 dígitos que muestra la app para activar la protección.
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.enable') }}" class="space-y-4">
            @csrf
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Código de verificación
                </label>
                <input id="code"
                       type="text"
                       name="code"
                       inputmode="numeric"
                       pattern="[0-9]{6}"
                       maxlength="6"
                       required
                       autofocus
                       autocomplete="one-time-code"
                       placeholder="000000"
                       class="w-full px-3.5 py-2.5 rounded-lg border text-center text-xl font-mono tracking-widest
                              border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-600">
            </div>
            <button type="submit"
                    class="w-full py-2.5 rounded-lg text-sm font-semibold text-white bg-brand-700
                           hover:bg-brand-800 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600">
                Activar autenticación en dos pasos
            </button>
        </form>
    </div>

    <div class="mt-5 text-center">
        <a href="{{ route('admin.profile.edit') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
            Cancelar y volver al perfil
        </a>
    </div>
</div>
@endsection
