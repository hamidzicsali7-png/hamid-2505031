<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_surat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan')->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('penduduk_id')->constrained('penduduk')->restrictOnDelete();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surat')->restrictOnDelete();
            $table->string('keperluan');
            $table->json('data_tambahan')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'diverifikasi', 'disetujui', 'selesai', 'ditolak', 'revisi'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tgl_pengajuan')->useCurrent();
            $table->timestamps();

            $table->index('user_id');
            $table->index('penduduk_id');
            $table->index('jenis_surat_id');
            $table->index('status');
            $table->index('diverifikasi_oleh');
            $table->index('disetujui_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surat');
    }
};
