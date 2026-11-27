<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('log_aktivitas')
            ->where('aksi', 'pengajuan.status')
            ->where('keterangan', 'like', '% Catatan: %')
            ->orderBy('id')
            ->chunkById(100, function ($logs): void {
                foreach ($logs as $log) {
                    $posisiCatatan = strpos($log->keterangan, ' Catatan: ');

                    if ($posisiCatatan !== false) {
                        DB::table('log_aktivitas')
                            ->where('id', $log->id)
                            ->update([
                                'keterangan' => substr($log->keterangan, 0, $posisiCatatan).'.',
                            ]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Free-form notes are intentionally not restored to activity logs.
    }
};
