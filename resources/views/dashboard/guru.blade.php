{{-- resources/views/dashboard/guru.blade.php --}}
@extends('layouts.app')
@section('page-title','Dashboard Guru')
@section('sidebar-menu') @include('partials.sidebar_guru') @endsection
@section('content')
@include('partials._dashboard_responsive')
<div class="page-title">
    <h1>👋 Selamat Datang, {{ $guru?->nama ?? auth()->user()->name }}!</h1>
    <p>{{ $sem?->nama ?? 'Belum ada semester aktif' }} &mdash; {{ now()->translatedFormat('l, d F Y') }}</p>
</div>

@if(!$guru)
<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#92400e;font-size:13px;">
    ⚠️ Data GTK belum terhubung ke akun ini. Hubungi admin untuk sinkronisasi.
</div>
@endif

<div class="dash-grid-4">
    @foreach([['Jurnal Hari Ini',$jurnalStats->hari_ini,'#6366f1','journal-text'],['Jurnal Bln Ini',$jurnalStats->bulan_ini,'#10b981','journal-bookmark-fill'],['Absensi Hari Ini',$absensiStats->hari_ini,'#f59e0b','clipboard-check-fill'],['Tiket Aktif',$tiketStats->open,'#dc2626','ticket-perforated-fill']] as [$l,$v,$c,$ic])
    <div class="dash-mini-card" style="background:{{ $c }}11;border:1.5px solid {{ $c }}33;">
        <i class="bi bi-{{ $ic }}" style="color:{{ $c }};"></i>
        <div><p class="dmc-label" style="color:{{ $c }};">{{ $l }}</p><p class="dmc-val" style="color:{{ $c }};">{{ $v }}</p></div>
    </div>
    @endforeach
</div>

<div class="dash-grid-2-1">
    <div class="card">
        <div class="card-header"><h3>📓 Jurnal Terbaru</h3><a href="{{ route('guru.jurnal.index') }}" style="font-size:12px;color:#6366f1;">Semua →</a></div>
        <div class="card-body" style="padding:0;">
            @forelse($jurnalTerbaru as $j)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid #f1f5f9;">
                <div style="width:38px;height:38px;border-radius:10px;background:#eef2ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-journal-text" style="color:#6366f1;font-size:17px;"></i></div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $j->mataPelajaran?->nama ?? '-' }} — {{ $j->nama_rombel }}</p>
                    <p style="font-size:11px;color:#94a3b8;">{{ $j->tanggal->translatedFormat('d M Y') }} · Pertemuan ke-{{ $j->pertemuan_ke }}</p>
                    <p style="font-size:12px;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $j->materi }}</p>
                </div>
            </div>
            @empty
            <div style="padding:28px;text-align:center;color:#94a3b8;"><i class="bi bi-journal-x" style="font-size:28px;display:block;margin-bottom:6px;"></i>Belum ada jurnal — <a href="{{ route('guru.jurnal.create') }}" style="color:#6366f1;">Buat sekarang</a></div>
            @endforelse
        </div>
        <div style="padding:12px 16px;border-top:1px solid #f1f5f9;">
            <a href="{{ route('guru.jurnal.create') }}" class="btn btn-primary" style="font-size:13px;"><i class="bi bi-plus-circle-fill"></i> Input Jurnal Hari Ini</a>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:12px;">
        <div class="card">
            <div class="card-header"><h3>📊 Ringkasan</h3></div>
            <div class="card-body">
                @foreach([['Jurnal Semester',$jurnalStats->semester,'#6366f1'],['Prestasi Input',$prestasiStats->total,'#16a34a'],['Prestasi Pending',$prestasiStats->pending,'#d97706'],['Tiket Selesai',$tiketStats->selesai,'#64748b']] as [$l,$v,$c])
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #f1f5f9;">
                    <span style="font-size:12px;color:#374151;">{{ $l }}</span>
                    <span style="font-size:17px;font-weight:800;color:{{ $c }};">{{ $v }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>📋 Absensi</h3><a href="{{ route('guru.absensi.index') }}" style="font-size:12px;color:#6366f1;">Lihat →</a></div>
            <div class="card-body">
                <div class="dash-grid-2" style="gap:8px;margin-bottom:10px;">
                    @foreach([['Hari Ini',$absensiStats->hari_ini,'#f59e0b'],['Bulan Ini',$absensiStats->bulan_ini,'#6366f1']] as [$l,$v,$c])
                    <div class="dash-box-center" style="background:{{ $c }}11;padding:10px 6px;"><p class="dbc-val" style="color:{{ $c }};">{{ $v }}</p><p class="dbc-label" style="color:{{ $c }};">{{ $l }}</p></div>
                    @endforeach
                </div>
                <a href="{{ route('guru.absensi.create') }}" class="btn" style="width:100%;background:#fef3c7;color:#d97706;text-align:center;font-size:12px;"><i class="bi bi-plus-circle"></i> Input Absensi Sekarang</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>🎫 Tiket Saya</h3></div>
            <div class="card-body">
                <div class="dash-grid-2" style="gap:8px;margin-bottom:10px;">
                    <div class="dash-box-center" style="background:#dcfce7;padding:10px 6px;"><p class="dbc-val" style="color:#16a34a;">{{ $tiketStats->open }}</p><p class="dbc-label" style="color:#16a34a;">Aktif</p></div>
                    <div class="dash-box-center" style="background:#f1f5f9;padding:10px 6px;"><p class="dbc-val" style="color:#64748b;">{{ $tiketStats->selesai }}</p><p class="dbc-label" style="color:#64748b;">Selesai</p></div>
                </div>
                <a href="{{ route('tiket.create') }}" class="btn" style="width:100%;background:#eef2ff;color:#6366f1;text-align:center;font-size:12px;"><i class="bi bi-plus-circle"></i> Buat Tiket Baru</a>
            </div>
        </div>
    </div>
</div>
@endsection
