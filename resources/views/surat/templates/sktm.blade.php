@extends('surat.templates.layout')

@section('isi')
    <p>Yang bertanda tangan di bawah ini, Kepala Desa {{ $pengaturan->nama_desa }}, Kecamatan {{ $pengaturan->kecamatan }}, Kabupaten {{ $pengaturan->kabupaten }}, menerangkan bahwa:</p>
    @include('surat.templates.partials.identitas-penduduk')
    <p>Berdasarkan keterangan dan data yang tersedia di desa, yang bersangkutan termasuk keluarga dengan kondisi ekonomi kurang mampu, dengan penghasilan sekitar Rp {{ number_format((float) data_get($pengajuan->data_tambahan, 'penghasilan', 0), 0, ',', '.') }} per bulan dan {{ data_get($pengajuan->data_tambahan, 'jumlah_tanggungan', '—') }} orang tanggungan.</p>
    <p>Surat keterangan ini dibuat untuk keperluan {{ $pengajuan->keperluan }} dan agar dapat dipergunakan sebagaimana mestinya.</p>
@endsection
