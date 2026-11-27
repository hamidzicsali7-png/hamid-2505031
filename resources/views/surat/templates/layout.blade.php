<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: A4 portrait;
            margin: 2.5cm 2.5cm 2.5cm 3cm;
        }

        body {
            color: #111;
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.45;
        }

        .kop {
            min-height: 2.3cm;
            position: relative;
            text-align: center;
        }

        .logo {
            height: 2cm;
            left: 0;
            object-fit: contain;
            position: absolute;
            top: 0;
            width: 2cm;
        }

        .kop p,
        .kop h1 {
            margin: 0;
        }

        .kop .pemerintah {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop .nama-desa {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop .alamat-desa {
            font-size: 10pt;
        }

        .garis-kop {
            border-bottom: 3px double #111;
            margin: 0.25cm 0 0.65cm;
        }

        .judul {
            margin: 0 0 0.7cm;
            text-align: center;
        }

        .judul h2 {
            font-size: 12pt;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .judul p {
            margin: 0.1cm 0 0;
        }

        p {
            margin: 0 0 0.35cm;
            text-align: justify;
        }

        table.identitas {
            border-collapse: collapse;
            margin: 0.35cm 0 0.45cm 0.6cm;
            width: 95%;
        }

        table.identitas td {
            padding: 0 0.1cm;
            vertical-align: top;
        }

        table.identitas td:first-child {
            width: 4.2cm;
        }

        .tanda-tangan {
            margin: 0.9cm 0.2cm 0 auto;
            page-break-inside: avoid;
            text-align: center;
            width: 7cm;
        }

        .tanda-tangan p {
            margin: 0;
            text-align: center;
        }

        .gambar-ttd {
            height: 1.5cm;
            margin: 0.1cm auto;
            object-fit: contain;
            width: 4cm;
        }

        .qr-verifikasi {
            bottom: 0;
            font-size: 8pt;
            position: fixed;
            right: 0;
            text-align: center;
            width: 2.8cm;
        }

        .qr-verifikasi img {
            height: 2.2cm;
            width: 2.2cm;
        }

        .qr-verifikasi p {
            font-size: 7pt;
            margin: 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="kop">
        @if ($logoDataUri)
            <img class="logo" src="{{ $logoDataUri }}" alt="Logo desa">
        @endif
        <p class="pemerintah">Pemerintah {{ $pengaturan->kabupaten }}</p>
        <h1 class="nama-desa">{{ $pengaturan->nama_desa }}</h1>
        <p class="alamat-desa">
            Kecamatan {{ $pengaturan->kecamatan }}, {{ $pengaturan->kabupaten }}, {{ $pengaturan->provinsi }}<br>
            {{ $pengaturan->alamat }} · Kode Pos {{ $pengaturan->kode_pos }}
        </p>
    </header>

    <div class="garis-kop"></div>

    <section class="judul">
        <h2>{{ $pengajuan->jenisSurat->nama }}</h2>
        <p>Nomor: {{ $suratTerbit->nomor_surat }}</p>
    </section>

    @yield('isi')

    <section class="tanda-tangan">
        <p>{{ $pengaturan->nama_desa }}, {{ $suratTerbit->tgl_terbit->locale('id')->translatedFormat('d F Y') }}</p>
        <p>{{ $suratTerbit->pejabatDesa->jabatan }}</p>
        @if ($tandaTanganDataUri)
            <img class="gambar-ttd" src="{{ $tandaTanganDataUri }}" alt="Tanda tangan pejabat">
        @else
            <div style="height: 1.7cm"></div>
        @endif
        <p><strong><u>{{ $suratTerbit->pejabatDesa->nama }}</u></strong></p>
        @if ($suratTerbit->pejabatDesa->nip)
            <p>NIP. {{ $suratTerbit->pejabatDesa->nip }}</p>
        @endif
    </section>

    <div class="qr-verifikasi">
        <img src="{{ $qrCodeDataUri }}" alt="QR verifikasi surat">
        <p>Pindai untuk verifikasi</p>
    </div>
</body>
</html>
