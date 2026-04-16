@extends('layouts.app')

@section('page-title', 'Input Pelanggaran')
@section('page-subtitle', 'Pencatatan pelanggaran siswa')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<style>
    .pv-page { width: 100%; padding: 1.5rem 2.5rem; }
    @media (max-width: 768px) { .pv-page { padding: 1rem; } }

    /* ── Header ──────────────────────────────────────── */
    .pv-header { display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .pv-header-left h1 { font-size: 22px; font-weight: 700; color: #111827; }
    .pv-header-left p  { font-size: 13px; color: #6b7280; margin-top: 3px; }
    @media (max-width: 480px) {
        .pv-header { flex-direction: column; align-items: flex-start; }
    }

    /* ── Alert ───────────────────────────────────────── */
    .pv-alert { display: flex; align-items: center; gap: 10px; padding: 13px 16px;
        border-radius: 10px; border: 1px solid #bbf7d0; background: #f0fdf4;
        color: #166534; font-size: 13px; font-weight: 600; margin-bottom: 1.25rem; }
    .pv-alert-close { margin-left: auto; background: none; border: none;
        font-size: 18px; cursor: pointer; color: inherit; opacity: .6; }
    .pv-alert-close:hover { opacity: 1; }

    /* ── Stat cards — BERWARNA ──────────────────────── */
    .stat-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr));
        gap: 14px; margin-bottom: 1.5rem; }
    @media (max-width: 600px) { .stat-grid { grid-template-columns: 1fr; gap: 10px; } }

    .stat-card-v2 { border-radius: 14px; padding: 18px 20px;
        display: flex; align-items: center; gap: 14px; position: relative; overflow: hidden; }
    .stat-card-v2.c-purple { background: linear-gradient(135deg, #534AB7 0%, #3C3489 100%); }
    .stat-card-v2.c-amber  { background: linear-gradient(135deg, #EF9F27 0%, #BA7517 100%); }
    .stat-card-v2.c-red    { background: linear-gradient(135deg, #E24B4A 0%, #A32D2D 100%); }
    .stat-card-v2::after { content: ''; position: absolute; right: -20px; top: -20px;
        width: 90px; height: 90px; border-radius: 50%; background: rgba(255,255,255,.08); }

    .stat-ico { width: 46px; height: 46px; border-radius: 12px;
        background: rgba(255,255,255,.2); display: flex; align-items: center;
        justify-content: center; flex-shrink: 0; }
    .stat-num { font-size: 28px; font-weight: 800; color: #fff; line-height: 1; }
    .stat-lbl { font-size: 13px; color: rgba(255,255,255,.8); margin-top: 3px; }

    /* ── Card ────────────────────────────────────────── */
    .pv-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; margin-bottom: 1.25rem; }
    .pv-card-head { display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; border-bottom: 1px solid #f3f4f6; }
    .pv-card-title { font-size: 14px; font-weight: 700; color: #111827; }
    .pv-card-body  { padding: 18px; }

    /* ── Form fields ─────────────────────────────────── */
    .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 14px; }
    @media (max-width: 480px) { .form-grid { grid-template-columns: 1fr; } }
    .form-grid .span-2 { grid-column: span 2; }
    @media (max-width: 480px) { .form-grid .span-2 { grid-column: span 1; } }
    .form-field { display: flex; flex-direction: column; }
    .form-field label { font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 5px;
        letter-spacing: .05em; text-transform: uppercase; }
    .form-field input,
    .form-field select,
    .form-field textarea { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0;
        border-radius: 8px; font-size: 13px; font-family: inherit; background: #fff;
        color: #111827; outline: none; transition: border-color .15s; box-sizing: border-box; }
    .form-field input:focus,
    .form-field select:focus,
    .form-field textarea:focus { border-color: #6366f1; }
    .form-field textarea { resize: vertical; }
    .poin-preview { display: none; margin-top: 6px; padding: 8px 12px; border-radius: 8px;
        font-size: 12px; font-weight: 700; }

    /* ── Drawer ──────────────────────────────────────── */
    .drawer-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 1000; }
    .drawer-overlay.open { display: block; }
    .drawer { position: fixed; top: 0; right: 0; bottom: 0; width: 460px; max-width: 100vw;
        background: #fff; z-index: 1001; display: flex; flex-direction: column;
        transform: translateX(110%); transition: transform .28s cubic-bezier(.4,0,.2,1);
        border-left: 1px solid #e5e7eb; }
    .drawer.open { transform: translateX(0); }
    .drawer-head { display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6; flex-shrink: 0;
        background: linear-gradient(135deg, #E24B4A 0%, #A32D2D 100%); }
    .drawer-head-left { display: flex; align-items: center; gap: 10px; }
    .drawer-head h3 { font-size: 15px; font-weight: 700; color: #fff; }
    .drawer-close { background: rgba(255,255,255,.2); border: none; color: #fff;
        width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
        border-radius: 8px; cursor: pointer; font-size: 18px; transition: background .15s; }
    .drawer-close:hover { background: rgba(255,255,255,.35); }
    .drawer-body { flex: 1; overflow-y: auto; padding: 20px; }
    .drawer-foot { padding: 14px 20px; border-top: 1px solid #f3f4f6; flex-shrink: 0; background: #fafafa; }

    /* Info siswa box */
    .siswa-info-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px;
        padding: 12px 14px; margin-bottom: 4px; display: none; }
    .siswa-info-box.visible { display: block; }
    .sib-title { font-size: 11px; font-weight: 700; color: #0369a1; text-transform: uppercase;
        letter-spacing: .05em; margin-bottom: 8px; }
    .sib-row { display: flex; justify-content: space-between; font-size: 12px;
        padding: 4px 0; border-bottom: 1px solid #e0f2fe; }
    .sib-row:last-child { border-bottom: none; }
    .sib-lbl { color: #6b7280; }
    .sib-val { font-weight: 700; color: #111827; }

    /* ── Tombol Catat ────────────────────────────────── */
    .btn-catat { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
        background: linear-gradient(135deg, #E24B4A, #A32D2D); color: #fff; border: none;
        border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;
        font-family: inherit; transition: opacity .15s; white-space: nowrap; }
    .btn-catat:hover { opacity: .88; }

    /* ── Filter bar ──────────────────────────────────── */
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px; align-items: end; }
    .filter-actions { display: flex; gap: 8px; align-items: flex-end; }

    /* ── Generic buttons ─────────────────────────────── */
    .btn-pv { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
        border-radius: 8px; border: none; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all .15s; font-family: inherit; text-decoration: none; }
    .btn-primary-pv { background: #3C3489; color: #fff; }
    .btn-primary-pv:hover { background: #26215C; color: #fff; }
    .btn-danger-pv  { background: linear-gradient(135deg, #E24B4A, #A32D2D); color: #fff; }
    .btn-danger-pv:hover { opacity: .88; }
    .btn-ghost { background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; }
    .btn-ghost:hover { background: #e2e8f0; }
    .btn-sm-ico { width: 32px; height: 32px; display: inline-flex; align-items: center;
        justify-content: center; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; cursor: pointer; font-size: 13px; transition: all .15s; color: #6b7280; }
    .btn-sm-ico.edit:hover { background: #FAEEDA; border-color: #FAC775; color: #854F0B; }
    .btn-sm-ico.del:hover  { background: #FCEBEB; border-color: #F09595; color: #A32D2D; }

    /* ── Table ───────────────────────────────────────── */
    .pv-table-wrap { overflow-x: auto; }
    .pv-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 640px; }
    .pv-table th { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase;
        letter-spacing: .05em; padding: 10px 16px; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6; text-align: left; white-space: nowrap; }
    .pv-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; color: #374151; vertical-align: middle; }
    .pv-table tr:last-child td { border-bottom: none; }
    .pv-table tr:hover td { background: #f8faff; }

    /* ── Badges ──────────────────────────────────────── */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 99px; white-space: nowrap; }
    .badge-ringan  { background: #fef3c7; color: #92400e; }
    .badge-sedang  { background: #fed7aa; color: #9a3412; }
    .badge-berat   { background: #fee2e2; color: #991b1b; }
    .badge-aktif   { background: #fee2e2; color: #991b1b; }
    .badge-selesai { background: #dcfce7; color: #166534; }
    .badge-batal   { background: #f1f5f9; color: #64748b; }
    .poin-num { font-size: 17px; font-weight: 800; color: #E24B4A; }

    /* ── Mobile card list ────────────────────────────── */
    .pv-card-list { display: none; }
    @media (max-width: 640px) {
        .pv-table-wrap { display: none; }
        .pv-card-list { display: block; }
    }
    .pv-list-item { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; }
    .pv-list-item:last-child { border-bottom: none; }
    .pv-list-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
    .pv-list-name { font-weight: 700; font-size: 13px; color: #111827; }
    .pv-list-sub  { font-size: 11px; color: #6b7280; margin-top: 1px; }
    .pv-list-bottom { display: flex; justify-content: space-between; align-items: center;
        margin-top: 8px; flex-wrap: wrap; gap: 6px; }

    /* ── Pagination ──────────────────────────────────── */
    .pv-pagination { padding: 14px 18px; border-top: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .pv-pagination .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; flex-wrap: wrap; }
    .pv-pagination .page-item .page-link { display: inline-flex; align-items: center; justify-content: center;
        min-width: 34px; height: 34px; padding: 0 8px; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; color: #374151; font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all .15s; }
    .pv-pagination .page-item .page-link:hover { background: #f1f5f9; }
    .pv-pagination .page-item.active .page-link { background: #3C3489; border-color: #3C3489; color: #fff; }
    .pv-pagination .page-item.disabled .page-link { color: #d1d5db; pointer-events: none; }
    .pv-pag-info { font-size: 12px; color: #6b7280; }

    /* ── Modal edit ──────────────────────────────────── */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45);
        z-index: 2000; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: #fff; border-radius: 14px; width: 100%; max-width: 420px; }
    .modal-head { display: flex; justify-content: space-between; align-items: center;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6; }
    .modal-head h3 { font-size: 15px; font-weight: 700; color: #111827; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; color: #9ca3af; }
    .modal-close:hover { color: #374151; }
    .modal-body { padding: 20px; display: flex; flex-direction: column; gap: 12px; }
    .modal-foot { display: flex; gap: 10px; justify-content: flex-end; padding: 0 20px 20px; }
    @media (max-width: 480px) {
        .modal-box { max-width: 100%; border-radius: 14px 14px 0 0; position: fixed; bottom: 0; left: 0; right: 0; }
        .modal-overlay { align-items: flex-end; padding: 0; }
    }

    .empty-state { text-align: center; padding: 3.5rem 1rem; }
    .empty-ico { width: 56px; height: 56px; background: #f3f4f6; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
</style>

<div class="pv-page">

    {{-- Header --}}
    <div class="pv-header">
        <div class="pv-header-left">
            <h1><i class="bi bi-exclamation-triangle-fill me-2" style="color:#E24B4A;font-size:20px;"></i>Pelanggaran Siswa</h1>
            <p>Pencatatan dan monitoring pelanggaran siswa</p>
        </div>
        <button class="btn-catat" onclick="openDrawer()">
            <i class="bi bi-plus-lg"></i> Catat Pelanggaran
        </button>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="pv-alert">
        <i class="bi bi-check-circle-fill" style="font-size:16px;flex-shrink:0;"></i>
        <span>{{ session('success') }}</span>
        <button class="pv-alert-close" onclick="this.closest('.pv-alert').remove()">&times;</button>
    </div>
    @endif

    {{-- Stat Cards --}}
    <div class="stat-grid">
        <div class="stat-card-v2 c-purple">
            <div class="stat-ico"><i class="bi bi-calendar-day" style="color:#fff;font-size:20px;"></i></div>
            <div>
                <div class="stat-num">{{ $totalHariIni }}</div>
                <div class="stat-lbl">Pelanggaran Hari Ini</div>
            </div>
        </div>
        <div class="stat-card-v2 c-amber">
            <div class="stat-ico"><i class="bi bi-calendar-month" style="color:#fff;font-size:20px;"></i></div>
            <div>
                <div class="stat-num">{{ $totalBulanIni }}</div>
                <div class="stat-lbl">Pelanggaran Bulan Ini</div>
            </div>
        </div>
        <div class="stat-card-v2 c-red">
            <div class="stat-ico"><i class="bi bi-bar-chart-fill" style="color:#fff;font-size:20px;"></i></div>
            <div>
                <div class="stat-num">{{ $totalSemester }}</div>
                <div class="stat-lbl">Total Semester Ini</div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="pv-card">
        <div class="pv-card-body">
            <form method="GET" action="{{ route('bk.pelanggaran.index') }}">
                <div class="filter-grid">
                    <div class="form-field">
                        <label>Semester</label>
                        <select name="semester_id">
                            <option value="">Semua</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->nama }}{{ $sem->is_aktif ? ' ✓' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Kategori</label>
                        <select name="kategori">
                            <option value="">Semua</option>
                            <option value="ringan" {{ request('kategori') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                            <option value="sedang" {{ request('kategori') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="berat"  {{ request('kategori') == 'berat'  ? 'selected' : '' }}>Berat</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Kelas</label>
                        <select name="rombel">
                            <option value="">Semua Kelas</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->rombongan_belajar_id }}" {{ request('rombel') == $r->rombongan_belajar_id ? 'selected' : '' }}>
                                    {{ $r->nama_rombel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Cari Siswa</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NISN...">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-pv btn-primary-pv">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('bk.pelanggaran.index') }}" class="btn-pv btn-ghost">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="pv-card">
        <div class="pv-card-head">
            <span class="pv-card-title">
                <i class="bi bi-list-check me-2" style="color:#6366f1;"></i>Riwayat Pelanggaran
            </span>
            <span style="font-size:12px;color:#6b7280;background:#f3f4f6;padding:3px 12px;border-radius:99px;font-weight:600;">
                {{ $pelanggaran->total() }} data
            </span>
        </div>

        {{-- Desktop table --}}
        <div class="pv-table-wrap">
            <table class="pv-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Siswa</th>
                        <th>Pelanggaran</th>
                        <th style="width:60px;">Poin</th>
                        <th style="width:100px;">Tanggal</th>
                        <th>Oleh</th>
                        <th style="width:90px;">Status</th>
                        <th style="width:80px;text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggaran as $i => $p)
                    @php
                        $kat = $p->jenisPelanggaran?->kategori;
                        $badgeStatus = match($p->status) {
                            'aktif'      => 'badge-aktif',
                            'selesai'    => 'badge-selesai',
                            'dibatalkan' => 'badge-batal',
                            default      => '',
                        };
                    @endphp
                    <tr>
                        <td style="color:#9ca3af;font-size:12px;">{{ $pelanggaran->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:700;font-size:13px;color:#111827;">{{ $p->siswa?->nama }}</div>
                            <div style="font-size:11px;color:#9ca3af;margin-top:1px;">{{ $p->siswa?->nama_rombel }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px;margin-bottom:4px;">{{ $p->jenisPelanggaran?->nama }}</div>
                            <span class="badge badge-{{ $kat }}">{{ $p->jenisPelanggaran?->kategoriLabel() }}</span>
                        </td>
                        <td><span class="poin-num">{{ $p->poin }}</span></td>
                        <td style="font-size:12px;color:#6b7280;white-space:nowrap;">{{ $p->tanggal?->format('d/m/Y') }}</td>
                        <td style="font-size:12px;color:#6b7280;">{{ $p->dicatatOleh?->name }}</td>
                        <td><span class="badge {{ $badgeStatus }}">{{ ucfirst($p->status) }}</span></td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <button class="btn-sm-ico edit" title="Edit"
                                    onclick="editPelanggaran({{ $p->id }}, '{{ $p->status }}', '{{ addslashes($p->tindakan ?? '') }}', '{{ addslashes($p->keterangan ?? '') }}')">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form method="POST" action="{{ route('bk.pelanggaran.destroy', $p) }}"
                                    onsubmit="return confirm('Batalkan pelanggaran ini?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-sm-ico del" title="Hapus">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-ico"><i class="bi bi-clipboard-x" style="font-size:24px;color:#9ca3af;"></i></div>
                                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada data pelanggaran</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list --}}
        <div class="pv-card-list">
            @forelse($pelanggaran as $p)
            @php
                $kat = $p->jenisPelanggaran?->kategori;
                $badgeStatus = match($p->status) { 'aktif' => 'badge-aktif', 'selesai' => 'badge-selesai', 'dibatalkan' => 'badge-batal', default => '' };
            @endphp
            <div class="pv-list-item">
                <div class="pv-list-top">
                    <div>
                        <div class="pv-list-name">{{ $p->siswa?->nama }}</div>
                        <div class="pv-list-sub">{{ $p->siswa?->nama_rombel }} &bull; {{ $p->tanggal?->format('d/m/Y') }}</div>
                    </div>
                    <span class="poin-num" style="font-size:22px;flex-shrink:0;">{{ $p->poin }}</span>
                </div>
                <div class="pv-list-bottom">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">
                            {{ $p->jenisPelanggaran?->nama }}
                        </div>
                        <span class="badge badge-{{ $kat }}">{{ $p->jenisPelanggaran?->kategoriLabel() }}</span>
                        <span class="badge {{ $badgeStatus }}" style="margin-left:4px;">{{ ucfirst($p->status) }}</span>
                    </div>
                    <div style="display:flex;gap:6px;">
                        <button class="btn-sm-ico edit" title="Edit"
                            onclick="editPelanggaran({{ $p->id }}, '{{ $p->status }}', '{{ addslashes($p->tindakan ?? '') }}', '{{ addslashes($p->keterangan ?? '') }}')">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <form method="POST" action="{{ route('bk.pelanggaran.destroy', $p) }}"
                            onsubmit="return confirm('Batalkan pelanggaran ini?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm-ico del">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-ico"><i class="bi bi-clipboard-x" style="font-size:24px;color:#9ca3af;"></i></div>
                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada data pelanggaran</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($pelanggaran->hasPages())
        <div class="pv-pagination">
            <span class="pv-pag-info">
                Menampilkan {{ $pelanggaran->firstItem() }}–{{ $pelanggaran->lastItem() }}
                dari {{ $pelanggaran->total() }} data
            </span>
            {{ $pelanggaran->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>

{{-- ══════════════════════════════════════
     DRAWER — Catat Pelanggaran
══════════════════════════════════════ --}}
<div class="drawer-overlay" id="drawer-overlay" onclick="closeDrawer()"></div>

<div class="drawer" id="drawer-catat">
    <div class="drawer-head">
        <div class="drawer-head-left">
            <i class="bi bi-pencil-fill" style="color:#fff;font-size:16px;"></i>
            <h3>Catat Pelanggaran</h3>
        </div>
        <button class="drawer-close" onclick="closeDrawer()">&times;</button>
    </div>

    <div class="drawer-body">
        <form method="POST" action="{{ route('bk.pelanggaran.store') }}" id="form-pelanggaran">
            @csrf
            <div class="form-grid">

                <div class="form-field">
                    <label>Semester <span style="color:#ef4444">*</span></label>
                    <select name="semester_id" required>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $semesterAktif?->id == $sem->id ? 'selected' : '' }}>
                                {{ $sem->nama }}{{ $sem->is_aktif ? ' (Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label>Filter Kelas</label>
                    <select id="filter-rombel" onchange="filterSiswa()">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->rombongan_belajar_id }}">{{ $r->nama_rombel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field span-2">
                    <label>Siswa <span style="color:#ef4444">*</span></label>
                    <select name="siswa_id" id="select-siswa" required onchange="loadInfoSiswa(this.value)">
                        <option value="">Pilih siswa...</option>
                    </select>
                </div>

                <div class="span-2">
                    <div class="siswa-info-box" id="siswa-info-box">
                        <div class="sib-title"><i class="bi bi-person-fill me-1"></i>Info Siswa</div>
                        <div id="siswa-info-content"></div>
                    </div>
                </div>

                <div class="form-field span-2">
                    <label>Jenis Pelanggaran <span style="color:#ef4444">*</span></label>
                    <select name="jenis_pelanggaran_id" id="select-jenis" required onchange="updatePoin()">
                        <option value="">Pilih jenis pelanggaran...</option>
                        @foreach($jenisList as $j)
                            <option value="{{ $j->id }}" data-poin="{{ $j->poin }}" data-kategori="{{ $j->kategori }}">
                                [{{ strtoupper($j->kategori) }}] {{ $j->nama }} ({{ $j->poin }} poin)
                            </option>
                        @endforeach
                    </select>
                    <div id="poin-preview" class="poin-preview"></div>
                </div>

                <div class="form-field">
                    <label>Tanggal <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}">
                </div>

                <div class="form-field">
                    <label>Tindakan</label>
                    <input type="text" name="tindakan" placeholder="Tindakan yang diambil...">
                </div>

                <div class="form-field span-2">
                    <label>Keterangan</label>
                    <textarea name="keterangan" rows="3" placeholder="Keterangan kejadian..."></textarea>
                </div>

            </div>
        </form>
    </div>

    <div class="drawer-foot">
        <div style="display:flex;gap:10px;">
            <button type="button" class="btn-pv btn-ghost" onclick="closeDrawer()" style="flex:1;">
                Batal
            </button>
            <button type="submit" form="form-pelanggaran" class="btn-pv btn-danger-pv" style="flex:2;">
                <i class="bi bi-exclamation-triangle-fill"></i> Simpan Pelanggaran
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     MODAL — Edit Pelanggaran
══════════════════════════════════════ --}}
<div id="modal-edit" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="bi bi-pencil-fill me-2" style="color:#6366f1;"></i>Update Pelanggaran</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" id="form-edit-pelanggaran">
                @csrf @method('PUT')
                <div class="form-field" style="margin-bottom:12px;">
                    <label>Status</label>
                    <select name="status" id="edit-status">
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div class="form-field" style="margin-bottom:12px;">
                    <label>Tindakan</label>
                    <input type="text" name="tindakan" id="edit-tindakan" placeholder="Tindakan yang diambil...">
                </div>
                <div class="form-field">
                    <label>Keterangan</label>
                    <textarea name="keterangan" id="edit-keterangan" rows="2" placeholder="Keterangan..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn-pv btn-ghost" onclick="closeModal()">Batal</button>
            <button type="submit" form="form-edit-pelanggaran" class="btn-pv btn-primary-pv">
                <i class="bi bi-check-lg"></i> Simpan
            </button>
        </div>
    </div>
</div>

<script>
const siswaData = @json($siswaList);

function openDrawer() {
    document.getElementById('drawer-overlay').classList.add('open');
    document.getElementById('drawer-catat').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeDrawer() {
    document.getElementById('drawer-overlay').classList.remove('open');
    document.getElementById('drawer-catat').classList.remove('open');
    document.body.style.overflow = '';
}

function filterSiswa() {
    const rombelId = document.getElementById('filter-rombel').value;
    const select   = document.getElementById('select-siswa');
    select.innerHTML = '<option value="">Pilih siswa...</option>';
    siswaData
        .filter(s => !rombelId || s.rombongan_belajar_id === rombelId)
        .forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.nama + (s.nisn ? ' (' + s.nisn + ')' : '');
            select.appendChild(opt);
        });
    document.getElementById('siswa-info-box').classList.remove('visible');
}

function loadInfoSiswa(id) {
    const box = document.getElementById('siswa-info-box');
    if (!id) { box.classList.remove('visible'); return; }
    const s = siswaData.find(x => x.id == id);
    if (!s)  { box.classList.remove('visible'); return; }
    document.getElementById('siswa-info-content').innerHTML =
        mkRow('Nama',  s.nama) +
        mkRow('NISN',  s.nisn || '-') +
        mkRow('Kelas', s.nama_rombel || '-') +
        (s.total_poin !== undefined
            ? mkRow('Total Poin', '<span style="color:#E24B4A;font-weight:800;">' + s.total_poin + ' poin</span>')
            : '');
    box.classList.add('visible');
}
function mkRow(lbl, val) {
    return '<div class="sib-row"><span class="sib-lbl">' + lbl + '</span><span class="sib-val">' + val + '</span></div>';
}

function updatePoin() {
    const sel  = document.getElementById('select-jenis');
    const opt  = sel.options[sel.selectedIndex];
    const prev = document.getElementById('poin-preview');
    if (opt.value) {
        const styles = {
            ringan: 'background:#fef3c7;color:#92400e;',
            sedang: 'background:#fed7aa;color:#9a3412;',
            berat:  'background:#fee2e2;color:#991b1b;'
        };
        prev.style.cssText = 'display:block;margin-top:6px;padding:8px 12px;border-radius:8px;font-size:12px;font-weight:700;'
            + (styles[opt.dataset.kategori] || 'background:#f1f5f9;color:#374151;');
        prev.textContent = 'Poin yang akan ditambahkan: ' + opt.dataset.poin + ' poin';
    } else {
        prev.style.display = 'none';
    }
}

function editPelanggaran(id, status, tindakan, keterangan) {
    document.getElementById('edit-status').value     = status;
    document.getElementById('edit-tindakan').value   = tindakan;
    document.getElementById('edit-keterangan').value = keterangan;
    document.getElementById('form-edit-pelanggaran').action = '/bk/pelanggaran/' + id;
    document.getElementById('modal-edit').classList.add('open');
}
function closeModal() {
    document.getElementById('modal-edit').classList.remove('open');
}
document.getElementById('modal-edit').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeDrawer(); closeModal(); }
});

filterSiswa();
</script>
@endsection