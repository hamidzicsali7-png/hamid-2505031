<?php

namespace App\Policies;

use App\Models\Penduduk;
use App\Models\User;

class PendudukPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'kades', 'sekretaris']);
    }

    public function view(User $user, Penduduk $penduduk): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Penduduk $penduduk): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Penduduk $penduduk): bool
    {
        return $user->hasRole('admin');
    }
}
