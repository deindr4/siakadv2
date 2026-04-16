{{-- ============================================================ --}}
{{-- resources/views/dashboard/tata_usaha.blade.php              --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('page-title', 'Dashboard Tata Usaha')
@section('sidebar-menu') @include('partials.sidebar_tata_usaha') @endsection
@section('content')
<div class="page-title">
    <h1>👋 Selamat Datang, {{ auth()->user()->name }}!</h1>
    <p>{{ $sem?->nama ?? 'Belum ada semester aktif' }} &mdash; {{ now()->translatedFormat('l, d F Y') }}</p>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    @foreach([
        ['Siswa Aktif',$stats->total_siswa,'#6366f1','person-badge-fill'],
        ['Guru & GTK',$stats->total_guru,'#10b981','person-workspace'],
        ['Alumni',$stats->total_alumni,'#7c3aed','mortarboard-fill'],
        ['Mutasi',$stats->total_mutasi,'#f59e0b','arrow-left-right'],
    ] as [$l,$v,$c,$ic])
    <div style="background:{{ $c }}11;border:1.5px solid {{ $c }}33;border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
        <i class="bi bi-{{ $ic }}" style="font-size:22px;color:{{ $c }};flex-shrink:0;"></i>
        <div><p style="font-size:10px;color:{{ $c }};font-weight:700;text-transform:uppercase;margin-bottom:2px;">{{ $l }}</p><p style="font-size:22px;font-weight:800;color:{{ $c }};line-height:1;">{{ number_format($v) }}</p></div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
    <div class="card">
        <div class="card-header"><h3>🏆 Prestasi Semester Ini</h3><a href="{{ route('laporan.prestasi') }}" style="font-size:12px;color:#6366f1;">Laporan →</a></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div style="text-align:center;padding:18px;background:#dcfce7;border-radius:12px;">
                    <p style="font-size:34px;font-weight:800;color:#16a34a;line-height:1;">{{ $prestasiStats->total }}</p>
                    <p style="font-size:12px;color:#16a34a;font-weight:700;margin-top:6px;">Terverifikasi</p>
                </div>
                <div style="text-align:center;padding:18px;background:#fef3c7;border-radius:12px;">
                    <p style="font-size:34px;font-weight:800;color:#d97706;line-height:1;">{{ $prestasiStats->pending }}</p>
                    <p style="font-size:12px;color:#d97706;font-weight:700;margin-top:6px;">Menunggu Verif.</p>
                </div>
            </div>
            @if($prestasiStats->pending > 0)
            <a href="{{ route('prestasi.index') }}?status=pending" class="btn" style="width:100%;margin-top:12px;background:#fef3c7;color:#d97706;text-align:center;font-size:12px;">
                ⚠️ {{ $prestasiStats->pending }} prestasi perlu diverifikasi
            </a>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>🎫 Tiket</h3><a href="{{ route('tiket.index') }}" style="font-size:12px;color:#6366f1;">Lihat →</a></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div style="text-align:center;padding:18px;background:#dcfce7;border-radius:12px;">
                    <p style="font-size:34px;font-weight:800;color:#16a34a;line-height:1;">{{ $tiketStats->open }}</p>
                    <p style="font-size:12px;color:#16a34a;font-weight:700;margin-top:6px;">Aktif</p>
                </div>
                <div style="text-align:center;padding:18px;background:#f1f5f9;border-radius:12px;">
                    <p style="font-size:34px;font-weight:800;color:#64748b;line-height:1;">{{ $tiketStats->selesai }}</p>
                    <p style="font-size:12px;color:#64748b;font-weight:700;margin-top:6px;">Selesai</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>🏆 Prestasi Terbaru</h3><a href="{{ route('prestasi.index') }}" style="font-size:12px;color:#6366f1;">Semua →</a></div>
    <div class="card-body" style="padding:0;">
        @forelse($prestasiTerbaru as $p)
        <a href="{{ route('prestasi.show',$p) }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid #f1f5f9;text-decoration:none;color:inherit;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
            <div style="width:36px;height:36px;border-radius:8px;background:#fef3c7;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">🏅</div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $p->nama_lomba }}</p>
                <p style="font-size:11px;color:#94a3b8;">{{ $p->juara }} · {{ ucfirst($p->tingkat) }} · {{ $p->siswas->first()?->nama ?? '-' }}</p>
            </div>
            <span style="font-size:11px;color:#94a3b8;white-space:nowrap;">{{ $p->created_at->translatedFormat('d M Y') }}</span>
        </a>
        @empty
        <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada prestasi</div>
        @endforelse
    </div>
