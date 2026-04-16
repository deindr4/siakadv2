{{-- resources/views/dashboard/wakil_kepala.blade.php --}}
@extends('layouts.app')
@section('page-title','Dashboard Wakil Kepala')
@section('sidebar-menu') @include('partials.sidebar_wakil_kepala') @endsection
@section('content')
@include('partials._dashboard_responsive')
<div class="page-title">
    <h1>👋 Selamat Datang, {{ auth()->user()->name }}!</h1>
    <p>{{ $sem?->nama ?? 'Belum ada semester aktif' }} &mdash; {{ now()->translatedFormat('l, d F Y') }}</p>
</div>

<div class="dash-grid-4">
    @foreach([['Siswa Aktif',$stats->total_siswa,'#6366f1','person-badge-fill'],['Guru & GTK',$stats->total_guru,'#10b981','person-workspace'],['Rombel',$stats->total_rombel,'#f59e0b','diagram-3-fill'],['Mata Pelajaran',$stats->total_mapel,'#0284c7','book-fill']] as [$l,$v,$c,$ic])
    <div class="dash-mini-card" style="background:{{ $c }}11;border:1.5px solid {{ $c }}33;"><i class="bi bi-{{ $ic }}" style="color:{{ $c }};"></i><div><p class="dmc-label" style="color:{{ $c }};">{{ $l }}</p><p class="dmc-val" style="color:{{ $c }};">{{ number_format($v) }}</p></div></div>
    @endforeach
</div>

<div class="dash-grid-2-1">
    <div class="card">
        <div class="card-header"><h3>📋 Absensi Hari Ini</h3><span style="font-size:12px;color:#94a3b8;">{{ $absensiHariIni->kelas }} kelas</span></div>
        <div class="card-body">
            <div class="dash-grid-4" style="margin-bottom:14px;">
                @foreach([['Hadir',$absensiHariIni->hadir,'#16a34a'],['Sakit',$absensiHariIni->sakit,'#0284c7'],['Izin',$absensiHariIni->izin,'#d97706'],['Alpa',$absensiHariIni->alpha,'#dc2626']] as [$l,$v,$c])
                <div class="dash-box-center" style="background:{{ $c }}11;"><p class="dbc-val" style="color:{{ $c }};">{{ $v }}</p><p class="dbc-label" style="color:{{ $c }};">{{ $l }}</p></div>
                @endforeach
            </div>
            <canvas id="chartAbsensi" height="90"></canvas>
        </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:12px;">
        <div class="card">
            <div class="card-header"><h3>📓 Jurnal</h3></div>
            <div class="card-body">
                <div class="dash-grid-2" style="gap:8px;margin-bottom:0;">
                    @foreach([['Hari Ini',$jurnalStats->hari_ini,'#6366f1'],['Bulan Ini',$jurnalStats->bulan_ini,'#10b981']] as [$l,$v,$c])
                    <div class="dash-box-center" style="background:{{ $c }}11;padding:12px 6px;"><p class="dbc-val" style="color:{{ $c }};">{{ $v }}</p><p class="dbc-label" style="color:{{ $c }};">{{ $l }}</p></div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>⚠️ Pelanggaran</h3></div>
            <div class="card-body">
                <div class="dash-grid-2" style="gap:8px;margin-bottom:0;">
                    @foreach([['Bulan Ini',$pelanggaranStats->bulan_ini,'#dc2626'],['Semester',$pelanggaranStats->semester,'#d97706']] as [$l,$v,$c])
                    <div class="dash-box-center" style="background:{{ $c }}11;padding:12px 6px;"><p class="dbc-val" style="color:{{ $c }};">{{ $v }}</p><p class="dbc-label" style="color:{{ $c }};">{{ $l }}</p></div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>🏆 Prestasi</h3><a href="{{ route('laporan.prestasi') }}" style="font-size:12px;color:#6366f1;">Laporan →</a></div>
            <div class="card-body">
                <div class="dash-grid-2" style="gap:8px;margin-bottom:0;">
                    @foreach([['Terverifikasi',$prestasiStats->total,'#16a34a'],['Pending',$prestasiStats->pending,'#d97706']] as [$l,$v,$c])
                    <div class="dash-box-center" style="background:{{ $c }}11;padding:12px 6px;"><p class="dbc-val" style="color:{{ $c }};">{{ $v }}</p><p class="dbc-label" style="color:{{ $c }};">{{ $l }}</p></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartAbsensi'), {
    type:'bar', data:{ labels:{!! json_encode($absensi7Hari->pluck('label')) !!}, datasets:[
        {label:'Hadir',data:{!! json_encode($absensi7Hari->pluck('hadir')) !!},backgroundColor:'#22c55e',borderRadius:4},
        {label:'Alpa', data:{!! json_encode($absensi7Hari->pluck('alpha')) !!},backgroundColor:'#ef4444',borderRadius:4},
    ]},
    options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:11},boxWidth:12}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true}}}
});
</script>
@endsection
