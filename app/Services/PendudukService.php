<?php

namespace App\Services;

use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PendudukService
{
    public function cocokkanDenganAkun(User $user): bool
    {
        if (! $user->nik) {
            return false;
        }

        return DB::transaction(function () use ($user): bool {
            $penduduk = Penduduk::query()->where('nik', $user->nik)->first();

            if (! $penduduk || ($penduduk->user_id && (int) $penduduk->user_id !== (int) $user->id)) {
                return false;
            }

            $pendudukTerhubung = Penduduk::query()->where('user_id', $user->id)->first();

            if ($pendudukTerhubung && ! $pendudukTerhubung->is($penduduk)) {
                return false;
            }

            if (! $penduduk->user_id) {
                $penduduk->update(['user_id' => $user->id]);
            }

            return true;
        });
    }
}