</div>
@endsection


{{-- ============================================================ --}}
{{-- resources/views/dashboard/siswa.blade.php                   --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('page-title', 'Dashboard Siswa')
@section('sidebar-menu') @include('partials.sidebar_siswa') @endsection
@section('content')
<div class="page-title">
    <h1>👋 Selamat Datang, {{ $siswa?->nama ?? auth()->user()->name }}!</h1>
    <p>{{ $sem?->nama ?? 'Belum ada semester aktif' }} &mdash; {{ now()->translatedFormat('l, d F Y') }}
    @if($siswa)<span style="margin-left:8px;padding:2px 10px;background:#eef2ff;color:#6366f1;border-radius:20px;font-size:12px;font-weight:700;">Kelas {{ $siswa->nama_rombel ?? '-' }}</span>@endif
    </p>
</div>

@if(!$siswa)
<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#92400e;font-size:13px;">
    ⚠️ Data siswa belum terhubung ke akun ini. Hubungi admin untuk sinkronisasi.
</div>
@endif

{{-- Absensi Rekap --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3>📋 Rekap Absensi Semester Ini</h3>
        <a href="{{ route('siswa.absensi') }}" style="font-size:12px;color:#6366f1;">Detail →</a>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;">
            @foreach([
                ['Hadir',$absensiSaya->hadir,'#16a34a','person-check-fill'],
                ['Sakit',$absensiSaya->sakit,'#0284c7','bandaid-fill'],
                ['Izin',$absensiSaya->izin,'#d97706','file-earmark-check-fill'],
                ['Alpa',$absensiSaya->alpha,'#dc2626','person-x-fill'],
                ['Dispensasi',$absensiSaya->dispensasi,'#7c3aed','patch-check-fill'],
            ] as [$l,$v,$c,$ic])
            <div style="text-align:center;padding:14px 10px;background:{{ $c }}11;border-radius:12px;border:1.5px solid {{ $c }}22;">
                <i class="bi bi-{{ $ic }}" style="font-size:20px;color:{{ $c }};display:block;margin-bottom:6px;"></i>
                <p style="font-size:24px;font-weight:800;color:{{ $c }};line-height:1;">{{ $v }}</p>
                <p style="font-size:11px;color:{{ $c }};font-weight:700;margin-top:4px;">{{ $l }}</p>
            </div>
            @endforeach
        </div>
        @php
            $totalAbsensi = $absensiSaya->hadir + $absensiSaya->sakit + $absensiSaya->izin + $absensiSaya->alpha + $absensiSaya->dispensasi;
            $pctHadir = $totalAbsensi > 0 ? round(($absensiSaya->hadir / $totalAbsensi) * 100) : 0;
        @endphp
        @if($totalAbsensi > 0)
        <div style="margin-top:14px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;">
                <span style="color:#374151;font-weight:600;">Tingkat Kehadiran</span>
                <span style="color:{{ $pctHadir >= 80 ? '#16a34a' : ($pctHadir >= 70 ? '#d97706' : '#dc2626') }};font-weight:800;">{{ $pctHadir }}%</span>
            </div>
            <div style="background:#f1f5f9;border-radius:20px;height:10px;overflow:hidden;">
                <div style="background:{{ $pctHadir >= 80 ? '#16a34a' : ($pctHadir >= 70 ? '#d97706' : '#dc2626') }};width:{{ $pctHadir }}%;height:100%;border-radius:20px;transition:.5s;"></div>
            </div>
        </div>
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
    {{-- Prestasi --}}
    <div class="card">
        <div class="card-header"><h3>🏆 Prestasi Saya</h3><a href="{{ route('prestasi.index') }}" style="font-size:12px;color:#6366f1;">Semua →</a></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
                <div style="text-align:center;padding:12px;background:#fef3c7;border-radius:10px;"><p style="font-size:22px;font-weight:800;color:#d97706;">{{ $prestasiStats->semester }}</p><p style="font-size:10px;color:#d97706;font-weight:700;">Semester Ini</p></div>
                <div style="text-align:center;padding:12px;background:#dcfce7;border-radius:10px;"><p style="font-size:22px;font-weight:800;color:#16a34a;">{{ $prestasiStats->total }}</p><p style="font-size:10px;color:#16a34a;font-weight:700;">Total</p></div>
            </div>
            @forelse($prestasiSaya as $p)
            <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:16px;">🏅</span>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:12px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $p->prestasi?->nama_lomba }}</p>
                    <p style="font-size:11px;color:#94a3b8;">{{ $p->prestasi?->juara }} · {{ ucfirst($p->prestasi?->tingkat ?? '') }}</p>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:#94a3b8;font-size:13px;padding:16px 0;">
                Belum ada prestasi — <a href="{{ route('prestasi.create') }}" style="color:#6366f1;">Input prestasi</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Tiket & Pelanggaran --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="card">
            <div class="card-header"><h3>🎫 Tiket Saya</h3><a href="{{ route('tiket.index') }}" style="font-size:12px;color:#6366f1;">Semua →</a></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                    <div style="text-align:center;padding:10px;background:#dcfce7;border-radius:8px;"><p style="font-size:20px;font-weight:800;color:#16a34a;">{{ $tiketStats->open }}</p><p style="font-size:10px;color:#16a34a;font-weight:700;">Aktif</p></div>
                    <div style="text-align:center;padding:10px;background:#f1f5f9;border-radius:8px;"><p style="font-size:20px;font-weight:800;color:#64748b;">{{ $tiketStats->selesai }}</p><p style="font-size:10px;color:#64748b;font-weight:700;">Selesai</p></div>
                </div>
                @forelse($tiketTerbaru as $t)
                <a href="{{ route('tiket.show',$t) }}" style="display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid #f1f5f9;text-decoration:none;color:inherit;">
                    <div style="width:7px;height:7px;border-radius:50%;flex-shrink:0;background:{{ $t->statusColor() }};"></div>
                    <p style="font-size:12px;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $t->judul }}</p>
                    <span style="font-size:10px;color:#94a3b8;white-space:nowrap;">{{ $t->created_at->diffForHumans(null,true) }}</span>
                </a>
                @empty
                <p style="font-size:12px;color:#94a3b8;text-align:center;padding:8px 0;">Belum ada tiket</p>
                @endforelse
                <a href="{{ route('tiket.create') }}" class="btn" style="width:100%;margin-top:10px;background:#eef2ff;color:#6366f1;text-align:center;font-size:12px;"><i class="bi bi-plus-circle"></i> Buat Tiket Baru</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>⚠️ Pelanggaran Saya</h3></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="text-align:center;padding:12px;background:#fee2e2;border-radius:10px;"><p style="font-size:22px;font-weight:800;color:#dc2626;">{{ $pelanggaranStats->semester }}</p><p style="font-size:10px;color:#dc2626;font-weight:700;">Semester Ini</p></div>
                    <div style="text-align:center;padding:12px;background:#fef3c7;border-radius:10px;"><p style="font-size:22px;font-weight:800;color:#d97706;">{{ $pelanggaranStats->poin }}</p><p style="font-size:10px;color:#d97706;font-weight:700;">Total Poin</p></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
