<?php

namespace App\Policies;

use App\Models\PengaturanDesa;
use App\Models\User;

class PengaturanDesaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'kades', 'sekretaris']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, PengaturanDesa $pengaturanDesa): bool
    {
        return $user->hasRole('admin');
    }
}
