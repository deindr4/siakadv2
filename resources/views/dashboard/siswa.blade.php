{{-- resources/views/dashboard/siswa.blade.php --}}
@extends('layouts.app')
@section('page-title','Dashboard Siswa')
@section('sidebar-menu') @include('partials.sidebar_siswa') @endsection
@section('content')
@include('partials._dashboard_responsive')

<div class="page-title">
    <h1>👋 Selamat Datang, {{ $siswa?->nama ?? auth()->user()->name }}!</h1>
    <p>
        {{ $sem?->nama ?? 'Belum ada semester aktif' }} &mdash; {{ now()->translatedFormat('l, d F Y') }}
        @if($siswa)
        <span style="margin-left:8px;padding:2px 10px;background:#eef2ff;color:#6366f1;border-radius:20px;font-size:12px;font-weight:700;">Kelas {{ $siswa->nama_rombel ?? '-' }}</span>
        @endif
    </p>
</div>

@if(!$siswa)
<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#92400e;font-size:13px;">
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
        <div class="dash-grid-5">
            @foreach([
                ['Hadir',      $absensiSaya->hadir,      '#16a34a', 'person-check-fill'],
                ['Sakit',      $absensiSaya->sakit,      '#0284c7', 'bandaid-fill'],
                ['Izin',       $absensiSaya->izin,       '#d97706', 'file-earmark-check-fill'],
                ['Alpa',       $absensiSaya->alpha,      '#dc2626', 'person-x-fill'],
                ['Dispensasi', $absensiSaya->dispensasi, '#7c3aed', 'patch-check-fill'],
            ] as [$l,$v,$c,$ic])
            <div class="dash-box-center" style="background:{{ $c }}11;border:1.5px solid {{ $c }}22;padding:14px 8px;">
                <i class="bi bi-{{ $ic }}" style="font-size:20px;color:{{ $c }};display:block;margin-bottom:6px;"></i>
                <p class="dbc-val" style="color:{{ $c }};font-size:24px;">{{ $v }}</p>
                <p class="dbc-label" style="color:{{ $c }};">{{ $l }}</p>
            </div>
            @endforeach
        </div>
        @php
            $total = $absensiSaya->hadir + $absensiSaya->sakit + $absensiSaya->izin + $absensiSaya->alpha + $absensiSaya->dispensasi;
            $pct   = $total > 0 ? round(($absensiSaya->hadir / $total) * 100) : 0;
            $pctColor = $pct >= 80 ? '#16a34a' : ($pct >= 70 ? '#d97706' : '#dc2626');
        @endphp
        @if($total > 0)
        <div style="margin-top:14px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px;">
                <span style="color:#374151;font-weight:600;">Tingkat Kehadiran</span>
                <span style="color:{{ $pctColor }};font-weight:800;">{{ $pct }}%</span>
            </div>
            <div class="dash-progress-wrap"><div class="dash-progress-bar" style="background:{{ $pctColor }};width:{{ $pct }}%;"></div></div>
            @if($pct < 75)
            <p style="font-size:11px;color:#dc2626;margin-top:4px;">⚠️ Kehadiran di bawah 75% — perlu ditingkatkan</p>
            @endif
        </div>
        @endif
    </div>
</div>

