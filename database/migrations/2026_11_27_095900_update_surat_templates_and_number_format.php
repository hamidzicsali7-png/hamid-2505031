<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const TEMPLATES = [
        'SKD' => 'surat.templates.domisili',
        'SKTM' => 'surat.templates.sktm',
        'SKU' => 'surat.templates.usaha',
        'SKL' => 'surat.templates.kelahiran',
        'SKM' => 'surat.templates.kematian',
        'SKCK' => 'surat.templates.pengantar',
    ];

    public function up(): void
    {
        foreach (self::TEMPLATES as $kode => $template) {
            DB::table('jenis_surat')->where('kode', $kode)->update(['template_view' => $template]);
        }

        DB::table('pengaturan_desa')
            ->where('format_nomor_surat', '{nomor}/{kode}/{tahun}')
            ->update(['format_nomor_surat' => '470/{urutan}/DS/{bulan_romawi}/{tahun}']);
    }

    public function down(): void
    {
        foreach (self::TEMPLATES as $kode => $template) {
            DB::table('jenis_surat')
                ->where('kode', $kode)
                ->where('template_view', $template)
                ->update(['template_view' => 'surat.pdf']);
        }

        DB::table('pengaturan_desa')
            ->where('format_nomor_surat', '470/{urutan}/DS/{bulan_romawi}/{tahun}')
            ->update(['format_nomor_surat' => '{nomor}/{kode}/{tahun}']);
    }
};
