@extends('layouts.app')

@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Daftar siswa aktif')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<style>
    .ds-page { width: 100%; padding: 1.5rem 2.5rem; }
    @media (max-width: 768px) { .ds-page { padding: 1rem; } }

    /* Header */
    .ds-header { display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .ds-header-left h1 { font-size: 22px; font-weight: 700; color: #111827; }
    .ds-header-left p  { font-size: 13px; color: #6b7280; margin-top: 3px; }
    @media (max-width: 480px) { .ds-header { flex-direction: column; align-items: flex-start; } }

    /* Stat cards */
    .ds-stat-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 14px; margin-bottom: 1.5rem; }
    @media (max-width: 600px) { .ds-stat-grid { grid-template-columns: 1fr; gap: 10px; } }

    .ds-stat { border-radius: 14px; padding: 18px 20px; display: flex; align-items: center;
        gap: 14px; position: relative; overflow: hidden; }
    .ds-stat.c-purple { background: linear-gradient(135deg, #534AB7 0%, #3C3489 100%); }
    .ds-stat.c-blue   { background: linear-gradient(135deg, #378ADD 0%, #0C447C 100%); }
    .ds-stat.c-pink   { background: linear-gradient(135deg, #D4537E 0%, #72243E 100%); }
    .ds-stat::after { content:''; position:absolute; right:-20px; top:-20px; width:90px; height:90px;
        border-radius:50%; background:rgba(255,255,255,.08); }
    .ds-stat-ico { width: 46px; height: 46px; border-radius: 12px; background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ds-stat-num { font-size: 28px; font-weight: 800; color: #fff; line-height: 1; }
    .ds-stat-lbl { font-size: 13px; color: rgba(255,255,255,.8); margin-top: 3px; }

    /* Card */
    .ds-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
    .ds-card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid #f3f4f6; }
    .ds-card-title { font-size: 14px; font-weight: 700; color: #111827; }
    .ds-card-body  { padding: 18px; }

    /* Form fields */
    .form-field { display: flex; flex-direction: column; }
    .form-field label { font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 5px; letter-spacing: .05em; text-transform: uppercase; }
    .form-field input,
    .form-field select { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; font-family: inherit; background: #fff; color: #111827;
        outline: none; transition: border-color .15s; box-sizing: border-box; }
    .form-field input:focus,
    .form-field select:focus { border-color: #6366f1; }

    /* Filter grid */
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; align-items: end; }
    .filter-actions { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; }

    /* Buttons */
    .btn-pv { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
        border-radius: 8px; border: none; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all .15s; font-family: inherit; text-decoration: none; white-space: nowrap; }
    .btn-primary-pv { background: #3C3489; color: #fff; }
    .btn-primary-pv:hover { background: #26215C; color: #fff; }
    .btn-success-pv { background: linear-gradient(135deg, #1D9E75, #0F6E56); color: #fff; }
    .btn-success-pv:hover { opacity: .88; color: #fff; }
    .btn-ghost { background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; }
    .btn-ghost:hover { background: #e2e8f0; }
    .btn-sm-ico { width: 32px; height: 32px; display: inline-flex; align-items: center;
        justify-content: center; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; cursor: pointer; font-size: 13px; transition: all .15s; color: #6b7280; text-decoration: none; }
    .btn-sm-ico.view:hover { background: #ede9fe; border-color: #a5b4fc; color: #4f46e5; }
    .btn-sm-ico.edit:hover { background: #FAEEDA; border-color: #FAC775; color: #854F0B; }

    /* Avatar */
    .avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; color: #fff; font-weight: 700; font-size: 13px; flex-shrink: 0; }
    .avatar.laki   { background: linear-gradient(135deg, #534AB7, #3C3489); }
    .avatar.wanita { background: linear-gradient(135deg, #D4537E, #72243E); }

    /* Table */
    .ds-table-wrap { overflow-x: auto; }
    .ds-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 680px; }
    .ds-table th { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase;
        letter-spacing: .05em; padding: 10px 16px; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6; text-align: left; white-space: nowrap; }
    .ds-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .ds-table tr:last-child td { border-bottom: none; }
    .ds-table tr:hover td { background: #f8faff; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 99px; white-space: nowrap; }
    .badge-laki   { background: #EEEDFE; color: #3C3489; }
    .badge-wanita { background: #FBEAF0; color: #72243E; }
    .badge-dapodik { background: #E1F5EE; color: #0F6E56; }
    .badge-excel   { background: #FAEEDA; color: #854F0B; }
    .badge-manual  { background: #E6F1FB; color: #0C447C; }

    /* Mobile card list */
    .ds-card-list { display: none; }
    @media (max-width: 640px) {
        .ds-table-wrap { display: none; }
        .ds-card-list  { display: block; }
    }
    .ds-list-item { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; display: flex; gap: 12px; align-items: flex-start; }
    .ds-list-item:last-child { border-bottom: none; }
    .ds-list-body { flex: 1; min-width: 0; }
    .ds-list-name { font-weight: 700; font-size: 13px; color: #111827; }
    .ds-list-sub  { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .ds-list-row  { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; flex-wrap: wrap; gap: 6px; }

    /* Pagination */
    .ds-pagination { padding: 14px 18px; border-top: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .ds-pagination .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; flex-wrap: wrap; }
    .ds-pagination .page-item .page-link { display: inline-flex; align-items: center; justify-content: center;
        min-width: 34px; height: 34px; padding: 0 8px; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; color: #374151; font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all .15s; }
    .ds-pagination .page-item .page-link:hover { background: #f1f5f9; }
    .ds-pagination .page-item.active .page-link { background: #3C3489; border-color: #3C3489; color: #fff; }
    .ds-pagination .page-item.disabled .page-link { color: #d1d5db; pointer-events: none; }
    .ds-pag-info { font-size: 12px; color: #6b7280; }

    .empty-state { text-align: center; padding: 3.5rem 1rem; }
    .empty-ico { width: 56px; height: 56px; background: #f3f4f6; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
</style>

<div class="ds-page">

    {{-- Header --}}
    <div class="ds-header">
        <div class="ds-header-left">
            <h1><i class="bi bi-people-fill me-2" style="color:#534AB7;font-size:20px;"></i>Data Siswa</h1>
            <p>Daftar siswa aktif semester ini</p>
        </div>
        <a href="{{ route('admin.siswa.create') }}" class="btn-pv btn-success-pv">
            <i class="bi bi-plus-lg"></i> Tambah Siswa
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="ds-stat-grid">
        <div class="ds-stat c-purple">
            <div class="ds-stat-ico"><i class="bi bi-people-fill" style="color:#fff;font-size:20px;"></i></div>
            <div>
                <div class="ds-stat-num">{{ $totalAktif }}</div>
                <div class="ds-stat-lbl">Total Siswa Aktif</div>
            </div>
        </div>
        <div class="ds-stat c-blue">
            <div class="ds-stat-ico"><i class="bi bi-person-fill" style="color:#fff;font-size:20px;"></i></div>
            <div>
                <div class="ds-stat-num">{{ $totalL }}</div>
                <div class="ds-stat-lbl">Laki-laki</div>
            </div>
        </div>
        <div class="ds-stat c-pink">
            <div class="ds-stat-ico"><i class="bi bi-person-fill" style="color:#fff;font-size:20px;"></i></div>
            <div>
                <div class="ds-stat-num">{{ $totalP }}</div>
                <div class="ds-stat-lbl">Perempuan</div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="ds-card">
        <div class="ds-card-body">
            <form method="GET" action="{{ route('admin.siswa.index') }}">
                <div class="filter-grid">
                    <div class="form-field">
                        <label>Semester</label>
                        <select name="semester_id">
                            <option value="">Semua Semester</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->nama }}{{ $sem->is_aktif ? ' ✓' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Tingkat</label>
                        <select name="tingkat">
                            <option value="">Semua Tingkat</option>
                            @foreach($tingkats as $t)
                                <option value="{{ $t }}" {{ request('tingkat') == $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Rombel</label>
                        <select name="rombel">
                            <option value="">Semua Rombel</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->rombongan_belajar_id }}" {{ request('rombel') == $r->rombongan_belajar_id ? 'selected' : '' }}>
                                    {{ $r->nama_rombel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin">
                            <option value="">Semua</option>
                            <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, NISN, NIPD...">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-pv btn-primary-pv">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.siswa.index') }}" class="btn-pv btn-ghost">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="ds-card">
        <div class="ds-card-head">
            <span class="ds-card-title">
                <i class="bi bi-people-fill me-2" style="color:#534AB7;"></i>Daftar Siswa Aktif
            </span>
            <span style="font-size:12px;color:#6b7280;background:#f3f4f6;padding:3px 12px;border-radius:99px;font-weight:600;">
                {{ $siswas->total() }} siswa
            </span>
        </div>

        {{-- Desktop table --}}
        <div class="ds-table-wrap">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas / Rombel</th>
                        <th>Kelamin</th>
                        <th>Agama</th>
                        <th>Sumber</th>
                        <th style="width:80px;text-align:right;">Aksi</th>
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
                                <div>
                                    <div style="font-weight:700;font-size:13px;color:#111827;">{{ $siswa->nama }}</div>
                                    <div style="font-size:11px;color:#9ca3af;font-family:monospace;">NIPD: {{ $siswa->nipd ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;font-family:monospace;color:#374151;">{{ $siswa->nisn ?? '-' }}</td>
                        <td>
                            <div style="font-weight:700;font-size:13px;color:#111827;">{{ $siswa->nama_rombel ?? '-' }}</div>
                            <div style="font-size:11px;color:#9ca3af;">Kelas {{ $siswa->tingkat_pendidikan_id ?? '-' }}</div>
                        </td>
                        <td>
                            @if($siswa->jenis_kelamin === 'L')
                                <span class="badge badge-laki">Laki-laki</span>
                            @else
                                <span class="badge badge-wanita">Perempuan</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#6b7280;">{{ $siswa->agama ?? '-' }}</td>
                        <td>
                            @if($siswa->sumber_data === 'dapodik')
                                <span class="badge badge-dapodik">Dapodik</span>
                            @elseif($siswa->sumber_data === 'excel')
                                <span class="badge badge-excel">Excel</span>
                            @else
                                <span class="badge badge-manual">Manual</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('admin.siswa.show', $siswa) }}" class="btn-sm-ico view" title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn-sm-ico edit" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-ico"><i class="bi bi-people" style="font-size:24px;color:#9ca3af;"></i></div>
                                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada data siswa aktif</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list --}}
        <div class="ds-card-list">
            @forelse($siswas as $siswa)
            <div class="ds-list-item">
                <div class="avatar {{ $siswa->jenis_kelamin === 'L' ? 'laki' : 'wanita' }}" style="margin-top:2px;">
                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                </div>
                <div class="ds-list-body">
                    <div class="ds-list-name">{{ $siswa->nama }}</div>
                    <div class="ds-list-sub">
                        {{ $siswa->nama_rombel ?? '-' }}
                        @if($siswa->nisn) &bull; NISN: {{ $siswa->nisn }} @endif
                    </div>
                    <div class="ds-list-row">
                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                            @if($siswa->jenis_kelamin === 'L')
                                <span class="badge badge-laki">Laki-laki</span>
                            @else
                                <span class="badge badge-wanita">Perempuan</span>
                            @endif
                            @if($siswa->sumber_data === 'dapodik')
                                <span class="badge badge-dapodik">Dapodik</span>
                            @elseif($siswa->sumber_data === 'excel')
                                <span class="badge badge-excel">Excel</span>
                            @else
                                <span class="badge badge-manual">Manual</span>
                            @endif
                        </div>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.siswa.show', $siswa) }}" class="btn-sm-ico view" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn-sm-ico edit" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-ico"><i class="bi bi-people" style="font-size:24px;color:#9ca3af;"></i></div>
                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada data siswa aktif</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($siswas->hasPages())
        <div class="ds-pagination">
            <span class="ds-pag-info">
                Menampilkan {{ $siswas->firstItem() }}–{{ $siswas->lastItem() }} dari {{ $siswas->total() }} siswa
            </span>
            {{ $siswas->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection