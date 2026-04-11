<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 font-sans antialiased">

    {{-- ── Navbar (solo en páginas internas del candidato) ──────────────── --}}
    @hasSection('show-nav')
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-30">
        <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg width="28" height="28" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                    <circle cx="23" cy="23" r="21" fill="#14B8A6" fill-opacity="0.10"/>
                    <circle cx="23" cy="23" r="21" stroke="#0F766E" stroke-width="1.5"/>
                    <line x1="9" y1="31" x2="9" y2="15" stroke="#1E293B" stroke-width="2.4" stroke-linecap="round"/>
                    <line x1="9" y1="15" x2="17" y2="24" stroke="#1E293B" stroke-width="2.4" stroke-linecap="round"/>
                    <line x1="17" y1="24" x2="25" y2="15" stroke="#1E293B" stroke-width="2.4" stroke-linecap="round"/>
                    <line x1="25" y1="15" x2="25" y2="31" stroke="#1E293B" stroke-width="2.4" stroke-linecap="round"/>
                    <path d="M39 17 C36 13 28 13 28 23 C28 33 36 33 39 29" stroke="#0F766E" stroke-width="2.4" fill="none" stroke-linecap="round"/>
                    <circle cx="40" cy="10" r="3" fill="#14B8A6"/>
                </svg>
                <div class="leading-tight">
                    <span class="font-bold text-slate-800 text-sm tracking-tight">MenteClara</span>
                    <span class="text-[10px] text-slate-400 font-light italic block leading-none">by Emma Naranjo</span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                @yield('nav-info')
                <form method="POST" action="{{ route('candidate.logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-xs text-slate-400 hover:text-red-500 transition-colors font-medium">
                        Salir
                    </button>
                </form>
            </div>
        </div>
    </nav>
    @endif

    {{-- ── Flash messages ──────────────────────────────────────────────── --}}
    @if(session()->hasAny(['success', 'error', 'info']))
    <div class="max-w-2xl mx-auto px-4 pt-4 space-y-2">
        @if(session('success'))
            <div class="alert-success animate-fade-in">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-error animate-fade-in">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="alert-info animate-fade-in">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                {{ session('info') }}
            </div>
        @endif
    </div>
    @endif

    {{-- ── Contenido ────────────────────────────────────────────────────── --}}
    <main>
        @yield('content')
    </main>

</body>
</html>
