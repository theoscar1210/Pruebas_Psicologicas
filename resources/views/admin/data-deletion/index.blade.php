@extends('layouts.admin')

@section('title', 'Solicitudes de eliminación de datos')
@section('header', 'Solicitudes de eliminación de datos')

@section('content')

@if(session('success'))
<div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">{{ session('success') }}</div>
@endif

{{-- Pendientes --}}
<div class="mb-8">
    <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
        Pendientes
        @if($pending->count())
        <span class="ml-2 bg-red-100 text-red-700 text-xs font-bold rounded-full px-2 py-0.5">{{ $pending->count() }}</span>
        @endif
    </h2>

    @if($pending->isEmpty())
    <div class="card border-slate-100">
        <div class="card-body text-center text-slate-400 text-sm py-8">No hay solicitudes pendientes.</div>
    </div>
    @else
    <div class="space-y-4">
        @foreach($pending as $req)
        <div class="card border-red-100">
            <div class="card-body">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <p class="font-semibold text-slate-800">{{ $req->candidate->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $req->candidate->position?->name ?? 'Sin cargo' }}
                            · Solicitado el {{ $req->created_at->format('d/m/Y H:i') }}
                            · IP: {{ $req->ip_address ?? '—' }}
                        </p>
                        @if($req->reason)
                        <p class="text-sm text-slate-600 mt-2 italic">"{{ $req->reason }}"</p>
                        @endif
                    </div>
                    <span class="text-xs font-bold bg-amber-100 text-amber-700 rounded-full px-3 py-1">Pendiente</span>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Aprobar --}}
                    <form method="POST" action="{{ route('admin.data-deletion.approve', $req) }}"
                          onsubmit="return confirm('¿Eliminar PERMANENTEMENTE todos los datos de {{ addslashes($req->candidate->name) }}? Esta acción no se puede deshacer.')">
                        @csrf
                        <div class="space-y-2">
                            <input type="text" name="admin_notes" placeholder="Notas (opcional)"
                                   class="input input-sm w-full text-xs">
                            <button type="submit" class="btn-danger btn-sm w-full justify-center">
                                Aprobar y eliminar datos
                            </button>
                        </div>
                    </form>

                    {{-- Rechazar --}}
                    <form method="POST" action="{{ route('admin.data-deletion.reject', $req) }}">
                        @csrf
                        <div class="space-y-2">
                            <input type="text" name="admin_notes" placeholder="Motivo del rechazo (obligatorio)"
                                   class="input input-sm w-full text-xs" required>
                            <button type="submit" class="btn-secondary btn-sm w-full justify-center">
                                Rechazar solicitud
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Historial --}}
@if($processed->isNotEmpty())
<div>
    <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Historial reciente</h2>
    <div class="card border-slate-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Candidato</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Procesado por</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Notas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($processed as $req)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-4 py-3 text-slate-700">{{ $req->candidate?->name ?? '(eliminado)' }}</td>
                    <td class="px-4 py-3">
                        @if($req->status === 'approved')
                        <span class="text-xs font-semibold text-red-700 bg-red-50 rounded-full px-2 py-0.5">Aprobado</span>
                        @else
                        <span class="text-xs font-semibold text-slate-600 bg-slate-100 rounded-full px-2 py-0.5">Rechazado</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $req->processedBy?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $req->processed_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $req->admin_notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
