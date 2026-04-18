@extends('layouts.app')

@section('page-title', 'Data Rombel')
@section('page-subtitle', 'Daftar rombongan belajar')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<style>
    .rb-page { width: 100%; padding: 1.5rem 2.5rem; }
    @media (max-width: 768px) { .rb-page { padding: 1rem; } }

    /* Header */
    .rb-header { display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .rb-header-left h1 { font-size: 22px; font-weight: 700; color: #111827; }
    .rb-header-left p  { font-size: 13px; color: #6b7280; margin-top: 3px; }
    @media (max-width: 480px) { .rb-header { flex-direction: column; align-items: flex-start; } }

    /* Stat cards — 4 kolom */
    .rb-stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 14px; margin-bottom: 1.5rem; }
    @media (max-width: 768px) { .rb-stat-grid { grid-template-columns: repeat(2, minmax(0,1fr)); gap: 10px; } }
    @media (max-width: 400px) { .rb-stat-grid { grid-template-columns: 1fr; } }
    .rb-stat { border-radius: 14px; padding: 16px 18px; display: flex; align-items: center;
        gap: 12px; position: relative; overflow: hidden; }
    .rb-stat.c-amber  { background: linear-gradient(135deg, #EF9F27 0%, #854F0B 100%); }
    .rb-stat.c-blue   { background: linear-gradient(135deg, #378ADD 0%, #0C447C 100%); }
    .rb-stat.c-purple { background: linear-gradient(135deg, #534AB7 0%, #3C3489 100%); }
    .rb-stat.c-green  { background: linear-gradient(135deg, #1D9E75 0%, #0F6E56 100%); }
    .rb-stat::after { content:''; position:absolute; right:-16px; top:-16px; width:80px; height:80px;
        border-radius:50%; background:rgba(255,255,255,.08); }
    .rb-stat-ico { width: 42px; height: 42px; border-radius: 11px; background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .rb-stat-num { font-size: 26px; font-weight: 800; color: #fff; line-height: 1; }
    .rb-stat-lbl { font-size: 12px; color: rgba(255,255,255,.8); margin-top: 3px; }

    /* Tabs */
    .rb-tabs { display: flex; gap: 4px; margin-bottom: 1.25rem; background: #f1f5f9;
        padding: 5px; border-radius: 12px; width: fit-content; max-width: 100%; flex-wrap: wrap; }
    .rb-tab { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
        border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;
        transition: all .2s; color: #64748b; white-space: nowrap; }
    .rb-tab.active { background: #fff; color: #534AB7; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
    .rb-tab-count { font-size: 11px; padding: 2px 7px; border-radius: 99px; font-weight: 700; }
    .rb-tab.active .rb-tab-count { background: #EEEDFE; color: #534AB7; }
    .rb-tab:not(.active) .rb-tab-count { background: #e2e8f0; color: #94a3b8; }
    @media (max-width: 600px) {
        .rb-tabs { width: 100%; }
        .rb-tab  { flex: 1; justify-content: center; padding: 8px 8px; font-size: 12px; }
    }

    /* Card */
    .rb-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
    .rb-card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid #f3f4f6; }
    .rb-card-title { font-size: 14px; font-weight: 700; color: #111827; }
    .rb-card-body  { padding: 18px; }

    /* Form fields */
    .form-field { display: flex; flex-direction: column; }
    .form-field label { font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 5px; letter-spacing: .05em; text-transform: uppercase; }
    .form-field input,
    .form-field select { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; font-family: inherit; background: #fff; color: #111827;
        outline: none; transition: border-color .15s; box-sizing: border-box; }
    .form-field input:focus,
    .form-field select:focus { border-color: #6366f1; }

    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; align-items: end; }
    .filter-actions { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; }

    /* Buttons */
    .btn-pv { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
        border-radius: 8px; border: none; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all .15s; font-family: inherit; text-decoration: none; }
    .btn-primary-pv { background: #3C3489; color: #fff; }
    .btn-primary-pv:hover { background: #26215C; color: #fff; }
    .btn-ghost { background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; }
    .btn-ghost:hover { background: #e2e8f0; }
    .btn-sm-view { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
        border-radius: 7px; border: none; font-size: 12px; font-weight: 600;
        background: #EEEDFE; color: #3C3489; cursor: pointer; text-decoration: none;
        transition: background .15s; white-space: nowrap; }
    .btn-sm-view:hover { background: #CECBF6; color: #26215C; }

    /* Rombel avatar */
    .rb-avatar { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center;
        justify-content: center; color: #fff; font-weight: 700; font-size: 12px; flex-shrink: 0; }
    .rb-avatar.kelas  { background: linear-gradient(135deg, #EF9F27, #854F0B); }
    .rb-avatar.mapel  { background: linear-gradient(135deg, #378ADD, #0C447C); }
    .rb-avatar.ekskul { background: linear-gradient(135deg, #534AB7, #3C3489); }

    /* Table */
    .rb-table-wrap { overflow-x: auto; }
    .rb-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 600px; }
    .rb-table th { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase;
        letter-spacing: .05em; padding: 10px 16px; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6; text-align: left; white-space: nowrap; }
    .rb-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .rb-table tr:last-child td { border-bottom: none; }
    .rb-table tr:hover td { background: #f8faff; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 99px; white-space: nowrap; }
    .badge-tingkat { background: #EEEDFE; color: #3C3489; }

    /* Mobile card list */
    .rb-card-list { display: none; }
    @media (max-width: 640px) {
        .rb-table-wrap { display: none; }
        .rb-card-list  { display: block; }
    }
    .rb-list-item { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; display: flex; gap: 12px; align-items: flex-start; }
    .rb-list-item:last-child { border-bottom: none; }
    .rb-list-body { flex: 1; min-width: 0; }
    .rb-list-name { font-weight: 700; font-size: 13px; color: #111827; }
    .rb-list-sub  { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .rb-list-row  { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; flex-wrap: wrap; gap: 6px; }

    /* Pagination */
    .rb-pagination { padding: 14px 18px; border-top: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .rb-pagination .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; flex-wrap: wrap; }
    .rb-pagination .page-item .page-link { display: inline-flex; align-items: center; justify-content: center;
        min-width: 34px; height: 34px; padding: 0 8px; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; color: #374151; font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all .15s; }
    .rb-pagination .page-item .page-link:hover { background: #f1f5f9; }
    .rb-pagination .page-item.active .page-link { background: #3C3489; border-color: #3C3489; color: #fff; }
    .rb-pagination .page-item.disabled .page-link { color: #d1d5db; pointer-events: none; }
    .rb-pag-info { font-size: 12px; color: #6b7280; }

    .empty-state { text-align: center; padding: 3.5rem 1rem; }
    .empty-ico { width: 56px; height: 56px; background: #f3f4f6; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
</style>

<div class="rb-page">

    {{-- Header --}}
    <div class="rb-header">
        <div class="rb-header-left">
            <h1><i class="bi bi-diagram-3-fill me-2" style="color:#EF9F27;font-size:20px;"></i>Data Rombel</h1>
            <p>Kelas reguler, mapel pilihan, dan ekstrakurikuler</p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="rb-stat-grid">
        <div class="rb-stat c-amber">
            <div class="rb-stat-ico"><i class="bi bi-building" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="rb-stat-num">{{ $countKelas }}</div>
                <div class="rb-stat-lbl">Rombel Kelas</div>
            </div>
        </div>
        <div class="rb-stat c-blue">
            <div class="rb-stat-ico"><i class="bi bi-book-fill" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="rb-stat-num">{{ $countMapel }}</div>
                <div class="rb-stat-lbl">Mapel Pilihan</div>
            </div>
        </div>
        <div class="rb-stat c-purple">
            <div class="rb-stat-ico"><i class="bi bi-trophy-fill" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="rb-stat-num">{{ $countEkskul }}</div>
                <div class="rb-stat-lbl">Ekstrakurikuler</div>
            </div>
        </div>
        <div class="rb-stat c-green">
            <div class="rb-stat-ico"><i class="bi bi-people-fill" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="rb-stat-num">{{ $totalSiswa }}</div>
                <div class="rb-stat-lbl">Total Siswa Aktif</div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="rb-tabs">
        @foreach([
            ['kelas',  'Kelas',         $countKelas,  'bi-building'],
            ['mapel',  'Mapel Pilihan',  $countMapel,  'bi-book-fill'],
            ['ekskul', 'Ekskul',         $countEkskul, 'bi-trophy-fill'],
            ['semua',  'Semua',          $countSemua,  'bi-grid-fill'],
        ] as [$key, $label, $count, $ico])
        <a href="{{ route('admin.rombel.index', array_merge(request()->except('tab','page'), ['tab'=>$key])) }}"
           class="rb-tab {{ $tab === $key ? 'active' : '' }}">
            <i class="bi {{ $ico }}" style="font-size:13px;"></i>
            {{ $label }}
            <span class="rb-tab-count">{{ $count }}</span>
        </a>
        @endforeach
    </div>

    {{-- Filter Form --}}
    <div class="rb-card">
        <div class="rb-card-body">
            <form method="GET" action="{{ route('admin.rombel.index') }}">
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
                    @if(in_array($tab, ['kelas', 'mapel', 'semua']))
                    <div class="form-field">
                        <label>Tingkat</label>
                        <select name="tingkat">
                            <option value="">Semua Tingkat</option>
                            @foreach($tingkats as $t)
                                <option value="{{ $t }}" {{ request('tingkat') == $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="form-field">
                        <label>Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ $tab === 'ekskul' ? 'Nama ekstrakurikuler...' : ($tab === 'mapel' ? 'Nama mapel pilihan...' : 'Nama kelas...') }}">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-pv btn-primary-pv">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.rombel.index', ['tab' => $tab]) }}" class="btn-pv btn-ghost">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="rb-card">
        <div class="rb-card-head">
            <span class="rb-card-title">
                @php
                    $tabIco = match($tab) { 'kelas' => 'bi-building', 'mapel' => 'bi-book-fill', 'ekskul' => 'bi-trophy-fill', default => 'bi-grid-fill' };
                    $tabLabel = match($tab) { 'kelas' => 'Rombel Kelas', 'mapel' => 'Mapel Pilihan', 'ekskul' => 'Ekstrakurikuler', default => 'Semua Rombel' };
                @endphp
                <i class="bi {{ $tabIco }} me-2" style="color:#534AB7;"></i>{{ $tabLabel }}
            </span>
            <span style="font-size:12px;color:#6b7280;background:#f3f4f6;padding:3px 12px;border-radius:99px;font-weight:600;">
                {{ $rombels->total() }} rombel
            </span>
        </div>

        {{-- Desktop table --}}
        <div class="rb-table-wrap">
            <table class="rb-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Rombel</th>
                        @if($tab === 'ekskul')
                            <th>Jenis Ekskul</th>
                            <th>Pembina</th>
                        @else
                            <th style="width:90px;">Tingkat</th>
                            <th>Kurikulum</th>
                        @endif
                        <th>Semester</th>
                        <th style="width:90px;">Jenis</th>
                        <th style="width:100px;">Siswa</th>
                        <th style="width:110px;text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rombels as $i => $rombel)
                    @php
                        $avatarClass = $rombel->isEkskul() ? 'ekskul' : ($rombel->isMapel() ? 'mapel' : 'kelas');
                    @endphp
                    <tr>
                        <td style="color:#9ca3af;font-size:12px;">{{ $rombels->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="rb-avatar {{ $avatarClass }}">
                                    {{ strtoupper(substr($rombel->nama_rombel, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:13px;color:#111827;">{{ $rombel->nama_rombel }}</div>
                                    @if($rombel->rombongan_belajar_id)
                                    <div style="font-size:10px;color:#9ca3af;font-family:monospace;">
                                        {{ substr($rombel->rombongan_belajar_id, 0, 10) }}…
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        @if($tab === 'ekskul')
                            <td style="font-size:12px;color:#374151;">{{ $rombel->jenis_rombel_str ?? '-' }}</td>
                            <td style="font-size:12px;color:#374151;">{{ $rombel->wali_kelas ?? '-' }}</td>
                        @else
                            <td>
                                @if($rombel->tingkat)
                                    <span class="badge badge-tingkat">Kelas {{ $rombel->tingkat }}</span>
                                @else
                                    <span style="color:#9ca3af;font-size:12px;">-</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:#374151;">{{ $rombel->kurikulum ?? '-' }}</td>
                        @endif
                        <td style="font-size:12px;color:#6b7280;">{{ $rombel->semester?->nama ?? '-' }}</td>
                        <td>
                            <span style="font-size:11px;padding:3px 10px;border-radius:99px;font-weight:600;{{ $rombel->jenisBadgeStyle() }}">
                                {{ $rombel->jenisLabel() }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:5px;">
                                <span style="font-size:18px;font-weight:800;color:#111827;">{{ $rombel->jumlah_siswa_aktif ?? 0 }}</span>
                                <span style="font-size:11px;color:#9ca3af;">siswa</span>
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('admin.rombel.show', $rombel) }}" class="btn-sm-view">
                                <i class="bi bi-eye-fill"></i> Lihat Siswa
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-ico"><i class="bi bi-diagram-3" style="font-size:24px;color:#9ca3af;"></i></div>
                                <p style="font-size:13px;color:#9ca3af;margin:0;">
                                    Belum ada data
                                    {{ $tab === 'ekskul' ? 'ekstrakurikuler' : ($tab === 'mapel' ? 'mapel pilihan' : 'rombel kelas') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list --}}
        <div class="rb-card-list">
            @forelse($rombels as $rombel)
            @php $avatarClass = $rombel->isEkskul() ? 'ekskul' : ($rombel->isMapel() ? 'mapel' : 'kelas'); @endphp
            <div class="rb-list-item">
                <div class="rb-avatar {{ $avatarClass }}" style="margin-top:2px;">
                    {{ strtoupper(substr($rombel->nama_rombel, 0, 2)) }}
                </div>
                <div class="rb-list-body">
                    <div class="rb-list-name">{{ $rombel->nama_rombel }}</div>
                    <div class="rb-list-sub">
                        {{ $rombel->semester?->nama ?? '-' }}
                        @if($rombel->tingkat) &bull; Kelas {{ $rombel->tingkat }} @endif
                        @if($rombel->kurikulum) &bull; {{ $rombel->kurikulum }} @endif
                    </div>
                    <div class="rb-list-row">
                        <div style="display:flex;gap:5px;align-items:center;flex-wrap:wrap;">
                            <span style="font-size:11px;padding:3px 10px;border-radius:99px;font-weight:600;{{ $rombel->jenisBadgeStyle() }}">
                                {{ $rombel->jenisLabel() }}
                            </span>
                            <span style="font-size:13px;font-weight:800;color:#111827;">{{ $rombel->jumlah_siswa_aktif ?? 0 }}</span>
                            <span style="font-size:11px;color:#9ca3af;">siswa</span>
                        </div>
                        <a href="{{ route('admin.rombel.show', $rombel) }}" class="btn-sm-view">
                            <i class="bi bi-eye-fill"></i> Lihat
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-ico"><i class="bi bi-diagram-3" style="font-size:24px;color:#9ca3af;"></i></div>
                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada data rombel</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($rombels->hasPages())
        <div class="rb-pagination">
            <span class="rb-pag-info">
                Menampilkan {{ $rombels->firstItem() }}–{{ $rombels->lastItem() }} dari {{ $rombels->total() }} rombel
            </span>
            {{ $rombels->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection