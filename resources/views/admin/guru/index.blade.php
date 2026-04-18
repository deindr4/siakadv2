@extends('layouts.app')

@section('page-title', 'Data Guru & GTK')
@section('page-subtitle', 'Kelola data guru dan tenaga kependidikan')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<style>
    .gg-page { width: 100%; padding: 1.5rem 2.5rem; }
    @media (max-width: 768px) { .gg-page { padding: 1rem; } }

    /* Header */
    .gg-header { display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .gg-header-left h1 { font-size: 22px; font-weight: 700; color: #111827; }
    .gg-header-left p  { font-size: 13px; color: #6b7280; margin-top: 3px; }
    @media (max-width: 480px) { .gg-header { flex-direction: column; align-items: flex-start; } }

    /* Tabs */
    .gg-tabs { display: flex; gap: 0; margin-bottom: 1.25rem; background: #fff;
        border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
    .gg-tab { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 13px 16px; font-size: 13px; font-weight: 600; text-decoration: none;
        color: #64748b; border-bottom: 3px solid transparent; transition: all .2s;
        background: #fff; white-space: nowrap; }
    .gg-tab.active { color: #534AB7; border-bottom-color: #534AB7; background: #f5f3ff; }
    .gg-tab:hover:not(.active) { background: #f8faff; }
    .gg-tab-count { display: inline-flex; align-items: center; justify-content: center;
        min-width: 22px; height: 22px; padding: 0 6px; border-radius: 99px; font-size: 11px; font-weight: 700; }
    .gg-tab.active .gg-tab-count { background: #534AB7; color: #fff; }
    .gg-tab:not(.active) .gg-tab-count { background: #f1f5f9; color: #64748b; }
    @media (max-width: 480px) {
        .gg-tab { padding: 10px 8px; font-size: 12px; }
        .gg-tab-count { display: none; }
    }

    /* Card */
    .gg-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
    .gg-card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid #f3f4f6; }
    .gg-card-title { font-size: 14px; font-weight: 700; color: #111827; }
    .gg-card-body  { padding: 18px; }

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
    .btn-success-pv { background: linear-gradient(135deg, #1D9E75, #0F6E56); color: #fff; }
    .btn-success-pv:hover { opacity: .88; color: #fff; }
    .btn-ghost { background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; }
    .btn-ghost:hover { background: #e2e8f0; }
    .btn-sm-ico { width: 32px; height: 32px; display: inline-flex; align-items: center;
        justify-content: center; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; cursor: pointer; font-size: 13px; transition: all .15s;
        color: #6b7280; text-decoration: none; }
    .btn-sm-ico.view:hover  { background: #f1f5f9; color: #374151; }
    .btn-sm-ico.edit:hover  { background: #ede9fe; border-color: #a5b4fc; color: #4f46e5; }
    .btn-sm-ico.del:hover   { background: #FCEBEB; border-color: #F09595; color: #A32D2D; }

    /* Avatar */
    .avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; color: #fff; font-weight: 700; font-size: 13px; flex-shrink: 0; }
    .avatar.laki   { background: linear-gradient(135deg, #534AB7, #3C3489); }
    .avatar.wanita { background: linear-gradient(135deg, #D4537E, #72243E); }

    /* Table */
    .gg-table-wrap { overflow-x: auto; }
    .gg-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 750px; }
    .gg-table th { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase;
        letter-spacing: .05em; padding: 10px 16px; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6; text-align: left; white-space: nowrap; }
    .gg-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .gg-table tr:last-child td { border-bottom: none; }
    .gg-table tr:hover td { background: #f8faff; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 99px; white-space: nowrap; }
    .badge-pns    { background: #EEEDFE; color: #3C3489; }
    .badge-pppk   { background: #E1F5EE; color: #0F6E56; }
    .badge-honor  { background: #FAEEDA; color: #854F0B; }
    .badge-guru   { background: #EEEDFE; color: #3C3489; }
    .badge-tendik { background: #FAEEDA; color: #854F0B; }
    .badge-dapodik { background: #E1F5EE; color: #0F6E56; }
    .badge-excel   { background: #FAEEDA; color: #854F0B; }
    .badge-manual  { background: #E6F1FB; color: #0C447C; }

    /* Mobile card list */
    .gg-card-list { display: none; }
    @media (max-width: 640px) {
        .gg-table-wrap { display: none; }
        .gg-card-list  { display: block; }
    }
    .gg-list-item { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; display: flex; gap: 12px; align-items: flex-start; }
    .gg-list-item:last-child { border-bottom: none; }
    .gg-list-body { flex: 1; min-width: 0; }
    .gg-list-name { font-weight: 700; font-size: 13px; color: #111827; }
    .gg-list-sub  { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .gg-list-row  { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; flex-wrap: wrap; gap: 6px; }

    /* Pagination */
    .gg-pagination { padding: 14px 18px; border-top: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .gg-pagination .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; flex-wrap: wrap; }
    .gg-pagination .page-item .page-link { display: inline-flex; align-items: center; justify-content: center;
        min-width: 34px; height: 34px; padding: 0 8px; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; color: #374151; font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all .15s; }
    .gg-pagination .page-item .page-link:hover { background: #f1f5f9; }
    .gg-pagination .page-item.active .page-link { background: #3C3489; border-color: #3C3489; color: #fff; }
    .gg-pagination .page-item.disabled .page-link { color: #d1d5db; pointer-events: none; }
    .gg-pag-info { font-size: 12px; color: #6b7280; }

    .empty-state { text-align: center; padding: 3.5rem 1rem; }
    .empty-ico { width: 56px; height: 56px; background: #f3f4f6; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
</style>

<div class="gg-page">

    {{-- Header --}}
    <div class="gg-header">
        <div class="gg-header-left">
            <h1><i class="bi bi-person-workspace me-2" style="color:#534AB7;font-size:20px;"></i>Data Guru & GTK</h1>
            <p>Guru dan Tenaga Kependidikan dari Dapodik</p>
        </div>
        <a href="{{ route('admin.guru.create') }}" class="btn-pv btn-success-pv">
            <i class="bi bi-plus-lg"></i> Tambah Manual
        </a>
    </div>

    {{-- Tabs --}}
    <div class="gg-tabs">
        @foreach([
            ['semua',  'Semua GTK',  $totalSemua,  'bi-people-fill'],
            ['guru',   'Guru',       $totalGuru,   'bi-person-workspace'],
            ['tendik', 'Tendik',     $totalTendik, 'bi-briefcase-fill'],
        ] as [$key, $label, $count, $ico])
        <a href="{{ request()->fullUrlWithQuery(['tab' => $key, 'page' => 1]) }}"
           class="gg-tab {{ $tab === $key ? 'active' : '' }}">
            <i class="bi {{ $ico }}" style="font-size:14px;"></i>
            {{ $label }}
            <span class="gg-tab-count">{{ $count }}</span>
        </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="gg-card">
        <div class="gg-card-body">
            <form method="GET" action="{{ route('admin.guru.index') }}">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="filter-grid">
                    <div class="form-field">
                        <label>Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nama, NIP, NUPTK, Jabatan...">
                    </div>
                    <div class="form-field">
                        <label>Status Kepegawaian</label>
                        <select name="status_kepegawaian">
                            <option value="">Semua</option>
                            @foreach($statusKepegawaian as $sk)
                                <option value="{{ $sk }}" {{ request('status_kepegawaian') == $sk ? 'selected' : '' }}>{{ $sk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-pv btn-primary-pv">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.guru.index', ['tab' => $tab]) }}" class="btn-pv btn-ghost">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="gg-card">
        <div class="gg-card-head">
            <span class="gg-card-title">
                @php
                    $tabIco   = match($tab) { 'guru' => 'bi-person-workspace', 'tendik' => 'bi-briefcase-fill', default => 'bi-people-fill' };
                    $tabLabel = match($tab) { 'guru' => 'Daftar Guru', 'tendik' => 'Daftar Tenaga Kependidikan', default => 'Semua GTK' };
                @endphp
                <i class="bi {{ $tabIco }} me-2" style="color:#534AB7;"></i>{{ $tabLabel }}
            </span>
            <span style="font-size:12px;color:#6b7280;background:#f3f4f6;padding:3px 12px;border-radius:99px;font-weight:600;">
                {{ $gurus->total() }} data
            </span>
        </div>

        {{-- Desktop table --}}
        <div class="gg-table-wrap">
            <table class="gg-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama</th>
                        <th>NIP / NUPTK</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Pendidikan</th>
                        <th style="width:80px;">Jenis</th>
                        <th style="width:80px;">Sumber</th>
                        <th style="width:90px;text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $i => $guru)
                    @php
                        $skClass = match($guru->status_kepegawaian) {
                            'PNS'   => 'badge-pns',
                            'PPPK'  => 'badge-pppk',
                            default => 'badge-honor',
                        };
                    @endphp
                    <tr>
                        <td style="color:#9ca3af;font-size:12px;">{{ $gurus->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="avatar {{ $guru->jenis_kelamin === 'L' ? 'laki' : 'wanita' }}">
                                    {{ strtoupper(substr($guru->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:13px;color:#111827;">{{ $guru->nama }}</div>
                                    <div style="font-size:11px;color:#9ca3af;">{{ $guru->jk_label }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($guru->nip)
                            <div style="font-size:11px;color:#9ca3af;">NIP</div>
                            <div style="font-family:monospace;font-size:12px;color:#374151;">{{ $guru->nip }}</div>
                            @endif
                            @if($guru->nuptk)
                            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">NUPTK</div>
                            <div style="font-family:monospace;font-size:12px;color:#374151;">{{ $guru->nuptk }}</div>
                            @endif
                            @if(!$guru->nip && !$guru->nuptk)
                            <span style="color:#9ca3af;font-size:12px;">-</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#374151;">{{ $guru->jabatan ?? '-' }}</td>
                        <td>
                            @if($guru->status_kepegawaian)
                                <span class="badge {{ $skClass }}">{{ $guru->status_kepegawaian }}</span>
                            @else
                                <span style="color:#9ca3af;font-size:12px;">-</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#374151;">{{ $guru->pendidikan_terakhir ?? '-' }}</td>
                        <td>
                            @if($guru->jenis_ptk === 'Guru')
                                <span class="badge badge-guru">Guru</span>
                            @elseif($guru->jenis_ptk)
                                <span class="badge badge-tendik">Tendik</span>
                            @else
                                <span style="color:#9ca3af;font-size:12px;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($guru->sumber_data === 'dapodik')
                                <span class="badge badge-dapodik">Dapodik</span>
                            @elseif($guru->sumber_data === 'excel')
                                <span class="badge badge-excel">Excel</span>
                            @else
                                <span class="badge badge-manual">Manual</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;justify-content:flex-end;">
                                <a href="{{ route('admin.guru.show', $guru) }}" class="btn-sm-ico view" title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.guru.edit', $guru) }}" class="btn-sm-ico edit" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('admin.guru.destroy', $guru) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-sm-ico del" title="Hapus"
                                        onclick="confirmDelete(this.closest('form'))">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-ico"><i class="bi bi-person-workspace" style="font-size:24px;color:#9ca3af;"></i></div>
                                <p style="font-size:13px;color:#9ca3af;margin:0;">
                                    Belum ada data {{ $tab === 'guru' ? 'guru' : ($tab === 'tendik' ? 'tenaga kependidikan' : 'GTK') }}
                                </p>
                                @if(request('search'))
                                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Tidak ada hasil untuk "{{ request('search') }}"</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list --}}
        <div class="gg-card-list">
            @forelse($gurus as $guru)
            @php
                $skClass = match($guru->status_kepegawaian) { 'PNS' => 'badge-pns', 'PPPK' => 'badge-pppk', default => 'badge-honor' };
            @endphp
            <div class="gg-list-item">
                <div class="avatar {{ $guru->jenis_kelamin === 'L' ? 'laki' : 'wanita' }}" style="margin-top:2px;">
                    {{ strtoupper(substr($guru->nama, 0, 1)) }}
                </div>
                <div class="gg-list-body">
                    <div class="gg-list-name">{{ $guru->nama }}</div>
                    <div class="gg-list-sub">
                        {{ $guru->jabatan ?? '-' }}
                        @if($guru->nip) &bull; NIP: {{ $guru->nip }} @endif
                    </div>
                    <div class="gg-list-row">
                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                            @if($guru->status_kepegawaian)
                                <span class="badge {{ $skClass }}">{{ $guru->status_kepegawaian }}</span>
                            @endif
                            @if($guru->jenis_ptk === 'Guru')
                                <span class="badge badge-guru">Guru</span>
                            @elseif($guru->jenis_ptk)
                                <span class="badge badge-tendik">Tendik</span>
                            @endif
                        </div>
                        <div style="display:flex;gap:5px;">
                            <a href="{{ route('admin.guru.show', $guru) }}" class="btn-sm-ico view" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <a href="{{ route('admin.guru.edit', $guru) }}" class="btn-sm-ico edit" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-ico"><i class="bi bi-person-workspace" style="font-size:24px;color:#9ca3af;"></i></div>
                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada data GTK</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($gurus->hasPages())
        <div class="gg-pagination">
            <span class="gg-pag-info">
                Menampilkan {{ $gurus->firstItem() }}–{{ $gurus->lastItem() }} dari {{ $gurus->total() }} data
            </span>
            {{ $gurus->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection