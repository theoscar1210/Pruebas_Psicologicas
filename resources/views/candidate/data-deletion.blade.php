@extends('layouts.candidate')

@section('title', 'Solicitar eliminación de mis datos')

@section('content')
<div class="max-w-xl mx-auto py-8 px-4">

    <div class="mb-6">
        <a href="{{ route('candidate.dashboard') }}" class="text-sm text-slate-500 hover:text-slate-700">← Volver al inicio</a>
    </div>

    <h1 class="text-xl font-bold text-slate-900 mb-1">Solicitar eliminación de mis datos</h1>
    <p class="text-sm text-slate-500 mb-6">Derecho al olvido — Ley 1581 de 2012, Art. 8(c)</p>

    @if(session('info'))
    <div class="mb-5 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
        {{ session('info') }}
    </div>
    @endif

    @if($existing)
    {{-- Ya hay solicitud pendiente --}}
    <div class="card border-amber-200 bg-amber-50/50">
        <div class="card-body space-y-3">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-amber-800 text-sm">Solicitud en proceso</p>
                    <p class="text-xs text-amber-700 mt-0.5">Enviada el {{ $existing->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
            <p class="text-sm text-slate-700">Tu solicitud de eliminación de datos está siendo procesada. Será atendida en máximo <strong>15 días hábiles</strong> conforme a la Ley 1581 de 2012.</p>
        </div>
    </div>

    @else
    {{-- Formulario --}}
    <div class="card border-red-100">
        <div class="card-body space-y-5">

            <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800 space-y-2">
                <p class="font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    Acción irreversible
                </p>
                <p>Al confirmar esta solicitud, el equipo de selección eliminará <strong>permanentemente</strong> todos tus datos, resultados de pruebas y perfil psicológico. Esta acción <strong>no se puede deshacer</strong>.</p>
            </div>

            <form method="POST" action="{{ route('candidate.data-deletion.store') }}" class="space-y-4">
                @csrf

                <div class="form-group">
                    <label class="form-label text-sm">Motivo de la solicitud <span class="form-hint">(opcional)</span></label>
                    <textarea name="reason" rows="3"
                              class="textarea w-full text-sm"
                              placeholder="Puedes indicar el motivo si lo deseas (ej. proceso finalizado, datos incorrectos, etc.)">{{ old('reason') }}</textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-4">
                    <a href="{{ route('candidate.dashboard') }}" class="btn-ghost btn-sm">Cancelar</a>
                    <button type="submit" class="btn-danger btn-sm"
                            onclick="return confirm('¿Confirmas que deseas solicitar la eliminación de todos tus datos? Esta acción no se puede deshacer.')">
                        Enviar solicitud de eliminación
                    </button>
                </div>
            </form>

        </div>
    </div>
    @endif

    <p class="mt-6 text-xs text-slate-400 text-center">
        <a href="{{ route('privacy') }}" target="_blank" class="underline hover:text-slate-600">Política de Privacidad</a>
        · Ley 1581 de 2012 · SIC Colombia
    </p>

</div>
@endsection
