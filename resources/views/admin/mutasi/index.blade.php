@extends('layouts.app')

@section('page-title', 'Mutasi Siswa')
@section('page-subtitle', 'Data mutasi, putus sekolah, dan berhenti')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<style>
    .mt-page { width: 100%; padding: 1.5rem 2.5rem; }
    @media (max-width: 768px) { .mt-page { padding: 1rem; } }

    /* Header */
    .mt-header { display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .mt-header-left h1 { font-size: 22px; font-weight: 700; color: #111827; }
    .mt-header-left p  { font-size: 13px; color: #6b7280; margin-top: 3px; }
    @media (max-width: 480px) { .mt-header { flex-direction: column; align-items: flex-start; } }

    /* Stat cards */
    .mt-stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 14px; margin-bottom: 1.5rem; }
    @media (max-width: 768px) { .mt-stat-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
    @media (max-width: 400px) { .mt-stat-grid { grid-template-columns: 1fr; } }

    .mt-stat { border-radius: 14px; padding: 16px 18px; display: flex; align-items: center;
        gap: 12px; position: relative; overflow: hidden; }
    .mt-stat.c-purple { background: linear-gradient(135deg, #534AB7 0%, #3C3489 100%); }
    .mt-stat.c-blue   { background: linear-gradient(135deg, #378ADD 0%, #0C447C 100%); }
    .mt-stat.c-amber  { background: linear-gradient(135deg, #EF9F27 0%, #854F0B 100%); }
    .mt-stat.c-red    { background: linear-gradient(135deg, #E24B4A 0%, #A32D2D 100%); }
    .mt-stat::after { content:''; position:absolute; right:-16px; top:-16px; width:80px; height:80px;
        border-radius:50%; background:rgba(255,255,255,.08); }
    .mt-stat-ico { width: 42px; height: 42px; border-radius: 11px; background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .mt-stat-num { font-size: 26px; font-weight: 800; color: #fff; line-height: 1; }
    .mt-stat-lbl { font-size: 12px; color: rgba(255,255,255,.8); margin-top: 3px; }

    /* Tab */
    .mt-tabs { display: flex; gap: 4px; margin-bottom: 1.25rem; background: #f1f5f9;
        padding: 5px; border-radius: 12px; width: fit-content; max-width: 100%; flex-wrap: wrap; }
    .mt-tab { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px;
        border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;
        transition: all .2s; color: #64748b; white-space: nowrap; }
    .mt-tab.active { background: #fff; color: #534AB7; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
    .mt-tab-count { font-size: 11px; padding: 2px 7px; border-radius: 99px; font-weight: 700; }
    .mt-tab.active .mt-tab-count { background: #EEEDFE; color: #534AB7; }
    .mt-tab:not(.active) .mt-tab-count { background: #e2e8f0; color: #94a3b8; }
    @media (max-width: 640px) {
        .mt-tabs { width: 100%; }
        .mt-tab  { flex: 1; justify-content: center; padding: 8px 10px; font-size: 12px; }
    }

    /* Card */
    .mt-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
    .mt-card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid #f3f4f6; }
    .mt-card-title { font-size: 14px; font-weight: 700; color: #111827; }
    .mt-card-body  { padding: 18px; }

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
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; align-items: end; }
    .filter-actions { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; }

    /* Buttons */
    .btn-pv { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
        border-radius: 8px; border: none; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all .15s; font-family: inherit; text-decoration: none; white-space: nowrap; }
    .btn-primary-pv { background: #3C3489; color: #fff; }
    .btn-primary-pv:hover { background: #26215C; color: #fff; }
    .btn-ghost { background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; }
    .btn-ghost:hover { background: #e2e8f0; }
    .btn-sm-ico { width: 32px; height: 32px; display: inline-flex; align-items: center;
        justify-content: center; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; cursor: pointer; font-size: 13px; transition: all .15s; color: #6b7280; text-decoration: none; }
    .btn-sm-ico.view:hover    { background: #ede9fe; border-color: #a5b4fc; color: #4f46e5; }
    .btn-sm-ico.restore:hover { background: #dcfce7; border-color: #86efac; color: #16a34a; }

    /* Avatar */
    .avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; color: #fff; font-weight: 700; font-size: 13px; flex-shrink: 0; }
    .avatar.laki   { background: linear-gradient(135deg, #534AB7, #3C3489); }
    .avatar.wanita { background: linear-gradient(135deg, #D4537E, #72243E); }

    /* Table */
    .mt-table-wrap { overflow-x: auto; }
    .mt-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 700px; }
    .mt-table th { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase;
        letter-spacing: .05em; padding: 10px 16px; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6; text-align: left; white-space: nowrap; }
    .mt-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .mt-table tr:last-child td { border-bottom: none; }
    .mt-table tr:hover td { background: #f8faff; }

    /* Status badges */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 99px; white-space: nowrap; }
    .badge-masuk   { background: #E6F1FB; color: #0C447C; }
    .badge-keluar  { background: #FAEEDA; color: #854F0B; }
    .badge-putus   { background: #FCEBEB; color: #A32D2D; }
    .badge-berhenti{ background: #FCEBEB; color: #A32D2D; }
    .badge-laki    { background: #EEEDFE; color: #3C3489; }
    .badge-wanita  { background: #FBEAF0; color: #72243E; }

    /* Mobile card list */
    .mt-card-list { display: none; }
    @media (max-width: 640px) {
        .mt-table-wrap { display: none; }
        .mt-card-list  { display: block; }
    }
    .mt-list-item { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; display: flex; gap: 12px; align-items: flex-start; }
    .mt-list-item:last-child { border-bottom: none; }
    .mt-list-body { flex: 1; min-width: 0; }
    .mt-list-name { font-weight: 700; font-size: 13px; color: #111827; }
    .mt-list-sub  { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .mt-list-row  { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; flex-wrap: wrap; gap: 6px; }

    /* Pagination */
    .mt-pagination { padding: 14px 18px; border-top: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .mt-pagination .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; flex-wrap: wrap; }
    .mt-pagination .page-item .page-link { display: inline-flex; align-items: center; justify-content: center;
        min-width: 34px; height: 34px; padding: 0 8px; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; color: #374151; font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all .15s; }
    .mt-pagination .page-item .page-link:hover { background: #f1f5f9; }
    .mt-pagination .page-item.active .page-link { background: #3C3489; border-color: #3C3489; color: #fff; }
    .mt-pagination .page-item.disabled .page-link { color: #d1d5db; pointer-events: none; }
    .mt-pag-info { font-size: 12px; color: #6b7280; }

    .empty-state { text-align: center; padding: 3.5rem 1rem; }
    .empty-ico { width: 56px; height: 56px; background: #f3f4f6; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
</style>

<div class="mt-page">

    {{-- Header --}}
    <div class="mt-header">
        <div class="mt-header-left">
            <h1><i class="bi bi-arrow-left-right me-2" style="color:#534AB7;font-size:20px;"></i>Mutasi Siswa</h1>
            <p>Data siswa mutasi, putus sekolah, dan berhenti</p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="mt-stat-grid">
        <div class="mt-stat c-purple">
            <div class="mt-stat-ico"><i class="bi bi-clipboard-data-fill" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="mt-stat-num">{{ $countSemua }}</div>
                <div class="mt-stat-lbl">Total Mutasi</div>
            </div>
        </div>
        <div class="mt-stat c-blue">
            <div class="mt-stat-ico"><i class="bi bi-box-arrow-in-right" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="mt-stat-num">{{ $countMutasiMasuk }}</div>
                <div class="mt-stat-lbl">Mutasi Masuk</div>
            </div>
        </div>
        <div class="mt-stat c-amber">
            <div class="mt-stat-ico"><i class="bi bi-box-arrow-right" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="mt-stat-num">{{ $countMutasiKeluar }}</div>
                <div class="mt-stat-lbl">Mutasi Keluar</div>
            </div>
        </div>
        <div class="mt-stat c-red">
            <div class="mt-stat-ico"><i class="bi bi-x-circle-fill" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="mt-stat-num">{{ $countPutus + $countBerhenti }}</div>
                <div class="mt-stat-lbl">Putus / Berhenti</div>
            </div>
        </div>
    </div>

    {{-- Tab Filter --}}
    <div class="mt-tabs">
        @foreach([
            ['semua',         'Semua',         $countSemua,        'bi-grid-fill'],
            ['mutasi_masuk',  'Mutasi Masuk',  $countMutasiMasuk,  'bi-box-arrow-in-right'],
            ['mutasi_keluar', 'Mutasi Keluar', $countMutasiKeluar, 'bi-box-arrow-right'],
            ['putus_sekolah', 'Putus Sekolah', $countPutus,        'bi-x-circle'],
            ['berhenti',      'Berhenti',      $countBerhenti,     'bi-slash-circle'],
        ] as [$key, $label, $count, $ico])
        <a href="{{ route('admin.mutasi.index', array_merge(request()->except('tab','page'), ['tab'=>$key])) }}"
           class="mt-tab {{ $tab === $key ? 'active' : '' }}">
            <i class="bi {{ $ico }}" style="font-size:13px;"></i>
            {{ $label }}
            <span class="mt-tab-count">{{ $count }}</span>
        </a>
        @endforeach
    </div>

    {{-- Filter Form --}}
    <div class="mt-card">
        <div class="mt-card-body">
            <form method="GET" action="{{ route('admin.mutasi.index') }}">
                <input type="hidden" name="tab" value="{{ $tab }}">
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
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin">
                            <option value="">Semua</option>
                            <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NISN...">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-pv btn-primary-pv">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.mutasi.index', ['tab' => $tab]) }}" class="btn-pv btn-ghost">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="mt-card">
        <div class="mt-card-head">
            <span class="mt-card-title">
                <i class="bi bi-arrow-left-right me-2" style="color:#534AB7;"></i>Data Mutasi
            </span>
            <span style="font-size:12px;color:#6b7280;background:#f3f4f6;padding:3px 12px;border-radius:99px;font-weight:600;">
                {{ $siswas->total() }} siswa
            </span>
        </div>

        {{-- Desktop table --}}
        <div class="mt-table-wrap">
            <table class="mt-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas / Rombel</th>
                        <th>Status Mutasi</th>
                        <th style="width:90px;">Tanggal</th>
                        <th>Keterangan</th>
                        <th style="width:80px;text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $i => $siswa)
                    @php
                        $badgeClass = match($siswa->status_mutasi) {
                            'mutasi_masuk'  => 'badge-masuk',
                            'mutasi_keluar' => 'badge-keluar',
                            'putus_sekolah' => 'badge-putus',
                            'berhenti'      => 'badge-berhenti',
                            default         => '',
                        };
                        $statusLabel = match($siswa->status_mutasi) {
                            'mutasi_masuk'  => 'Mutasi Masuk',
                            'mutasi_keluar' => 'Mutasi Keluar',
                            'putus_sekolah' => 'Putus Sekolah',
                            'berhenti'      => 'Berhenti',
                            default         => $siswa->status_mutasi,
                        };
                        $statusIcon = match($siswa->status_mutasi) {
                            'mutasi_masuk'  => 'bi-box-arrow-in-right',
                            'mutasi_keluar' => 'bi-box-arrow-right',
                            'putus_sekolah' => 'bi-x-circle-fill',
                            'berhenti'      => 'bi-slash-circle-fill',
                            default         => 'bi-dash',
                        };
                    @endphp
                    <tr>
                        <td style="color:#9ca3af;font-size:12px;">{{ $siswas->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="avatar {{ $siswa->jenis_kelamin === 'L' ? 'laki' : 'wanita' }}">
                                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:13px;color:#111827;">{{ $siswa->nama }}</div>
                                    <div style="font-size:11px;color:#9ca3af;">
                                        {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;font-family:monospace;color:#374151;">{{ $siswa->nisn ?? '-' }}</td>
                        <td>
                            <div style="font-weight:700;font-size:13px;color:#111827;">{{ $siswa->nama_rombel ?? '-' }}</div>
                            <div style="font-size:11px;color:#9ca3af;">Kelas {{ $siswa->tingkat_pendidikan_id ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $badgeClass }}">
                                <i class="bi {{ $statusIcon }} me-1" style="font-size:10px;"></i>
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:#6b7280;white-space:nowrap;">
                            {{ $siswa->tanggal_mutasi?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td style="font-size:12px;color:#6b7280;max-width:160px;">
                            <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ $siswa->keterangan_mutasi ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('admin.siswa.show', $siswa) }}" class="btn-sm-ico view" title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.mutasi.restore', $siswa) }}"
                                    onsubmit="return confirm('Kembalikan siswa ini ke status aktif?')" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-sm-ico restore" title="Kembalikan ke Aktif">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-ico"><i class="bi bi-arrow-left-right" style="font-size:24px;color:#9ca3af;"></i></div>
                                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada data mutasi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list --}}
        <div class="mt-card-list">
            @forelse($siswas as $siswa)
            @php
                $badgeClass  = match($siswa->status_mutasi) { 'mutasi_masuk' => 'badge-masuk', 'mutasi_keluar' => 'badge-keluar', 'putus_sekolah' => 'badge-putus', 'berhenti' => 'badge-berhenti', default => '' };
                $statusLabel = match($siswa->status_mutasi) { 'mutasi_masuk' => 'Mutasi Masuk', 'mutasi_keluar' => 'Mutasi Keluar', 'putus_sekolah' => 'Putus Sekolah', 'berhenti' => 'Berhenti', default => $siswa->status_mutasi };
            @endphp
            <div class="mt-list-item">
                <div class="avatar {{ $siswa->jenis_kelamin === 'L' ? 'laki' : 'wanita' }}" style="margin-top:2px;">
                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                </div>
                <div class="mt-list-body">
                    <div class="mt-list-name">{{ $siswa->nama }}</div>
                    <div class="mt-list-sub">
                        {{ $siswa->nama_rombel ?? '-' }}
                        @if($siswa->nisn) &bull; NISN: {{ $siswa->nisn }} @endif
                        &bull; {{ $siswa->tanggal_mutasi?->format('d/m/Y') ?? '-' }}
                    </div>
                    @if($siswa->keterangan_mutasi)
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">{{ $siswa->keterangan_mutasi }}</div>
                    @endif
                    <div class="mt-list-row">
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.siswa.show', $siswa) }}" class="btn-sm-ico view" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.mutasi.restore', $siswa) }}"
                                onsubmit="return confirm('Kembalikan siswa ini ke status aktif?')" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-sm-ico restore" title="Kembalikan ke Aktif">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-ico"><i class="bi bi-arrow-left-right" style="font-size:24px;color:#9ca3af;"></i></div>
                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada data mutasi</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($siswas->hasPages())
        <div class="mt-pagination">
            <span class="mt-pag-info">
                Menampilkan {{ $siswas->firstItem() }}–{{ $siswas->lastItem() }} dari {{ $siswas->total() }} siswa
            </span>
            {{ $siswas->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection