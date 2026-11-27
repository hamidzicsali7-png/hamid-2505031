<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_kode_counter', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->unsignedInteger('urutan_terakhir')->default(0);
            $table->timestamps();
        });

        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->foreignId('pengajuan_surat_id')
                ->nullable()
                ->constrained('pengajuan_surat')
                ->nullOnDelete();
            $table->index('pengajuan_surat_id');
        });
    }

    public function down(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pengajuan_surat_id');
        });

        Schema::dropIfExists('pengajuan_kode_counter');
    }
};
