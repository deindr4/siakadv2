{{-- resources/views/dashboard/bk.blade.php --}}
@extends('layouts.app')
@section('page-title','Dashboard BK')
@section('sidebar-menu') @include('partials.sidebar_bk') @endsection
@section('content')
@include('partials._dashboard_responsive')
<div class="page-title">
    <h1>👋 Selamat Datang, {{ auth()->user()->name }}!</h1>
    <p>{{ $sem?->nama ?? 'Belum ada semester aktif' }} &mdash; {{ now()->translatedFormat('l, d F Y') }}</p>
</div>

<div class="dash-grid-3">
    @foreach([['Pelanggaran Hari Ini',$pelanggaranStats->hari_ini,'linear-gradient(135deg,#dc2626,#b91c1c)','exclamation-triangle-fill'],['Pelanggaran Bln Ini',$pelanggaranStats->bulan_ini,'linear-gradient(135deg,#f59e0b,#d97706)','calendar-event-fill'],['Pelanggaran Semester',$pelanggaranStats->semester,'linear-gradient(135deg,#6366f1,#4f46e5)','bar-chart-fill']] as [$l,$v,$bg,$ic])
    <div class="dash-stat-card" style="background:{{ $bg }}"><i class="bi bi-{{ $ic }}"></i><div><p class="dsc-label">{{ $l }}</p><p class="dsc-val">{{ $v }}</p></div></div>
    @endforeach
</div>

<div class="dash-grid-2">
    <div class="card">
        <div class="card-header"><h3>📈 Pelanggaran 7 Hari Terakhir</h3></div>
        <div class="card-body"><canvas id="chartPelanggaran" height="130"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><h3>⚠️ Siswa Poin Tertinggi</h3><a href="{{ route('bk.pelanggaran.index') }}" style="font-size:12px;color:#6366f1;">Input →</a></div>
        <div class="card-body" style="padding:0;">
            @forelse($siswaPoinTertinggi as $i => $s)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid #f1f5f9;">
                <div style="width:26px;height:26px;border-radius:50%;background:{{ $i===0?'#fef3c7':($i===1?'#f1f5f9':'#fff7ed') }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:{{ $i===0?'#d97706':($i===1?'#64748b':'#c2410c') }};flex-shrink:0;">{{ $i+1 }}</div>
                <div style="flex:1;min-width:0;"><p style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $s->siswa?->nama ?? 'Siswa #'.$s->siswa_id }}</p><p style="font-size:11px;color:#94a3b8;">{{ $s->siswa?->nama_rombel ?? '-' }}</p></div>
                <span style="font-size:15px;font-weight:800;color:#dc2626;">{{ $s->total_poin }} poin</span>
            </div>
            @empty
            <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada data pelanggaran</div>
            @endforelse
        </div>
    </div>
</div>

<div class="dash-grid-2">
    <div class="card">
        <div class="card-header"><h3>📋 Rekap Per Jenis</h3><a href="{{ route('laporan.pelanggaran') }}" style="font-size:12px;color:#6366f1;">Laporan →</a></div>
        <div class="card-body" style="padding:0;">
            @forelse($rekapJenis as $r)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid #f1f5f9;">
                <div style="flex:1;min-width:0;"><p style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $r->jenisPelanggaran?->nama ?? '-' }}</p><p style="font-size:11px;color:#94a3b8;">Total poin: {{ $r->total_poin }}</p></div>
                <span style="font-size:15px;font-weight:800;color:#dc2626;">{{ $r->total }}x</span>
            </div>
            @empty
            <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada data</div>
            @endforelse
        </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="card">
            <div class="card-header"><h3>🏆 Prestasi</h3><a href="{{ route('prestasi.index') }}" style="font-size:12px;color:#6366f1;">Lihat →</a></div>
            <div class="card-body">
                <div class="dash-grid-2" style="gap:8px;margin-bottom:0;">
                    <div class="dash-box-center" style="background:#dcfce7;padding:12px;"><p class="dbc-val" style="color:#16a34a;">{{ $prestasiStats->total }}</p><p class="dbc-label" style="color:#16a34a;">Terverifikasi</p></div>
                    <div class="dash-box-center" style="background:#fef3c7;padding:12px;"><p class="dbc-val" style="color:#d97706;">{{ $prestasiStats->pending }}</p><p class="dbc-label" style="color:#d97706;">Pending</p></div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>🎫 Tiket</h3><a href="{{ route('tiket.index') }}" style="font-size:12px;color:#6366f1;">Kelola →</a></div>
            <div class="card-body">
                <div class="dash-grid-2" style="gap:8px;margin-bottom:0;">
                    <div class="dash-box-center" style="background:#dcfce7;padding:12px;"><p class="dbc-val" style="color:#16a34a;">{{ $tiketStats->open }}</p><p class="dbc-label" style="color:#16a34a;">Aktif</p></div>
                    <div class="dash-box-center" style="background:#fee2e2;padding:12px;"><p class="dbc-val" style="color:#dc2626;">{{ $tiketStats->tinggi }}</p><p class="dbc-label" style="color:#dc2626;">Prioritas ‼</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartPelanggaran'), {
    type:'line', data:{ labels:{!! json_encode($pelanggaran7Hari->pluck('label')) !!}, datasets:[{
        label:'Pelanggaran', data:{!! json_encode($pelanggaran7Hari->pluck('total')) !!},
        borderColor:'#dc2626', backgroundColor:'#dc262618', fill:true, tension:.4, pointRadius:4, pointBackgroundColor:'#dc2626'
    }]},
    options:{responsive:true,plugins:{legend:{display:false}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:'#f1f5f9'},ticks:{stepSize:1}}}}
});
</script>
@endsection
