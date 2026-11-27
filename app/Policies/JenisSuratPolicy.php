<?php

namespace App\Policies;

use App\Models\JenisSurat;
use App\Models\User;

class JenisSuratPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'kades', 'sekretaris']);
    }

    public function view(User $user, JenisSurat $jenisSurat): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, JenisSurat $jenisSurat): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, JenisSurat $jenisSurat): bool
    {
        return $user->hasRole('admin');
    }
}
