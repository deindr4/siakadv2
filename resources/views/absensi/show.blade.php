@extends('layouts.app')

@section('page-title', 'Detail Absensi')
@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>📋 Detail Absensi</h1>
    <p>{{ $absensi->tanggal?->translatedFormat('l, d F Y') }} — {{ $absensi->nama_rombel }}</p>
</div>

{{-- Info Header --}}
<div class="card" style="margin-bottom:20px;border-left:4px solid #6366f1;">
    <div class="card-body">
        <div style="display:flex;gap:0;flex-wrap:wrap;">
            <div style="flex:1;min-width:180px;padding:8px 16px 8px 0;border-right:1px solid #f1f5f9;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;">KELAS</p>
                <p style="font-size:16px;font-weight:800;color:#6366f1;">{{ $absensi->nama_rombel }}</p>
            </div>
            <div style="flex:1;min-width:180px;padding:8px 16px;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;">DIABSEN OLEH</p>
                <p style="font-size:14px;font-weight:700;">{{ $absensi->nama_guru ?? '-' }}</p>
                <p style="font-size:11px;color:#94a3b8;">{{ $absensi->diabsen_pada?->format('H:i') }} | {{ $absensi->ip_address }}</p>
            </div>
            <div style="flex:1;min-width:180px;padding:8px 16px;border-left:1px solid #f1f5f9;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;">STATUS</p>
                @if($absensi->is_locked)
                    <span style="font-size:12px;padding:4px 12px;border-radius:20px;background:#fee2e2;color:#dc2626;font-weight:700;">🔒 Terkunci</span>
                @else
                    <span style="font-size:12px;padding:4px 12px;border-radius:20px;background:#dcfce7;color:#16a34a;font-weight:700;">🔓 Terbuka</span>
                @endif
            </div>
            <div style="flex:1;min-width:180px;padding:8px 0 8px 16px;border-left:1px solid #f1f5f9;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;">SEMESTER</p>
                <p style="font-size:14px;font-weight:700;">{{ $absensi->semester?->nama }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Stat --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;">
    @foreach(['H' => ['Hadir','#16a34a','#dcfce7'], 'S' => ['Sakit','#0284c7','#e0f2fe'], 'I' => ['Izin','#d97706','#fef3c7'], 'A' => ['Alpa','#dc2626','#fee2e2'], 'D' => ['Dispensasi','#7c3aed','#ede9fe']] as $kode => $info)
    @php $count = $absensi->absensiSiswa->where('status', $kode)->count(); @endphp
    <div style="background:{{ $info[2] }};border-radius:12px;padding:16px;text-align:center;">
        <p style="font-size:11px;color:{{ $info[1] }};font-weight:700;">{{ $info[0] }}</p>
        <p style="font-size:28px;font-weight:800;color:{{ $info[1] }};">{{ $count }}</p>
    </div>
    @endforeach
</div>

{{-- Tabel Siswa --}}
<div class="card">
    <div class="card-header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <h3>👥 Daftar Kehadiran Siswa
                <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $absensi->absensiSiswa->count() }} siswa</span>
            </h3>
            <div style="display:flex;gap:8px;">
                @if($isAdmin)
                <a href="{{ route('admin.absensi.edit', $absensi->id) }}"
                    class="btn" style="background:#fef3c7;color:#d97706;">
                    <i class="bi bi-pencil-fill"></i> Edit
                </a>
                <form method="POST" action="{{ route('admin.absensi.lock', $absensi->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn"
                        style="background:{{ $absensi->is_locked ? '#dcfce7' : '#fee2e2' }};color:{{ $absensi->is_locked ? '#16a34a' : '#dc2626' }};">
                        <i class="bi bi-{{ $absensi->is_locked ? 'unlock-fill' : 'lock-fill' }}"></i>
                        {{ $absensi->is_locked ? 'Buka Kunci' : 'Kunci' }}
                    </button>
                </form>
                @endif
                <button onclick="window.print()" class="btn" style="background:#f1f5f9;color:#374151;">
                    <i class="bi bi-printer-fill"></i> Print
                </button>
                <a href="{{ route(($isAdmin ? 'admin' : 'guru').'.absensi.index') }}"
                    class="btn" style="background:#f1f5f9;color:#374151;">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th style="text-align:center;">Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusMap = [
                            'H' => ['Hadir',      '#16a34a', '#dcfce7'],
                            'S' => ['Sakit',      '#0284c7', '#e0f2fe'],
                            'I' => ['Izin',       '#d97706', '#fef3c7'],
                            'A' => ['Alpa',       '#dc2626', '#fee2e2'],
                            'D' => ['Dispensasi', '#7c3aed', '#ede9fe'],
                        ];
                    @endphp
                    @forelse($absensi->absensiSiswa->sortBy('siswa.nama') as $i => $as)
                    @php $sm = $statusMap[$as->status] ?? ['-', '#94a3b8', '#f1f5f9']; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="font-size:13px;font-weight:600;">{{ $as->siswa?->nama }}</td>
                        <td style="font-size:12px;color:#64748b;">{{ $as->siswa?->nisn ?? '-' }}</td>
                        <td style="text-align:center;">
                            <span style="font-size:12px;padding:4px 14px;border-radius:20px;font-weight:700;background:{{ $sm[2] }};color:{{ $sm[1] }};">
                                {{ $as->status }} — {{ $sm[0] }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#64748b;">{{ $as->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;color:#94a3b8;">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($absensi->catatan)
<div class="card" style="margin-top:16px;border-left:4px solid #fbbf24;">
    <div class="card-body">
        <p style="font-size:12px;font-weight:700;color:#d97706;margin-bottom:4px;">📝 CATATAN</p>
        <p style="font-size:13px;">{{ $absensi->catatan }}</p>
    </div>
</div>
@endif

<style>
@media print {
    .sidebar, .page-title, .btn, form { display: none !important; }
    .card-header div div { display: none !important; }
}
</style>
@endsection
