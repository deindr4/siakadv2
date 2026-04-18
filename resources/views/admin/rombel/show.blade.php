@extends('layouts.app')

@section('page-title', 'Detail Rombel')
@section('page-subtitle', $rombel->nama_rombel)

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<style>
    .dr-page { width: 100%; padding: 1.5rem 2.5rem; }
    @media (max-width: 768px) { .dr-page { padding: 1rem; } }

    /* Header */
    .dr-header { display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .dr-header-left h1 { font-size: 22px; font-weight: 700; color: #111827; }
    .dr-header-left p  { font-size: 13px; color: #6b7280; margin-top: 3px; }
    @media (max-width: 480px) { .dr-header { flex-direction: column; align-items: flex-start; } }

    /* Top grid: info + stats */
    .dr-top-grid { display: grid; grid-template-columns: 1fr 280px; gap: 14px; margin-bottom: 1.5rem; align-items: start; }
    @media (max-width: 768px) { .dr-top-grid { grid-template-columns: 1fr; } }

    /* Card */
    .dr-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
    .dr-card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid #f3f4f6; }
    .dr-card-title { font-size: 14px; font-weight: 700; color: #111827; }
    .dr-card-body  { padding: 18px; }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 10px; }
    @media (max-width: 480px) { .info-grid { grid-template-columns: 1fr; } }
    .info-item { background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 10px; padding: 12px 14px; }
    .info-label { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
    .info-value { font-size: 13px; font-weight: 600; color: #111827; }

    /* Stat mini cards */
    .dr-mini-stats { display: flex; flex-direction: column; gap: 10px; }
    @media (max-width: 768px) { .dr-mini-stats { flex-direction: row; } }
    @media (max-width: 480px) { .dr-mini-stats { flex-direction: column; } }
    .dr-mini-stat { border-radius: 14px; padding: 16px 18px; display: flex; align-items: center; gap: 12px; position: relative; overflow: hidden; flex: 1; }
    .dr-mini-stat.c-blue { background: linear-gradient(135deg, #378ADD 0%, #0C447C 100%); }
    .dr-mini-stat.c-pink { background: linear-gradient(135deg, #D4537E 0%, #72243E 100%); }
    .dr-mini-stat::after { content:''; position:absolute; right:-14px; top:-14px; width:70px; height:70px; border-radius:50%; background:rgba(255,255,255,.08); }
    .dr-mini-ico { width: 40px; height: 40px; border-radius: 10px; background: rgba(255,255,255,.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .dr-mini-num { font-size: 24px; font-weight: 800; color: #fff; line-height: 1; }
    .dr-mini-lbl { font-size: 12px; color: rgba(255,255,255,.8); margin-top: 3px; }

    /* Form fields */
    .form-field { display: flex; flex-direction: column; }
    .form-field label { font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 5px; letter-spacing: .05em; text-transform: uppercase; }
    .form-field input,
    .form-field select { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; font-family: inherit; background: #fff; color: #111827;
        outline: none; transition: border-color .15s; box-sizing: border-box; }
    .form-field input:focus,
    .form-field select:focus { border-color: #6366f1; }

    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; align-items: end; }
    .filter-actions { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; }

    /* Buttons */
    .btn-pv { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
        border-radius: 8px; border: none; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all .15s; font-family: inherit; text-decoration: none; }
    .btn-primary-pv { background: #3C3489; color: #fff; }
    .btn-primary-pv:hover { background: #26215C; color: #fff; }
    .btn-ghost { background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; }
    .btn-ghost:hover { background: #e2e8f0; }
    .btn-sm-ico { width: 32px; height: 32px; display: inline-flex; align-items: center;
        justify-content: center; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; cursor: pointer; font-size: 13px; transition: all .15s; color: #6b7280; text-decoration: none; }
    .btn-sm-ico.view:hover { background: #ede9fe; border-color: #a5b4fc; color: #4f46e5; }

    /* Avatar */
    .avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; color: #fff; font-weight: 700; font-size: 13px; flex-shrink: 0; }
    .avatar.laki   { background: linear-gradient(135deg, #378ADD, #0C447C); }
    .avatar.wanita { background: linear-gradient(135deg, #D4537E, #72243E); }

    /* Table */
    .dr-table-wrap { overflow-x: auto; }
    .dr-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 580px; }
    .dr-table th { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase;
        letter-spacing: .05em; padding: 10px 16px; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6; text-align: left; white-space: nowrap; }
    .dr-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .dr-table tr:last-child td { border-bottom: none; }
    .dr-table tr:hover td { background: #f8faff; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 99px; white-space: nowrap; }
    .badge-laki   { background: #E6F1FB; color: #0C447C; }
    .badge-wanita { background: #FBEAF0; color: #72243E; }

    /* Mobile card list */
    .dr-card-list { display: none; }
    @media (max-width: 640px) {
        .dr-table-wrap { display: none; }
        .dr-card-list  { display: block; }
    }
    .dr-list-item { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; display: flex; gap: 12px; align-items: flex-start; }
    .dr-list-item:last-child { border-bottom: none; }
    .dr-list-body { flex: 1; min-width: 0; }
    .dr-list-name { font-weight: 700; font-size: 13px; color: #111827; }
    .dr-list-sub  { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .dr-list-row  { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; flex-wrap: wrap; gap: 6px; }

    /* Pagination */
    .dr-pagination { padding: 14px 18px; border-top: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .dr-pagination .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; flex-wrap: wrap; }
    .dr-pagination .page-item .page-link { display: inline-flex; align-items: center; justify-content: center;
        min-width: 34px; height: 34px; padding: 0 8px; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; color: #374151; font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all .15s; }
    .dr-pagination .page-item .page-link:hover { background: #f1f5f9; }
    .dr-pagination .page-item.active .page-link { background: #3C3489; border-color: #3C3489; color: #fff; }
    .dr-pagination .page-item.disabled .page-link { color: #d1d5db; pointer-events: none; }
    .dr-pag-info { font-size: 12px; color: #6b7280; }

    .empty-state { text-align: center; padding: 3.5rem 1rem; }
    .empty-ico { width: 56px; height: 56px; background: #f3f4f6; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
</style>

<div class="dr-page">

    {{-- Header --}}
    <div class="dr-header">
        <div class="dr-header-left">
            <h1>
                <i class="bi bi-diagram-3-fill me-2" style="color:#EF9F27;font-size:20px;"></i>
                {{ $rombel->nama_rombel }}
            </h1>
            <p>Daftar siswa dalam rombel ini</p>
        </div>
        <a href="{{ route('admin.rombel.index') }}" class="btn-pv btn-ghost">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Info Rombel + Mini Stats --}}
    <div class="dr-top-grid">

        {{-- Info Rombel --}}
        <div class="dr-card">
            <div class="dr-card-head">
                <span class="dr-card-title">
                    <i class="bi bi-info-circle-fill me-2" style="color:#EF9F27;"></i>Info Rombel
                </span>
            </div>
            <div class="dr-card-body">
                <div class="info-grid">
                    @foreach([
                        ['Nama Rombel', $rombel->nama_rombel],
                        ['Tingkat',     $rombel->tingkat ? 'Kelas '.$rombel->tingkat : '-'],
                        ['Kurikulum',   $rombel->kurikulum ?? '-'],
                        ['Semester',    $rombel->semester?->nama ?? '-'],
                    ] as [$label, $value])
                    <div class="info-item">
                        <div class="info-label">{{ $label }}</div>
                        <div class="info-value">{{ $value }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Mini stat cards --}}
        <div class="dr-mini-stats">
            <div class="dr-mini-stat c-blue">
                <div class="dr-mini-ico"><i class="bi bi-person-fill" style="color:#fff;font-size:18px;"></i></div>
                <div>
                    <div class="dr-mini-num">{{ $totalL }}</div>
                    <div class="dr-mini-lbl">Siswa Laki-laki</div>
                </div>
            </div>
            <div class="dr-mini-stat c-pink">
                <div class="dr-mini-ico"><i class="bi bi-person-fill" style="color:#fff;font-size:18px;"></i></div>
                <div>
                    <div class="dr-mini-num">{{ $totalP }}</div>
                    <div class="dr-mini-lbl">Siswa Perempuan</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Filter --}}
    <div class="dr-card">
        <div class="dr-card-body">
            <form method="GET">
                <div class="filter-grid">
                    <div class="form-field">
                        <label>Cari Siswa</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NISN...">
                    </div>
                    <div class="form-field">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin">
                            <option value="">Semua</option>
                            <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-pv btn-primary-pv">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.rombel.show', $rombel) }}" class="btn-pv btn-ghost">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Siswa --}}
    <div class="dr-card">
        <div class="dr-card-head">
            <span class="dr-card-title">
                <i class="bi bi-people-fill me-2" style="color:#534AB7;"></i>Daftar Siswa
            </span>
            <span style="font-size:12px;color:#6b7280;background:#f3f4f6;padding:3px 12px;border-radius:99px;font-weight:600;">
                {{ $siswas->total() }} siswa
            </span>
        </div>

        {{-- Desktop table --}}
        <div class="dr-table-wrap">
            <table class="dr-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th style="width:100px;">Kelamin</th>
                        <th>Tempat, Tgl Lahir</th>
                        <th>Agama</th>
                        <th style="width:60px;text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $i => $siswa)
                    <tr>
                        <td style="color:#9ca3af;font-size:12px;">{{ $siswas->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="avatar {{ $siswa->jenis_kelamin === 'L' ? 'laki' : 'wanita' }}">
                                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                </div>
                                <div style="font-weight:700;font-size:13px;color:#111827;">{{ $siswa->nama }}</div>
                            </div>
                        </td>
                        <td style="font-size:12px;font-family:monospace;color:#374151;">{{ $siswa->nisn ?? '-' }}</td>
                        <td>
                            @if($siswa->jenis_kelamin === 'L')
                                <span class="badge badge-laki">Laki-laki</span>
                            @else
                                <span class="badge badge-wanita">Perempuan</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#6b7280;">
                            {{ $siswa->tempat_lahir ?? '-' }},
                            {{ $siswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td style="font-size:12px;color:#374151;">{{ $siswa->agama ?? '-' }}</td>
                        <td style="text-align:right;">
                            <a href="{{ route('admin.siswa.show', $siswa) }}" class="btn-sm-ico view" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-ico"><i class="bi bi-people" style="font-size:24px;color:#9ca3af;"></i></div>
                                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada siswa di rombel ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list --}}
        <div class="dr-card-list">
            @forelse($siswas as $siswa)
            <div class="dr-list-item">
                <div class="avatar {{ $siswa->jenis_kelamin === 'L' ? 'laki' : 'wanita' }}" style="margin-top:2px;">
                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                </div>
                <div class="dr-list-body">
                    <div class="dr-list-name">{{ $siswa->nama }}</div>
                    <div class="dr-list-sub">
                        @if($siswa->nisn) NISN: {{ $siswa->nisn }} &bull; @endif
                        {{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}
                    </div>
                    <div class="dr-list-row">
                        <div style="display:flex;gap:5px;flex-wrap:wrap;align-items:center;">
                            @if($siswa->jenis_kelamin === 'L')
                                <span class="badge badge-laki">Laki-laki</span>
                            @else
                                <span class="badge badge-wanita">Perempuan</span>
                            @endif
                            @if($siswa->agama)
                                <span style="font-size:11px;color:#6b7280;">{{ $siswa->agama }}</span>
                            @endif
                        </div>
                        <a href="{{ route('admin.siswa.show', $siswa) }}" class="btn-sm-ico view" title="Detail">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-ico"><i class="bi bi-people" style="font-size:24px;color:#9ca3af;"></i></div>
                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada siswa di rombel ini</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($siswas->hasPages())
        <div class="dr-pagination">
            <span class="dr-pag-info">
                Menampilkan {{ $siswas->firstItem() }}–{{ $siswas->lastItem() }} dari {{ $siswas->total() }} siswa
            </span>
            {{ $siswas->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection