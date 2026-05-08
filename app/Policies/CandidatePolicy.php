<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\User;

class CandidatePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'psicologo', 'hr']);
    }

    public function view(User $user, Candidate $candidate): bool
    {
        return in_array($user->role, ['admin', 'psicologo', 'hr']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'hr']);
    }

    public function update(User $user, Candidate $candidate): bool
    {
        return in_array($user->role, ['admin', 'hr']);
    }

    // Solo admin puede eliminar un candidato y sus datos asociados
    public function delete(User $user, Candidate $candidate): bool
    {
        return $user->role === 'admin';
    }

    // Acceso al perfil psicológico: admin y psicólogo (completo); hr (solo PDF del informe IA)
    public function viewReport(User $user, Candidate $candidate): bool
    {
        return in_array($user->role, ['admin', 'psicologo', 'hr']);
    }
}
