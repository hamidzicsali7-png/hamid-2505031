@extends('surat.templates.layout')

@section('isi')
    <p>Yang bertanda tangan di bawah ini, Kepala Desa {{ $pengaturan->nama_desa }}, Kecamatan {{ $pengaturan->kecamatan }}, Kabupaten {{ $pengaturan->kabupaten }}, menerangkan bahwa:</p>
    @include('surat.templates.partials.identitas-penduduk')
    <p>Berdasarkan keterangan yang diberikan, yang bersangkutan benar berdomisili di wilayah Desa {{ $pengaturan->nama_desa }} selama {{ data_get($pengajuan->data_tambahan, 'lama_tinggal', '—') }}.</p>
    <p>Surat keterangan ini dibuat untuk keperluan {{ $pengajuan->keperluan }} dan agar dapat dipergunakan sebagaimana mestinya.</p>
@endsection
