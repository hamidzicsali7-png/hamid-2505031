@extends('surat.templates.layout')

@section('isi')
    <p>Yang bertanda tangan di bawah ini, Kepala Desa {{ $pengaturan->nama_desa }}, Kecamatan {{ $pengaturan->kecamatan }}, Kabupaten {{ $pengaturan->kabupaten }}, menerangkan bahwa telah lahir seorang anak:</p>
    <table class="identitas">
        <tr><td>Nama anak</td><td>:</td><td>{{ data_get($pengajuan->data_tambahan, 'nama_bayi', '—') }}</td></tr>
        <tr><td>Tanggal lahir</td><td>:</td><td>{{ data_get($pengajuan->data_tambahan, 'tanggal_lahir_bayi') ? \Illuminate\Support\Carbon::parse(data_get($pengajuan->data_tambahan, 'tanggal_lahir_bayi'))->locale('id')->translatedFormat('d F Y') : '—' }}</td></tr>
        <tr><td>Nama orang tua</td><td>:</td><td>{{ $pengajuan->penduduk->nama }}</td></tr>
        <tr><td>NIK orang tua</td><td>:</td><td>{{ $pengajuan->penduduk->nik }}</td></tr>
        <tr><td>Alamat</td><td>:</td><td>{{ $pengajuan->penduduk->alamat }}, RT {{ $pengajuan->penduduk->rt }} / RW {{ $pengajuan->penduduk->rw }}</td></tr>
    </table>
    <p>Surat keterangan ini dibuat berdasarkan dokumen dan keterangan pemohon untuk keperluan {{ $pengajuan->keperluan }}.</p>
@endsection
