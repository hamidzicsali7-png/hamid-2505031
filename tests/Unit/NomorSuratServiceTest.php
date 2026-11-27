<?php

namespace Tests\Unit;

use App\Models\JenisSurat;
use App\Models\PengaturanDesa;
use App\Services\NomorSuratService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class NomorSuratServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_nomor_berurutan_dan_counter_terpisah_per_jenis_surat_dan_tahun(): void
    {
        $this->travelTo(Carbon::parse('2026-11-27 09:00:00', 'Asia/Jakarta'));
        PengaturanDesa::factory()->create([
            'format_nomor_surat' => '470/{urutan}/DS/{bulan_romawi}/{tahun}',
        ]);
        $domisili = JenisSurat::factory()->create(['kode' => 'SKD']);
        $usaha = JenisSurat::factory()->create(['kode' => 'SKU']);
        $service = app(NomorSuratService::class);

        $this->assertSame('470/0001/DS/XI/2026', $service->generate($domisili));
        $this->assertSame('470/0002/DS/XI/2026', $service->generate($domisili));
        $this->assertSame('470/0001/DS/XI/2026', $service->generate($usaha));

        $this->travelTo(Carbon::parse('2027-01-03 09:00:00', 'Asia/Jakarta'));

        $this->assertSame('470/0001/DS/I/2027', $service->generate($domisili));
        $this->assertDatabaseCount('nomor_surat_counter', 3);
        $this->assertDatabaseHas('nomor_surat_counter', [
            'jenis_surat_id' => $domisili->id,
            'tahun' => 2026,
            'urutan_terakhir' => 2,
        ]);
    }

    public function test_format_nomor_lama_tetap_mendukung_placeholder_nomor_dan_kode(): void
    {
        $this->travelTo(Carbon::parse('2026-02-01 09:00:00', 'Asia/Jakarta'));
        PengaturanDesa::factory()->create([
            'format_nomor_surat' => '{nomor}/{kode}/{tahun}',
        ]);
        $jenisSurat = JenisSurat::factory()->create(['kode' => 'SKTM']);

        $this->assertSame(
            '0001/SKTM/2026',
            app(NomorSuratService::class)->generate($jenisSurat),
        );
    }
}