<div class="dash-grid-2">

    {{-- Prestasi --}}
    <div class="card">
        <div class="card-header">
            <h3>🏆 Prestasi Saya</h3>
            <a href="{{ route('prestasi.index') }}" style="font-size:12px;color:#6366f1;">Semua →</a>
        </div>
        <div class="card-body">
            <div class="dash-grid-2" style="gap:10px;margin-bottom:12px;">
                <div class="dash-box-center" style="background:#fef3c7;padding:12px;">
                    <p class="dbc-val" style="color:#d97706;">{{ $prestasiStats->semester }}</p>
                    <p class="dbc-label" style="color:#d97706;">Semester Ini</p>
                </div>
                <div class="dash-box-center" style="background:#dcfce7;padding:12px;">
                    <p class="dbc-val" style="color:#16a34a;">{{ $prestasiStats->total }}</p>
                    <p class="dbc-label" style="color:#16a34a;">Total</p>
                </div>
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
            <p style="text-align:center;color:#94a3b8;font-size:13px;padding:12px 0;">Belum ada prestasi — <a href="{{ route('prestasi.create') }}" style="color:#6366f1;">Input prestasi</a></p>
            @endforelse
            <a href="{{ route('prestasi.create') }}" class="btn" style="width:100%;margin-top:12px;background:#fef3c7;color:#d97706;text-align:center;font-size:12px;">
                <i class="bi bi-plus-circle"></i> Input Prestasi Baru
            </a>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Tiket --}}
        <div class="card">
            <div class="card-header">
                <h3>🎫 Tiket Saya</h3>
                <a href="{{ route('tiket.index') }}" style="font-size:12px;color:#6366f1;">Semua →</a>
            </div>
            <div class="card-body">
                <div class="dash-grid-2" style="gap:10px;margin-bottom:10px;">
                    <div class="dash-box-center" style="background:#dcfce7;padding:10px;">
                        <p class="dbc-val" style="color:#16a34a;">{{ $tiketStats->open }}</p>
                        <p class="dbc-label" style="color:#16a34a;">Aktif</p>
                    </div>
                    <div class="dash-box-center" style="background:#f1f5f9;padding:10px;">
                        <p class="dbc-val" style="color:#64748b;">{{ $tiketStats->selesai }}</p>
                        <p class="dbc-label" style="color:#64748b;">Selesai</p>
                    </div>
                </div>
                @forelse($tiketTerbaru as $t)
                <a href="{{ route('tiket.show',$t) }}" class="dash-feed-row" style="padding:7px 0;">
                    <div style="width:7px;height:7px;border-radius:50%;flex-shrink:0;background:{{ $t->statusColor() }};"></div>
                    <p style="font-size:12px;font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $t->judul }}</p>
                    <span style="font-size:10px;color:#94a3b8;white-space:nowrap;">{{ $t->created_at->diffForHumans(null,true) }}</span>
                </a>
                @empty
                <p style="font-size:12px;color:#94a3b8;text-align:center;padding:8px 0;">Belum ada tiket</p>
                @endforelse
                <a href="{{ route('tiket.create') }}" class="btn" style="width:100%;margin-top:10px;background:#eef2ff;color:#6366f1;text-align:center;font-size:12px;">
                    <i class="bi bi-plus-circle"></i> Buat Tiket Baru
                </a>
            </div>
        </div>

        {{-- Pelanggaran & Poin — UPDATED dengan net poin --}}
        @php
            $netPoin     = $pelanggaranStats->net_poin ?? $pelanggaranStats->poin;
            $poinPos     = $pelanggaranStats->poin_positif ?? 0;
            $poinPel     = $pelanggaranStats->poin ?? 0;
            $netColor    = $netPoin >= 75 ? '#dc2626' : ($netPoin >= 50 ? '#d97706' : ($netPoin >= 25 ? '#0284c7' : '#16a34a'));
            $netBg       = $netPoin >= 75 ? '#fee2e2' : ($netPoin >= 50 ? '#fef3c7' : ($netPoin >= 25 ? '#e0f2fe' : '#f0fdf4'));
        @endphp
        <div class="card">
            <div class="card-header">
                <h3>⚠️ Pelanggaran Saya</h3>
                <a href="{{ route('siswa.pelanggaran.index') }}" style="font-size:12px;color:#6366f1;">Detail →</a>
            </div>
            <div class="card-body">
                {{-- Net Poin Hero --}}
                <div style="background:{{ $netBg }};border-radius:10px;padding:12px 14px;margin-bottom:10px;display:flex;align-items:center;gap:14px;">
                    <div style="text-align:center;flex-shrink:0;">
                        <div style="font-size:32px;font-weight:900;color:{{ $netColor }};line-height:1;">{{ $netPoin }}</div>
                        <div style="font-size:10px;font-weight:700;color:{{ $netColor }};opacity:.8;">NET POIN</div>
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;gap:8px;flex-wrap:wrap;font-size:11px;margin-bottom:6px;">
                            <span style="color:#dc2626;font-weight:600;">{{ $poinPel }} pelanggaran</span>
                            <span style="color:#94a3b8;">−</span>
                            <span style="color:#16a34a;font-weight:600;">{{ $poinPos }} kebaikan</span>
                        </div>
                        <div style="background:rgba(0,0,0,.08);border-radius:99px;height:6px;">
                            <div style="background:{{ $netColor }};width:{{ min($netPoin,100) }}%;height:6px;border-radius:99px;"></div>
                        </div>
                        <div style="font-size:10px;color:{{ $netColor }};margin-top:4px;font-weight:600;">
                            @if($netPoin >= 75) ⚠️ Sangat tinggi! Segera konsultasi BK.
                            @elseif($netPoin >= 50) ⚠️ Cukup tinggi. Harap berhati-hati.
                            @elseif($netPoin >= 25) ℹ️ Dalam batas wajar.
                            @else ✅ Masih rendah. Pertahankan!
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Stats kasus --}}
                <div class="dash-grid-2" style="gap:8px;">
                    <div class="dash-box-center" style="background:#fee2e2;padding:10px;">
                        <p class="dbc-val" style="color:#dc2626;font-size:20px;">{{ $pelanggaranStats->semester }}</p>
                        <p class="dbc-label" style="color:#dc2626;">Kasus</p>
                    </div>
                    <div class="dash-box-center" style="background:#dcfce7;padding:10px;">
                        <p class="dbc-val" style="color:#16a34a;font-size:20px;">{{ $poinPos }}</p>
                        <p class="dbc-label" style="color:#16a34a;">Poin Kebaikan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Izin Berencana --}}
        <div class="card">
            <div class="card-header">
                <h3>📋 Izin Berencana</h3>
                <a href="{{ route('izin.index') }}" style="font-size:12px;color:#6366f1;">Semua →</a>
            </div>
            <div class="card-body" style="padding:12px 16px;">
                <div class="dash-grid-2" style="gap:8px;margin-bottom:10px;">
                    <div class="dash-box-center" style="background:#fef3c7;padding:10px;">
                        @php
                            $izinPending = \App\Models\IzinBerencana::whereHas('siswa', fn($q) => $q->where('user_id', auth()->id()))
                                ->where('status','pending')->count();
                        @endphp
                        <p class="dbc-val" style="color:#d97706;font-size:20px;">{{ $izinPending }}</p>
                        <p class="dbc-label" style="color:#d97706;">Menunggu</p>
                    </div>
                    <div class="dash-box-center" style="background:#dcfce7;padding:10px;">
                        @php
                            $izinDisetujui = \App\Models\IzinBerencana::whereHas('siswa', fn($q) => $q->where('user_id', auth()->id()))
                                ->where('status','disetujui')->count();
                        @endphp
                        <p class="dbc-val" style="color:#16a34a;font-size:20px;">{{ $izinDisetujui }}</p>
                        <p class="dbc-label" style="color:#16a34a;">Disetujui</p>
                    </div>
                </div>
                <a href="{{ route('izin.create') }}" class="btn" style="width:100%;background:#eef2ff;color:#6366f1;text-align:center;font-size:12px;">
                    <i class="bi bi-plus-circle"></i> Ajukan Izin Baru
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
