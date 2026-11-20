<x-app-layout>
<div class="max-w-[1400px] mx-auto">
  <div class="flex items-center justify-between mb-8"><div><p class="text-sm text-slate-500">Rabu, {{ now()->translatedFormat('d F Y') }}</p><h1 class="text-3xl font-bold mt-1">Ringkasan Desa</h1></div><div class="w-12 h-12 rounded-full bg-indigo-100 grid place-items-center text-indigo-700 font-bold">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div></div>
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    @foreach([['Diajukan',$ringkasan['diajukan'],'#6366f1'],['Diproses',$ringkasan['diproses'],'#8b5cf6'],['Selesai',$ringkasan['selesai'],'#10b981'],['Penduduk',$ringkasan['penduduk'],'#f59e0b']] as $item)
    <div class="bg-white rounded-3xl p-6 shadow-[0_12px_35px_rgba(76,67,126,.08)]"><div class="text-sm text-slate-500">{{ $item[0] }}</div><div class="text-3xl font-bold mt-3" style="color:{{ $item[2] }}">{{ $item[1] }}</div></div>
    @endforeach
  </div>
  <div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-[0_12px_35px_rgba(76,67,126,.08)]"><h2 class="font-bold mb-5">Pengajuan Terbaru</h2><div class="space-y-3">@foreach($terbaru as $p)<a href="{{ route('admin.pengajuan.show',$p) }}" class="flex items-center justify-between p-4 rounded-2xl bg-[#f8f7ff]"><div><p class="font-semibold">{{ $p->penduduk->nama }}</p><p class="text-xs text-slate-500 mt-1">{{ $p->jenisSurat->nama }}</p></div><span class="text-xs px-3 py-1 rounded-full bg-indigo-100 text-indigo-700">{{ $p->status->value }}</span></a>@endforeach</div></div>
    <div class="bg-white rounded-3xl p-6 shadow-[0_12px_35px_rgba(76,67,126,.08)]"><h2 class="font-bold mb-5">Pengajuan Bulanan</h2><canvas id="grafik"></canvas></div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>new Chart(document.getElementById('grafik'),{type:'line',data:{labels:['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],datasets:[{data:@json(array_map(fn($b)=>$bulanan[$b]??0, range('01','12'))),borderColor:'#6366f1',backgroundColor:'#e0e7ff',tension:.4,fill:true}]},options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}})</script>
</x-app-layout>