<?php

namespace App\Policies;

use App\Models\PengajuanSurat;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PengajuanSuratPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'kades', 'sekretaris', 'warga']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PengajuanSurat $pengajuanSurat): bool
    {
        return $user->hasAnyRole(['admin', 'kades', 'sekretaris'])
            || $pengajuanSurat->penduduk?->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('warga');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PengajuanSurat $pengajuanSurat): bool
    {
        return $user->hasAnyRole(['admin', 'kades', 'sekretaris'])
            || ($pengajuanSurat->penduduk?->user_id === $user->id && in_array($pengajuanSurat->status->value, ['draft', 'revisi']));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PengajuanSurat $pengajuanSurat): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PengajuanSurat $pengajuanSurat): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PengajuanSurat $pengajuanSurat): bool
    {
        return false;
    }
}
