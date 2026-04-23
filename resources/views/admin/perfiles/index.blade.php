@extends('layouts.admin')
@section('title', 'Perfiles Psicológicos')
@section('header', 'Perfiles Psicológicos')

@section('content')

<div class="table-wrapper">
    <table class="table-base">
        <thead>
            <tr>
                <th>Candidato</th>
                <th>Cargo</th>
                <th class="text-center">Recomendación</th>
                <th class="text-center">Ajuste</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Generado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($candidates as $candidate)
            @php $report = $candidate->latestReport; @endphp
            <tr>
                <td>
                    <p class="font-medium text-slate-900">{{ $candidate->name }}</p>
                    <p class="text-xs text-slate-400">{{ $candidate->email ?? $candidate->document_number ?? '—' }}</p>
                </td>
                <td class="text-slate-600 text-sm">{{ $candidate->position?->name ?? '—' }}</td>
                <td class="text-center">
                    @if($report?->recommendation)
                        <span class="{{ $report->recommendationBadgeClass() }} text-xs">
                            {{ $report->recommendationLabel() }}
                        </span>
                    @else
                        <span class="text-slate-300 text-xs">—</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($report?->adjustment_level)
                        <span class="{{ $report->adjustmentBadgeClass() }} text-xs capitalize">
                            {{ $report->adjustment_level }}
                        </span>
                    @else
                        <span class="text-slate-300 text-xs">—</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($report?->isCompleted())
                        <span class="badge-success">Completado</span>
                    @else
                        <span class="badge-warning">Borrador</span>
                    @endif
                </td>
                <td class="text-center text-xs text-slate-500">
                    {{ $report?->created_at->format('d/m/Y') ?? '—' }}
                </td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.profile.show', $candidate) }}"
                           class="text-brand-600 hover:text-brand-800 text-xs font-medium transition-colors">
                            Ver perfil
                        </a>
                        <a href="{{ route('admin.candidates.show', $candidate) }}"
                           class="text-slate-400 hover:text-slate-600 text-xs font-medium transition-colors">
                            Candidato
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                    Aún no hay perfiles psicológicos generados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($candidates->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $candidates->links() }}
        </div>
    @endif
</div>

@endsection
