<?php

namespace App\Services;

use App\Models\PengaturanDesa;
use Illuminate\Support\Facades\Cache;

class PengaturanDesaService
{
    private const CACHE_KEY = 'pengaturan_desa';

    private const CACHE_KEY_OPSIONAL = 'pengaturan_desa.opsional';

    public function ambil(): PengaturanDesa
    {
        return Cache::remember(
            self::CACHE_KEY,
            now()->addHour(),
            fn (): PengaturanDesa => PengaturanDesa::query()->firstOrFail(),
        );
    }

    public function ambilOpsional(): ?PengaturanDesa
    {
        return Cache::remember(
            self::CACHE_KEY_OPSIONAL,
            now()->addHour(),
            fn (): ?PengaturanDesa => PengaturanDesa::query()->first(),
        );
    }

    public function hapusCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY_OPSIONAL);
    }
}
