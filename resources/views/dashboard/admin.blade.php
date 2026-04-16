{{-- resources/views/dashboard/admin.blade.php --}}
@extends('layouts.app')
@section('page-title', 'Dashboard Admin')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
@include('partials._dashboard_responsive')

<div class="page-title" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div>
        <h1>👋 Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p>{{ $sem?->nama ?? 'Belum ada semester aktif' }} &mdash; {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <button type="button" id="btnClearCache" onclick="clearAllCache()"
        class="btn btn-sm"
        style="background:#fef3c7;color:#d97706;border:1px solid #fde68a;white-space:nowrap;align-self:center;">
        <i class="bi bi-arrow-clockwise"></i> Clear Cache
    </button>
</div>

<div class="dash-grid-3">
    <a href="{{ route('admin.siswa.index') }}" class="dash-stat-card" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
        <i class="bi bi-person-badge-fill"></i><div><p class="dsc-label">Siswa Aktif</p><p class="dsc-val">{{ number_format($stats->total_siswa) }}</p></div>
    </a>
    <a href="{{ route('admin.guru.index') }}" class="dash-stat-card" style="background:linear-gradient(135deg,#10b981,#059669)">
        <i class="bi bi-person-workspace"></i><div><p class="dsc-label">Guru & GTK</p><p class="dsc-val">{{ number_format($stats->total_guru) }}</p></div>
    </a>
    <a href="{{ route('admin.rombel.index') }}" class="dash-stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
        <i class="bi bi-diagram-3-fill"></i><div><p class="dsc-label">Rombel Aktif</p><p class="dsc-val">{{ $stats->total_rombel }}</p></div>
    </a>
</div>

<div class="dash-grid-4">
    @foreach([['Mata Pelajaran',$stats->total_mapel,'#0284c7','book-fill'],['Alumni',$stats->total_alumni,'#7c3aed','mortarboard-fill'],['Jurnal Hari Ini',$jurnalHariIni,'#16a34a','journal-text'],['Pelanggaran Bln Ini',$pelanggaranBulanIni,'#dc2626','exclamation-triangle-fill']] as [$l,$v,$c,$ic])
    <div class="dash-mini-card" style="background:{{ $c }}11;border:1.5px solid {{ $c }}33;">
        <i class="bi bi-{{ $ic }}" style="color:{{ $c }};"></i>
        <div><p class="dmc-label" style="color:{{ $c }};">{{ $l }}</p><p class="dmc-val" style="color:{{ $c }};">{{ $v }}</p></div>
    </div>
    @endforeach
</div>

<div class="dash-grid-2-1">
    <div class="card">
        <div class="card-header"><h3>📋 Absensi Hari Ini</h3><span style="font-size:12px;color:#94a3b8;">{{ $absensiStats->kelas }} kelas</span></div>
        <div class="card-body">
            <div class="dash-grid-4" style="margin-bottom:14px;">
                @foreach([['Hadir',$absensiStats->hadir,'#16a34a','person-check-fill'],['Sakit',$absensiStats->sakit,'#0284c7','bandaid-fill'],['Izin',$absensiStats->izin,'#d97706','file-earmark-check-fill'],['Alpa',$absensiStats->alpha,'#dc2626','person-x-fill']] as [$l,$v,$c,$ic])
                <div class="dash-box-center" style="background:{{ $c }}11;border:1px solid {{ $c }}22;">
                    <i class="bi bi-{{ $ic }}" style="font-size:18px;color:{{ $c }};display:block;margin-bottom:4px;"></i>
                    <p class="dbc-val" style="color:{{ $c }};">{{ $v }}</p><p class="dbc-label" style="color:{{ $c }};">{{ $l }}</p>
                </div>
                @endforeach
            </div>
            <p style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;">7 Hari Terakhir</p>
            <canvas id="chartAbsensi" height="90"></canvas>
        </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="card" style="flex:1;">
            <div class="card-header"><h3>🏆 Prestasi</h3><a href="{{ route('prestasi.index') }}" style="font-size:12px;color:#6366f1;">Lihat →</a></div>
            <div class="card-body">
                @foreach([['Terverifikasi',$prestasiStats->total,'#16a34a'],['Nasional/Int\'l',$prestasiStats->nasional,'#dc2626'],['Menunggu Verif.',$prestasiStats->pending,'#d97706']] as [$l,$v,$c])
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:12px;color:#374151;">{{ $l }}</span><span style="font-size:18px;font-weight:800;color:{{ $c }};">{{ $v }}</span>
                </div>
                @endforeach
                @if($prestasiStats->pending > 0)
                <a href="{{ route('prestasi.index') }}?status=pending" class="btn" style="width:100%;margin-top:10px;background:#fef3c7;color:#d97706;text-align:center;font-size:12px;">⚠️ {{ $prestasiStats->pending }} menunggu verifikasi</a>
                @endif
            </div>
        </div>
        <div class="card" style="flex:1;">
            <div class="card-header"><h3>🎫 Tiket</h3><a href="{{ route('tiket.index') }}" style="font-size:12px;color:#6366f1;">Kelola →</a></div>
            <div class="card-body">
                @foreach([['Aktif',$tiketStats->open,'#16a34a'],['Prioritas Tinggi',$tiketStats->tinggi,'#dc2626'],['Terkunci',$tiketStats->terkunci,'#94a3b8']] as [$l,$v,$c])
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:12px;color:#374151;">{{ $l }}</span><span style="font-size:18px;font-weight:800;color:{{ $c }};">{{ $v }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="dash-grid-2">
    <div class="card">
        <div class="card-header"><h3>🎫 Tiket Terbaru</h3><a href="{{ route('tiket.index') }}" style="font-size:12px;color:#6366f1;">Semua →</a></div>
        <div class="card-body" style="padding:0;">
            @forelse($tiketTerbaru as $t)
            <a href="{{ route('tiket.show',$t) }}" class="dash-feed-row">
                <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:{{ $t->prioritas==='tinggi'?'#dc2626':($t->prioritas==='sedang'?'#d97706':'#94a3b8') }};"></div>
                <div style="flex:1;min-width:0;"><p style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $t->judul }}</p><p style="font-size:11px;color:#94a3b8;">{{ $t->is_anonim?'Anonim':$t->user?->name }} · {{ $t->created_at->diffForHumans() }}</p></div>
                <span style="padding:2px 7px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap;background:{{ $t->statusBg() }};color:{{ $t->statusColor() }};">{{ $t->statusLabel() }}</span>
            </a>
            @empty
            <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;"><i class="bi bi-ticket-perforated" style="font-size:28px;display:block;margin-bottom:6px;"></i>Tidak ada tiket aktif</div>
            @endforelse
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>🏆 Prestasi Terbaru</h3><a href="{{ route('prestasi.index') }}" style="font-size:12px;color:#6366f1;">Semua →</a></div>
        <div class="card-body" style="padding:0;">
            @forelse($prestasiTerbaru as $p)
            <a href="{{ route('prestasi.show',$p) }}" class="dash-feed-row">
                <div style="width:34px;height:34px;border-radius:8px;background:#fef3c7;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">🏅</div>
                <div style="flex:1;min-width:0;"><p style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $p->nama_lomba }}</p><p style="font-size:11px;color:#94a3b8;">{{ $p->juara }} · {{ ucfirst($p->tingkat) }}</p></div>
            </a>
            @empty
            <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;"><i class="bi bi-trophy" style="font-size:28px;display:block;margin-bottom:6px;"></i>Belum ada prestasi</div>
            @endforelse
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
    options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:11},boxWidth:12}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:'#f1f5f9'}}}}
});

function clearAllCache() {
    const btn = document.getElementById('btnClearCache');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';

    fetch('{{ route("admin.clear-cache") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        Swal.fire({
            icon: data.success ? 'success' : 'error',
            title: data.success ? 'Berhasil!' : 'Gagal!',
            text: data.message,
            timer: 2500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    })
    .catch(() => {
        Swal.fire({ icon:'error', title:'Error', text:'Terjadi kesalahan koneksi.', toast:true, position:'top-end', timer:2500, showConfirmButton:false });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Clear Cache';
    });
}
</script>
@endsection
