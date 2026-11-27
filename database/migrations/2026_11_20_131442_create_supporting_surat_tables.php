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
        Schema::create('syarat_surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surat')->cascadeOnDelete();
            $table->string('nama_dokumen');
            $table->boolean('wajib')->default(true);
            $table->timestamps();

            $table->index('jenis_surat_id');
        });

        Schema::create('dokumen_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_surat_id')->constrained('pengajuan_surat')->cascadeOnDelete();
            $table->string('nama_dokumen');
            $table->string('file_path');
            $table->timestamps();

            $table->index('pengajuan_surat_id');
        });

        Schema::create('pejabat_desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('nip')->nullable();
            $table->string('tanda_tangan_path')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index(['jabatan', 'aktif']);
        });

        Schema::create('surat_terbit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_surat_id')->unique()->constrained('pengajuan_surat')->cascadeOnDelete();
            $table->string('nomor_surat')->unique();
            $table->date('tgl_terbit');
            $table->foreignId('pejabat_id')->constrained('pejabat_desa')->restrictOnDelete();
            $table->string('file_pdf')->nullable();
            $table->string('kode_verifikasi')->unique();
            $table->timestamps();

            $table->index('pejabat_id');
        });

        Schema::create('pengaturan_desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->string('provinsi');
            $table->text('alamat');
            $table->string('kode_pos', 10);
            $table->string('telepon');
            $table->string('email');
            $table->string('logo_path')->nullable();
            $table->string('nama_kades');
            $table->string('format_nomor_surat');
            $table->timestamps();
        });

        Schema::create('nomor_surat_counter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surat')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedInteger('urutan_terakhir')->default(0);
            $table->timestamps();

            $table->unique(['jenis_surat_id', 'tahun']);
        });

        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi');
            $table->text('keterangan');
            $table->string('ip_address', 45);
            $table->timestamps();

            $table->index('user_id');
            $table->index('aksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
        Schema::dropIfExists('nomor_surat_counter');
        Schema::dropIfExists('pengaturan_desa');
        Schema::dropIfExists('surat_terbit');
        Schema::dropIfExists('pejabat_desa');
        Schema::dropIfExists('dokumen_pengajuan');
        Schema::dropIfExists('syarat_surat');
    }
};
