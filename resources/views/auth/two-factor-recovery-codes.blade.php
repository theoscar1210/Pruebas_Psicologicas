@extends('layouts.admin')

@section('title', 'Códigos de recuperación 2FA')

@section('content')
<div class="max-w-lg mx-auto px-4 py-10">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Códigos de recuperación</h1>
        <p class="text-gray-500 text-sm mt-1">Guárdalos en un lugar seguro — solo se muestran una vez</p>
    </div>

    {{-- Aviso crítico --}}
    <div class="mb-6 px-4 py-4 rounded-xl bg-amber-50 border border-amber-200 flex gap-3">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div class="text-sm text-amber-800">
            <p class="font-semibold mb-1">Guarda estos códigos ahora</p>
            <p>Si pierdes acceso a tu dispositivo de autenticación, solo podrás iniciar sesión con uno de estos códigos. Cada código es de <strong>uso único</strong> y no podrás volver a ver esta lista.</p>
        </div>
    </div>

    {{-- Lista de códigos --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-5">
        <div class="grid grid-cols-2 gap-3 mb-5">
            @foreach($codes as $code)
            <div class="font-mono text-sm font-semibold text-center py-2.5 px-3 rounded-lg bg-gray-50 border border-gray-200 tracking-widest text-gray-800 select-all">
                {{ $code }}
            </div>
            @endforeach
        </div>

        <button onclick="copyAllCodes()"
                class="w-full py-2 text-sm font-medium text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 rounded-lg transition">
            Copiar todos los códigos
        </button>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.profile.edit') }}"
           class="flex-1 py-2.5 text-center rounded-lg text-sm font-semibold text-white bg-brand-700
                  hover:bg-brand-800 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600">
            He guardado mis códigos
        </a>
    </div>

    <p class="mt-4 text-xs text-center text-gray-400">
        Puedes regenerar nuevos códigos en cualquier momento desde tu perfil.
    </p>
</div>

@push('scripts')
<script nonce="{{ app('csp-nonce') }}">
function copyAllCodes() {
    const codes = @json($codes);
    navigator.clipboard.writeText(codes.join('\n')).then(() => {
        const btn = event.target;
        const original = btn.textContent;
        btn.textContent = '¡Copiado!';
        btn.classList.add('bg-teal-100');
        setTimeout(() => { btn.textContent = original; btn.classList.remove('bg-teal-100'); }, 2000);
    });
}
</script>
@endpush

@endsection
