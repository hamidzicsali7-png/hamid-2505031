@extends('surat.templates.layout')

@section('isi')
    <p>Yang bertanda tangan di bawah ini, Kepala Desa {{ $pengaturan->nama_desa }}, Kecamatan {{ $pengaturan->kecamatan }}, Kabupaten {{ $pengaturan->kabupaten }}, menerangkan bahwa:</p>
    @include('surat.templates.partials.identitas-penduduk')
    <p>Sepanjang pengetahuan Pemerintah Desa, yang bersangkutan berkelakuan baik dan surat pengantar ini diberikan untuk mengurus Surat Keterangan Catatan Kepolisian (SKCK) dengan keperluan {{ data_get($pengajuan->data_tambahan, 'keperluan_skck', $pengajuan->keperluan) }}.</p>
    <p>Demikian surat pengantar ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>
@endsection
