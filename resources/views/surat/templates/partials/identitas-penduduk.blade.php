<table class="identitas">
    <tr><td>Nama</td><td>:</td><td>{{ $pengajuan->penduduk->nama }}</td></tr>
    <tr><td>NIK</td><td>:</td><td>{{ $pengajuan->penduduk->nik }}</td></tr>
    <tr><td>Tempat, tanggal lahir</td><td>:</td><td>{{ $pengajuan->penduduk->tempat_lahir }}, {{ $pengajuan->penduduk->tgl_lahir?->locale('id')->translatedFormat('d F Y') }}</td></tr>
    <tr><td>Jenis kelamin</td><td>:</td><td>{{ $pengajuan->penduduk->jenis_kelamin }}</td></tr>
    <tr><td>Agama</td><td>:</td><td>{{ $pengajuan->penduduk->agama }}</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td>{{ $pengajuan->penduduk->pekerjaan }}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{ $pengajuan->penduduk->alamat }}, RT {{ $pengajuan->penduduk->rt }} / RW {{ $pengajuan->penduduk->rw }}{{ $pengajuan->penduduk->dusun ? ', '.$pengajuan->penduduk->dusun : '' }}</td></tr>
</table>
