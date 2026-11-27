<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28mm 20mm 22mm; }
        body { color: #1f2937; font-family: DejaVu Sans, sans-serif; font-size: 10pt; }
        .kop { border-bottom: 3px double #111827; margin-bottom: 24px; padding-bottom: 12px; text-align: center; }
        .kop h1 { font-size: 16pt; margin: 0 0 5px; text-transform: uppercase; }
        .kop p { font-size: 9pt; margin: 3px 0; }
        h2 { font-size: 14pt; margin: 0 0 8px; text-align: center; }
        .periode { margin: 0 0 22px; text-align: center; }
        h3 { font-size: 11pt; margin: 18px 0 8px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #9ca3af; padding: 8px; }
        th { background: #f3f4f6; text-align: left; }
        .angka { text-align: right; width: 28%; }
    </style>
</head>
<body>
    <header class="kop">
        <h1>Pemerintah {{ $pengaturan->nama_desa }}</h1>
        <p>{{ $pengaturan->alamat }}</p>
        <p>Kecamatan {{ $pengaturan->kecamatan }}, Kabupaten {{ $pengaturan->kabupaten }}, {{ $pengaturan->provinsi }} {{ $pengaturan->kode_pos }}</p>
        <p>Telepon {{ $pengaturan->telepon }} · {{ $pengaturan->email }}</p>
    </header>

    <h2>Laporan Pelayanan Surat</h2>
    <p class="periode">
        Periode {{ $tanggalMulai->locale('id')->translatedFormat('d F Y') }}
        sampai {{ $tanggalSelesai->locale('id')->translatedFormat('d F Y') }}
    </p>

    <h3>Rekap Pengajuan per Jenis Surat</h3>
    <table>
        <thead><tr><th>Jenis surat</th><th class="angka">Jumlah pengajuan</th></tr></thead>
        <tbody>
            @forelse ($rekapJenis as $item)
                <tr><td>{{ $item->nama }}</td><td class="angka">{{ number_format($item->jumlah, 0, ',', '.') }}</td></tr>
            @empty
                <tr><td colspan="2">Belum ada jenis surat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>Rekap Pengajuan per Status</h3>
    <table>
        <thead><tr><th>Status</th><th class="angka">Jumlah pengajuan</th></tr></thead>
        <tbody>
            @foreach ($rekapStatus as $item)
                <tr><td>{{ $item->label }}</td><td class="angka">{{ number_format($item->jumlah, 0, ',', '.') }}</td></tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
