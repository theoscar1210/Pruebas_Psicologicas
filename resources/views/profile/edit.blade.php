<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 2FA --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Autenticación en dos pasos (2FA)</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Añade una capa extra de seguridad usando Google Authenticator u otra app TOTP.
                        </p>
                    </header>

                    @if(session('success'))
                        <div class="mt-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mt-5">
                        @if(auth()->user()->hasTwoFactor())
                            <div class="flex items-center gap-2 text-sm text-green-700 mb-4">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                </svg>
                                <span class="font-medium">2FA activo</span> — tu cuenta está protegida
                            </div>
                            <form method="POST" action="{{ route('two-factor.disable') }}" class="flex items-end gap-4">
                                @csrf
                                <div class="flex-1">
                                    <label for="password_2fa" class="block text-sm font-medium text-gray-700 mb-1">
                                        Confirma tu contraseña para desactivar
                                    </label>
                                    <input id="password_2fa"
                                           type="password"
                                           name="password"
                                           required
                                           autocomplete="current-password"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-400/25 focus:border-red-400">
                                    @error('password', 'twoFactorDisable')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit"
                                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition">
                                    Desactivar 2FA
                                </button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500 mb-4">El 2FA no está activado en tu cuenta.</p>
                            <a href="{{ route('two-factor.setup') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                               style="background: #0F766E;">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                </svg>
                                Activar autenticación en dos pasos
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
