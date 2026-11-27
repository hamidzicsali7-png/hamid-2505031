@extends('surat.templates.layout')

@section('isi')
    <p>Yang bertanda tangan di bawah ini, Kepala Desa {{ $pengaturan->nama_desa }}, Kecamatan {{ $pengaturan->kecamatan }}, Kabupaten {{ $pengaturan->kabupaten }}, menerangkan bahwa:</p>
    @include('surat.templates.partials.identitas-penduduk')
    <p>Yang bersangkutan benar memiliki atau menjalankan usaha <strong>{{ data_get($pengajuan->data_tambahan, 'nama_usaha', '—') }}</strong> yang beralamat di {{ data_get($pengajuan->data_tambahan, 'alamat_usaha', '—') }}.</p>
    <p>Surat keterangan ini dibuat berdasarkan keterangan pemohon untuk keperluan {{ $pengajuan->keperluan }} dan agar dapat dipergunakan sebagaimana mestinya.</p>
@endsection
