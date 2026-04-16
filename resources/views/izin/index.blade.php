@extends('layouts.app')
@section('title', 'Izin Berencana')

@section('sidebar-menu')
    @if(auth()->user()->hasRole('siswa'))
        @include('partials.sidebar_siswa')
    @elseif(auth()->user()->hasRole('kepala_sekolah'))
        @include('partials.sidebar_kepala_sekolah')
    @elseif(auth()->user()->hasRole('bk'))
        @include('partials.sidebar_bk')
    @else
        @include('partials.sidebar_admin')
    @endif
@endsection

@section('content')
@include('partials._dashboard_responsive')

<style>
    /* 1. Custom Responsive Grid untuk Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    /* 2. Responsive Table to Card Stack (HP Mode) */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .table-responsive-stack thead {
            display: none;
        }

        .table-responsive-stack tr {
            display: block;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .table-responsive-stack td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0 !important;
            border: none !important;
            font-size: 13px;
        }

        /* Label data untuk HP */
        .table-responsive-stack td::before {
            content: attr(data-label);
            font-weight: 700;
            color: #94a3b8;
            font-size: 11px;
            text-transform: uppercase;
        }

        .table-responsive-stack td div {
            text-align: right;
        }

        .page-title h1 { font-size: 1.5rem; }
    }

    .form-filter {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-item {
        flex: 1;
        min-width: 120px;
    }
</style>

<div class="page-title">
    <h1>📋 Izin Berencana</h1>
    <p>Pengajuan dan persetujuan izin tidak masuk sekolah</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
</div>
@endif

{{-- Stats --}}
<div class="stats-grid">
    <div style="background:#fef3c7; padding:16px; border-radius:12px; text-align:center;">
        <i class="bi bi-hourglass-split" style="font-size:20px; color:#d97706;"></i>
        <p style="margin:5px 0 0; font-size:24px; font-weight:800; color:#d97706;">{{ $stats->pending }}</p>
        <p style="margin:0; font-size:11px; color:#d97706; font-weight:600;">MENUNGGU</p>
    </div>
    <div style="background:#dcfce7; padding:16px; border-radius:12px; text-align:center;">
        <i class="bi bi-check-circle-fill" style="font-size:20px; color:#16a34a;"></i>
        <p style="margin:5px 0 0; font-size:24px; font-weight:800; color:#16a34a;">{{ $stats->disetujui }}</p>
        <p style="margin:0; font-size:11px; color:#16a34a; font-weight:600;">DISETUJUI</p>
    </div>
    <div style="background:#fee2e2; padding:16px; border-radius:12px; text-align:center;">
        <i class="bi bi-x-circle-fill" style="font-size:20px; color:#dc2626;"></i>
        <p style="margin:5px 0 0; font-size:24px; font-weight:800; color:#dc2626;">{{ $stats->ditolak }}</p>
        <p style="margin:0; font-size:11px; color:#dc2626; font-weight:600;">DITOLAK</p>
    </div>
    <div style="background:#eef2ff; padding:16px; border-radius:12px; text-align:center;">
        <i class="bi bi-list-ul" style="font-size:20px; color:#6366f1;"></i>
        <p style="margin:5px 0 0; font-size:24px; font-weight:800; color:#6366f1;">{{ $stats->total }}</p>
        <p style="margin:0; font-size:11px; color:#6366f1; font-weight:600;">TOTAL</p>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:16px; border:none; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
    <div class="card-body" style="padding:15px;">
        <form method="GET" class="form-filter">
            @if(!auth()->user()->hasRole('siswa'))
            <div class="filter-item" style="flex: 2; min-width:160px;">
                <label style="font-size:11px; font-weight:700; color:#64748b;">CARI SISWA</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama..."
                    style="width:100%; padding:9px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none;">
            </div>
            <div class="filter-item">
                <label style="font-size:11px; font-weight:700; color:#64748b;">KELAS</label>
                <select name="rombel" style="width:100%; padding:9px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; background:#fff;" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($rombels as $r)
                    <option value="{{ $r->rombongan_belajar_id }}" {{ request('rombel') == $r->rombongan_belajar_id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="filter-item">
                <label style="font-size:11px; font-weight:700; color:#64748b;">STATUS</label>
                <select name="status" style="width:100%; padding:9px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; background:#fff;" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="filter-item">
                <label style="font-size:11px; font-weight:700; color:#64748b;">SEMESTER</label>
                <select name="semester_id" style="width:100%; padding:9px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; background:#fff;" onchange="this.form.submit()">
                    @foreach($semesters as $s)
                    <option value="{{ $s->id }}" {{ $semesterId == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:5px;">
                <button type="submit" class="btn btn-primary" style="padding:9px 15px;"><i class="bi bi-search"></i></button>
                <a href="{{ route('izin.index') }}" class="btn" style="background:#f1f5f9; padding:9px 15px;"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card" style="border:none; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
    <div class="card-header" style="background:#fff; display:flex; justify-content:space-between; align-items:center; padding: 15px;">
        <h3 style="font-size:15px; font-weight:700; margin:0;"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Daftar Pengajuan</h3>
        <div style="display:flex; gap:8px;">
            @if(auth()->user()->hasRole('siswa'))
                <a href="{{ route('izin.create') }}" class="btn btn-primary btn-sm">Ajukan Izin</a>
            @elseif(auth()->user()->hasAnyRole(['admin','kepala_sekolah']))
                <a href="{{ route('izin.laporan') }}" class="btn btn-sm" style="background:#eef2ff;color:#6366f1;">Laporan</a>
            @endif
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        @if($izinList->isEmpty())
            <div style="text-align:center; padding:50px; color:#94a3b8;">
                <i class="bi bi-folder2-open" style="font-size:40px;"></i>
                <p style="margin-top:10px;">Belum ada pengajuan izin.</p>
            </div>
        @else
        <div style="overflow-x: hidden; padding: 15px;">
            <table class="table-responsive-stack" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9;">
                        <th style="padding:12px; text-align:left; font-size:12px; color:#64748b;">NO. IZIN</th>
                        @if(!auth()->user()->hasRole('siswa'))<th style="padding:12px; text-align:left; font-size:12px; color:#64748b;">SISWA</th>@endif
                        <th style="padding:12px; text-align:left; font-size:12px; color:#64748b;">JENIS</th>
                        <th style="padding:12px; text-align:left; font-size:12px; color:#64748b;">TANGGAL</th>
                        <th style="padding:12px; text-align:center; font-size:12px; color:#64748b;">HARI</th>
                        <th style="padding:12px; text-align:center; font-size:12px; color:#64748b;">STATUS</th>
                        <th style="padding:12px; text-align:center; font-size:12px; color:#64748b;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($izinList as $izin)
                    <tr style="border-bottom: 1px solid #f1f5f9; {{ $izin->status === 'pending' ? 'background:#fffbeb;' : '' }}">
                        <td data-label="No. Izin">
                            <div>
                                <div style="font-weight:700; color:#6366f1; font-family:monospace;">{{ $izin->nomor_izin }}</div>
                                <div style="font-size:10px; color:#94a3b8;">{{ $izin->created_at->diffForHumans() }}</div>
                            </div>
                        </td>
                        @if(!auth()->user()->hasRole('siswa'))
                        <td data-label="Siswa">
                            <div>
                                <div style="font-weight:600;">{{ $izin->siswa?->nama }}</div>
                                <div style="font-size:11px; color:#94a3b8;">{{ $izin->siswa?->nama_rombel ?? '-' }}</div>
                            </div>
                        </td>
                        @endif
                        <td data-label="Jenis">
                            <span style="font-size:12px;">{{ App\Models\IzinBerencana::jenisList()[$izin->jenis] ?? $izin->jenis }}</span>
                        </td>
                        <td data-label="Tanggal">
                            <div style="font-size:12px;">
                                {{ $izin->tanggal_mulai->translatedFormat('d M Y') }}
                                @if($izin->tanggal_mulai != $izin->tanggal_selesai)
                                    <br><span style="color:#94a3b8;">s/d {{ $izin->tanggal_selesai->translatedFormat('d M Y') }}</span>
                                @endif
                            </div>
                        </td>
                        <td data-label="Hari" style="text-align:center;">
                            @php $hari = $izin->hariEfektif(); @endphp
                            <span style="background:{{ $hari > 2 ? '#fef3c7' : '#f0fdf4' }}; color:{{ $hari > 2 ? '#d97706' : '#16a34a' }}; font-weight:700; padding:2px 10px; border-radius:20px; font-size:12px;">
                                {{ $hari }}
                            </span>
                        </td>
                        <td data-label="Status" style="text-align:center;">
                            <div>
                                <span style="background:{{ $izin->statusBg() }}; color:{{ $izin->statusColor() }}; font-weight:700; padding:3px 10px; border-radius:20px; font-size:11px;">
                                    {{ $izin->statusLabel() }}
                                </span>
                                @if($izin->melebihiBatasMandiri() && $izin->status === 'pending')
                                    <div style="font-size:10px; color:#d97706; margin-top:2px;">⚠️ >2 hari</div>
                                @endif
                            </div>
                        </td>
                        <td data-label="Aksi">
                            <div style="display:flex; gap:5px; justify-content: flex-end;">
                                <a href="{{ route('izin.show', $izin) }}" class="btn btn-sm" style="background:#eef2ff; color:#6366f1;">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                @if($izin->status === 'pending' && auth()->user()->hasRole('siswa'))
                                <form action="{{ route('izin.batalkan', $izin) }}" method="POST" onsubmit="return confirm('Batalkan izin ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm" style="background:#fee2e2; color:#dc2626;">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Pagination --}}
        @if($izinList->hasPages())
        <div style="padding:15px; border-top:1px solid #f1f5f9;">
            {{ $izinList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
