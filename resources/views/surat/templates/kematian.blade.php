@extends('surat.templates.layout')

@section('isi')
    <p>Yang bertanda tangan di bawah ini, Kepala Desa {{ $pengaturan->nama_desa }}, Kecamatan {{ $pengaturan->kecamatan }}, Kabupaten {{ $pengaturan->kabupaten }}, menerangkan bahwa warga berikut:</p>
    @include('surat.templates.partials.identitas-penduduk')
    <p>Telah meninggal dunia pada tanggal {{ data_get($pengajuan->data_tambahan, 'tanggal_meninggal') ? \Illuminate\Support\Carbon::parse(data_get($pengajuan->data_tambahan, 'tanggal_meninggal'))->locale('id')->translatedFormat('d F Y') : '—' }}.</p>
    <p>Surat keterangan ini dibuat berdasarkan keterangan dan dokumen yang disampaikan pemohon untuk keperluan {{ $pengajuan->keperluan }}.</p>
@endsection
