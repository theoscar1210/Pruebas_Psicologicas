@extends('layouts.candidate')

@section('title', 'Acceso Candidatos')

@section('content')

<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        {{-- Logo y título --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-2xl mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Portal de Candidatos</h1>
            <p class="text-gray-500 text-sm mt-1">Ingresa tu código de acceso para continuar</p>
        </div>

        {{-- Formulario --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

            <form method="POST" action="{{ route('candidate.access.post') }}" x-data="{ code: '', loading: false }" @submit="loading = true">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 text-center">
                        Código de acceso
                    </label>
                    <input
                        type="text"
                        name="access_code"
                        x-model="code"
                        maxlength="8"
                        autocomplete="off"
                        autofocus
                        placeholder="XXXXXXXX"
                        class="w-full text-center text-2xl font-mono font-bold tracking-[0.4em] uppercase border-2 rounded-xl px-4 py-4
                               focus:outline-none focus:border-indigo-500 transition
                               @error('access_code') border-red-400 bg-red-50 @else border-gray-200 @enderror"
                        value="{{ old('access_code') }}">

                    @error('access_code')
                        <p class="mt-2 text-center text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    :disabled="code.length < 8 || loading"
                    class="w-full py-3 rounded-xl font-semibold text-sm transition
                           bg-indigo-600 text-white hover:bg-indigo-700
                           disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-show="!loading">Ingresar →</span>
                    <span x-show="loading" x-cloak>Verificando…</span>
                </button>

            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Si no tienes tu código, contáctate con el área de Recursos Humanos.
        </p>

    </div>
</div>

@endsection
