{{-- resources/views/dashboard/kepala_sekolah.blade.php --}}
@extends('layouts.app')
@section('page-title', 'Dashboard Kepala Sekolah')
@section('sidebar-menu') @include('partials.sidebar_kepala_sekolah') @endsection

@section('content')
<div class="page-title">
    <h1>👋 Selamat Datang, {{ auth()->user()->name }}!</h1>
    <p>{{ $sem?->nama ?? 'Belum ada semester aktif' }} &mdash; {{ now()->translatedFormat('l, d F Y') }}</p>
</div>

{{-- STATS UTAMA --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:16px;">
    @foreach([
        ['Siswa Aktif',$stats->total_siswa,'linear-gradient(135deg,#6366f1,#8b5cf6)','person-badge-fill'],
        ['Guru & GTK',$stats->total_guru,'linear-gradient(135deg,#10b981,#059669)','person-workspace'],
        ['Rombel Aktif',$stats->total_rombel,'linear-gradient(135deg,#f59e0b,#d97706)','diagram-3-fill'],
    ] as [$label,$val,$bg,$icon])
    <div style="background:{{ $bg }};border-radius:14px;padding:18px 20px;color:#fff;display:flex;align-items:center;gap:14px;">
        <i class="bi bi-{{ $icon }}" style="font-size:34px;opacity:.85;"></i>
        <div><p style="font-size:11px;opacity:.8;font-weight:700;text-transform:uppercase;margin-bottom:2px;">{{ $label }}</p><p style="font-size:30px;font-weight:800;line-height:1;">{{ number_format($val) }}</p></div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

    {{-- Absensi Hari Ini --}}
    <div class="card">
        <div class="card-header">
            <h3>📋 Absensi Hari Ini</h3>
            <span style="font-size:12px;color:#94a3b8;">{{ $absensiStats->kelas }} kelas melapor</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px;">
                @foreach([['Hadir',$absensiStats->hadir,'#16a34a','person-check-fill'],['Sakit',$absensiStats->sakit,'#0284c7','bandaid-fill'],['Izin',$absensiStats->izin,'#d97706','file-earmark-check-fill'],['Alpa',$absensiStats->alpha,'#dc2626','person-x-fill']] as [$l,$v,$c,$ic])
                <div style="text-align:center;padding:10px 8px;background:{{ $c }}11;border-radius:10px;border:1px solid {{ $c }}22;">
                    <i class="bi bi-{{ $ic }}" style="font-size:18px;color:{{ $c }};display:block;margin-bottom:4px;"></i>
                    <p style="font-size:20px;font-weight:800;color:{{ $c }};line-height:1;">{{ $v }}</p>
                    <p style="font-size:10px;color:{{ $c }};font-weight:700;margin-top:3px;">{{ $l }}</p>
                </div>
                @endforeach
            </div>
            <p style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;">Kehadiran 7 Hari Terakhir</p>
            <canvas id="chartAbsensi" height="90"></canvas>
        </div>
    </div>

    {{-- Right column --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
        {{-- Prestasi --}}
        <div class="card">
            <div class="card-header"><h3>🏆 Prestasi Semester Ini</h3><a href="{{ route('laporan.prestasi') }}" style="font-size:12px;color:#6366f1;">Laporan →</a></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                    @foreach([['Total',$prestasiStats->total,'#6366f1'],['Nasional/Int\'l',$prestasiStats->nasional,'#dc2626'],['Provinsi',$prestasiStats->provinsi,'#d97706']] as [$l,$v,$c])
                    <div style="text-align:center;padding:10px;background:{{ $c }}11;border-radius:10px;">
                        <p style="font-size:20px;font-weight:800;color:{{ $c }};">{{ $v }}</p>
                        <p style="font-size:10px;color:{{ $c }};font-weight:700;">{{ $l }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        {{-- Tiket --}}
        <div class="card">
            <div class="card-header"><h3>🎫 Tiket & Pengaduan</h3><a href="{{ route('tiket.index') }}" style="font-size:12px;color:#6366f1;">Kelola →</a></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                    @foreach([['Aktif',$tiketStats->open,'#16a34a'],['Prioritas ‼',$tiketStats->tinggi,'#dc2626'],['Selesai',$tiketStats->selesai,'#64748b']] as [$l,$v,$c])
                    <div style="text-align:center;padding:10px;background:{{ $c }}11;border-radius:10px;">
                        <p style="font-size:20px;font-weight:800;color:{{ $c }};">{{ $v }}</p>
                        <p style="font-size:10px;color:{{ $c }};font-weight:700;">{{ $l }}</p>
                    </div>
                    @endforeach
                </div>
                @if($tiketStats->tinggi > 0)
                <a href="{{ route('tiket.index') }}?prioritas=tinggi" class="btn" style="width:100%;margin-top:10px;background:#fee2e2;color:#dc2626;text-align:center;font-size:12px;">
                    🔴 {{ $tiketStats->tinggi }} tiket prioritas tinggi menunggu
                </a>
                @endif
            </div>
        </div>
        {{-- Pelanggaran --}}
        <div class="card">
            <div class="card-header"><h3>⚠️ Pelanggaran</h3></div>
            <div class="card-body">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:13px;color:#374151;">Bulan {{ now()->translatedFormat('F') }}</span>
                    <span style="font-size:26px;font-weight:800;color:#dc2626;">{{ $pelanggaranBulanIni }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    {{-- Tiket Prioritas Tinggi --}}
    <div class="card">
        <div class="card-header"><h3>🔴 Tiket Prioritas Tinggi</h3><a href="{{ route('tiket.index') }}" style="font-size:12px;color:#6366f1;">Semua →</a></div>
        <div class="card-body" style="padding:0;">
            @forelse($tiketPrioritasTinggi as $t)
            <a href="{{ route('tiket.show',$t) }}" style="display:flex;align-items:center;gap:10px;padding:11px 16px;border-bottom:1px solid #f1f5f9;text-decoration:none;color:inherit;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <i class="bi bi-exclamation-circle-fill" style="color:#dc2626;font-size:16px;flex-shrink:0;"></i>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $t->judul }}</p>
                    <p style="font-size:11px;color:#94a3b8;">{{ $t->kategoriLabel() }} · {{ $t->created_at->diffForHumans() }}</p>
                </div>
            </a>
            @empty
            <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">Tidak ada tiket prioritas tinggi ✅</div>
            @endforelse
        </div>
    </div>
    {{-- Prestasi Unggulan --}}
    <div class="card">
        <div class="card-header"><h3>🥇 Prestasi Unggulan</h3><a href="{{ route('laporan.prestasi') }}" style="font-size:12px;color:#6366f1;">Laporan →</a></div>
        <div class="card-body" style="padding:0;">
            @forelse($prestasiTerbaru as $p)
            <a href="{{ route('prestasi.show',$p) }}" style="display:flex;align-items:center;gap:10px;padding:11px 16px;border-bottom:1px solid #f1f5f9;text-decoration:none;color:inherit;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <div style="width:34px;height:34px;border-radius:8px;background:#fef3c7;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">🏅</div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $p->nama_lomba }}</p>
                    <p style="font-size:11px;color:#94a3b8;">{{ $p->juara }} · {{ ucfirst($p->tingkat) }}</p>
                </div>
            </a>
            @empty
            <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada prestasi unggulan</div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartAbsensi'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($absensi7Hari->pluck('label')) !!},
        datasets: [
            { label: 'Hadir', data: {!! json_encode($absensi7Hari->pluck('hadir')) !!}, backgroundColor: '#22c55e', borderRadius: 4 },
            { label: 'Alpa',  data: {!! json_encode($absensi7Hari->pluck('alpha')) !!}, backgroundColor: '#ef4444', borderRadius: 4 },
        ]
    },
    options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{ font:{size:11}, boxWidth:12 } } }, scales:{ x:{ grid:{display:false} }, y:{ beginAtZero:true, grid:{color:'#f1f5f9'} } } }
});
</script>
@endsection
