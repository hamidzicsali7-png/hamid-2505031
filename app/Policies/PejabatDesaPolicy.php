<?php

namespace App\Policies;

use App\Models\PejabatDesa;
use App\Models\User;

class PejabatDesaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'kades', 'sekretaris']);
    }

    public function view(User $user, PejabatDesa $pejabatDesa): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, PejabatDesa $pejabatDesa): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, PejabatDesa $pejabatDesa): bool
    {
        return $user->hasRole('admin');
    }
}
