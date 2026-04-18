@extends('layouts.app')

@section('page-title', 'Jenis Pelanggaran')
@section('page-subtitle', 'Master data jenis pelanggaran dan poin')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<style>
    .jp-page { width: 100%; padding: 1.5rem 2.5rem; }
    @media (max-width: 768px) { .jp-page { padding: 1rem; } }

    /* Header */
    .jp-header { display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .jp-header-left h1 { font-size: 22px; font-weight: 700; color: #111827; }
    .jp-header-left p  { font-size: 13px; color: #6b7280; margin-top: 3px; }
    @media (max-width: 480px) { .jp-header { flex-direction: column; align-items: flex-start; } }

    /* Alert */
    .jp-alert { display: flex; align-items: center; gap: 10px; padding: 13px 16px;
        border-radius: 10px; border: 1px solid #bbf7d0; background: #f0fdf4;
        color: #166534; font-size: 13px; font-weight: 600; margin-bottom: 1.25rem; }
    .jp-alert-close { margin-left: auto; background: none; border: none; font-size: 18px; cursor: pointer; color: inherit; opacity: .6; }
    .jp-alert-close:hover { opacity: 1; }

    /* Error alert */
    .jp-error { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px;
        padding: 12px 16px; margin-bottom: 1rem; font-size: 13px; color: #991b1b; }
    .jp-error p { margin: 2px 0; }

    /* Stat cards */
    .jp-stat-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 14px; margin-bottom: 1.5rem; }
    @media (max-width: 600px) { .jp-stat-grid { grid-template-columns: 1fr; gap: 10px; } }
    .jp-stat { border-radius: 14px; padding: 18px 20px; display: flex; align-items: center;
        gap: 14px; position: relative; overflow: hidden; }
    .jp-stat.c-amber  { background: linear-gradient(135deg, #EF9F27 0%, #854F0B 100%); }
    .jp-stat.c-orange { background: linear-gradient(135deg, #f97316 0%, #c2410c 100%); }
    .jp-stat.c-red    { background: linear-gradient(135deg, #E24B4A 0%, #A32D2D 100%); }
    .jp-stat::after { content:''; position:absolute; right:-16px; top:-16px; width:80px; height:80px;
        border-radius:50%; background:rgba(255,255,255,.08); }
    .jp-stat-ico { width: 42px; height: 42px; border-radius: 11px; background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .jp-stat-num { font-size: 26px; font-weight: 800; color: #fff; line-height: 1; }
    .jp-stat-lbl { font-size: 12px; color: rgba(255,255,255,.8); margin-top: 3px; }

    /* Card */
    .jp-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
    .jp-card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid #f3f4f6; }
    .jp-card-title { font-size: 14px; font-weight: 700; color: #111827; }
    .jp-card-body  { padding: 18px; }

    /* Filter bar */
    .filter-bar { display: flex; gap: 10px; padding: 12px 18px; border-bottom: 1px solid #f3f4f6; flex-wrap: wrap; }
    .filter-bar select,
    .filter-bar input { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; font-family: inherit; background: #fff; color: #111827; outline: none; }
    .filter-bar select { min-width: 140px; }
    .filter-bar input  { flex: 1; min-width: 120px; }

    /* Form fields */
    .form-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px; }
    @media (max-width: 480px) { .form-grid-2 { grid-template-columns: 1fr; } }
    .form-field { display: flex; flex-direction: column; margin-bottom: 14px; }
    .form-field label { font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 5px; letter-spacing: .05em; text-transform: uppercase; }
    .form-field input,
    .form-field select,
    .form-field textarea { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; font-family: inherit; background: #fff; color: #111827;
        outline: none; transition: border-color .15s; box-sizing: border-box; }
    .form-field input:focus,
    .form-field select:focus,
    .form-field textarea:focus { border-color: #ef4444; }
    .form-field textarea { resize: vertical; }

    /* Drawer */
    .drawer-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 1000; }
    .drawer-overlay.open { display: block; }
    .drawer { position: fixed; top: 0; right: 0; bottom: 0; width: 440px; max-width: 100vw;
        background: #fff; z-index: 1001; display: flex; flex-direction: column;
        transform: translateX(110%); transition: transform .28s cubic-bezier(.4,0,.2,1);
        border-left: 1px solid #e5e7eb; }
    .drawer.open { transform: translateX(0); }
    .drawer-head { display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6; flex-shrink: 0; }
    .drawer-head.add-mode  { background: linear-gradient(135deg, #E24B4A, #A32D2D); }
    .drawer-head.edit-mode { background: linear-gradient(135deg, #534AB7, #3C3489); }
    .drawer-head-left { display: flex; align-items: center; gap: 10px; }
    .drawer-head h3 { font-size: 15px; font-weight: 700; color: #fff; }
    .drawer-close { background: rgba(255,255,255,.2); border: none; color: #fff;
        width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
        border-radius: 8px; cursor: pointer; font-size: 18px; transition: background .15s; }
    .drawer-close:hover { background: rgba(255,255,255,.35); }
    .drawer-body { flex: 1; overflow-y: auto; padding: 20px; }
    .drawer-foot { padding: 14px 20px; border-top: 1px solid #f3f4f6; flex-shrink: 0; background: #fafafa; }

    /* Buttons */
    .btn-add { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
        background: linear-gradient(135deg, #E24B4A, #A32D2D); color: #fff; border: none;
        border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;
        font-family: inherit; transition: opacity .15s; white-space: nowrap; }
    .btn-add:hover { opacity: .88; }
    .btn-pv { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px;
        border-radius: 8px; border: none; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all .15s; font-family: inherit; text-decoration: none; }
    .btn-primary-pv { background: #3C3489; color: #fff; }
    .btn-primary-pv:hover { background: #26215C; color: #fff; }
    .btn-ghost { background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; }
    .btn-ghost:hover { background: #e2e8f0; }
    .btn-submit-red    { width:100%; padding:10px; background:linear-gradient(135deg,#E24B4A,#A32D2D); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; }
    .btn-submit-purple { width:100%; padding:10px; background:linear-gradient(135deg,#534AB7,#3C3489); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; }
    .btn-submit-red:hover, .btn-submit-purple:hover { opacity: .88; }
    .btn-sm-ico { width: 32px; height: 32px; display: inline-flex; align-items: center;
        justify-content: center; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; cursor: pointer; font-size: 13px; transition: all .15s; color: #6b7280; }
    .btn-sm-ico.edit-btn:hover { background: #FAEEDA; border-color: #FAC775; color: #854F0B; }
    .btn-sm-ico.del-btn:hover  { background: #FCEBEB; border-color: #F09595; color: #A32D2D; }

    /* Table */
    .jp-table-wrap { overflow-x: auto; }
    .jp-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 480px; }
    .jp-table th { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase;
        letter-spacing: .05em; padding: 10px 16px; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6; text-align: left; white-space: nowrap; }
    .jp-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .jp-table tr:last-child td { border-bottom: none; }
    .jp-table tr:hover td { background: #fff8f8; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 99px; white-space: nowrap; }
    .badge-ringan { background: #fef3c7; color: #92400e; }
    .badge-sedang { background: #fed7aa; color: #9a3412; }
    .badge-berat  { background: #fee2e2; color: #991b1b; }
    .poin-num { font-size: 16px; font-weight: 800; color: #E24B4A; }

    /* Mobile card list */
    .jp-card-list { display: none; }
    @media (max-width: 600px) {
        .jp-table-wrap { display: none; }
        .jp-card-list  { display: block; }
    }
    .jp-list-item { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; }
    .jp-list-item:last-child { border-bottom: none; }
    .jp-list-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
    .jp-list-name { font-weight: 700; font-size: 13px; color: #111827; }
    .jp-list-code { font-family: monospace; font-size: 11px; color: #6b7280; margin-top: 2px; }
    .jp-list-bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; flex-wrap: wrap; gap: 6px; }

    /* Pagination */
    .jp-pagination { padding: 14px 18px; border-top: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .jp-pagination .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; flex-wrap: wrap; }
    .jp-pagination .page-item .page-link { display: inline-flex; align-items: center; justify-content: center;
        min-width: 34px; height: 34px; padding: 0 8px; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; color: #374151; font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all .15s; }
    .jp-pagination .page-item .page-link:hover { background: #fff8f8; }
    .jp-pagination .page-item.active .page-link { background: #A32D2D; border-color: #A32D2D; color: #fff; }
    .jp-pagination .page-item.disabled .page-link { color: #d1d5db; pointer-events: none; }
    .jp-pag-info { font-size: 12px; color: #6b7280; }

    .empty-state { text-align: center; padding: 3rem 1rem; }
    .empty-ico { width: 52px; height: 52px; background: #f3f4f6; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
</style>

<div class="jp-page">

    {{-- Header --}}
    <div class="jp-header">
        <div class="jp-header-left">
            <h1><i class="bi bi-exclamation-triangle-fill me-2" style="color:#E24B4A;font-size:20px;"></i>Jenis Pelanggaran</h1>
            <p>Master data jenis pelanggaran dan poin</p>
        </div>
        <button class="btn-add" onclick="openDrawer('add')">
            <i class="bi bi-plus-lg"></i> Tambah Jenis
        </button>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="jp-alert">
        <i class="bi bi-check-circle-fill" style="font-size:16px;flex-shrink:0;"></i>
        <span>{{ session('success') }}</span>
        <button class="jp-alert-close" onclick="this.closest('.jp-alert').remove()">&times;</button>
    </div>
    @endif

    {{-- Stat Cards --}}
    <div class="jp-stat-grid">
        <div class="jp-stat c-amber">
            <div class="jp-stat-ico"><i class="bi bi-circle-fill" style="color:#fff;font-size:16px;"></i></div>
            <div>
                <div class="jp-stat-num">{{ $totalRingan }}</div>
                <div class="jp-stat-lbl">Pelanggaran Ringan</div>
            </div>
        </div>
        <div class="jp-stat c-orange">
            <div class="jp-stat-ico"><i class="bi bi-exclamation-circle-fill" style="color:#fff;font-size:16px;"></i></div>
            <div>
                <div class="jp-stat-num">{{ $totalSedang }}</div>
                <div class="jp-stat-lbl">Pelanggaran Sedang</div>
            </div>
        </div>
        <div class="jp-stat c-red">
            <div class="jp-stat-ico"><i class="bi bi-x-octagon-fill" style="color:#fff;font-size:16px;"></i></div>
            <div>
                <div class="jp-stat-num">{{ $totalBerat }}</div>
                <div class="jp-stat-lbl">Pelanggaran Berat</div>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="jp-card">
        <div class="jp-card-head">
            <span class="jp-card-title">
                <i class="bi bi-list-check me-2" style="color:#E24B4A;"></i>Daftar Jenis Pelanggaran
            </span>
            <span style="font-size:12px;color:#6b7280;background:#f3f4f6;padding:3px 12px;border-radius:99px;font-weight:600;">
                {{ $jenis->total() }} data
            </span>
        </div>

        {{-- Filter --}}
        <form method="GET">
            <div class="filter-bar">
                <select name="kategori" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <option value="ringan" {{ request('kategori') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                    <option value="sedang" {{ request('kategori') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="berat"  {{ request('kategori') == 'berat'  ? 'selected' : '' }}>Berat</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode...">
                <button type="submit" class="btn-pv btn-primary-pv">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>

        {{-- Desktop table --}}
        <div class="jp-table-wrap">
            <table class="jp-table">
                <thead>
                    <tr>
                        <th style="width:80px;">Kode</th>
                        <th>Nama Pelanggaran</th>
                        <th style="width:100px;">Kategori</th>
                        <th style="width:80px;">Poin</th>
                        <th style="width:80px;text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenis as $j)
                    <tr>
                        <td>
                            <span style="font-family:monospace;font-weight:700;font-size:12px;background:#f3f4f6;padding:3px 8px;border-radius:6px;color:#374151;">
                                {{ $j->kode }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:700;font-size:13px;color:#111827;">{{ $j->nama }}</div>
                            @if($j->deskripsi)
                            <div style="font-size:11px;color:#9ca3af;margin-top:1px;">{{ $j->deskripsi }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $j->kategori }}">{{ $j->kategoriLabel() }}</span>
                        </td>
                        <td>
                            <span class="poin-num">{{ $j->poin }}</span>
                            <span style="font-size:11px;color:#9ca3af;">poin</span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <button class="btn-sm-ico edit-btn" title="Edit"
                                    onclick="openDrawer('edit', {{ $j->id }}, '{{ $j->kode }}', '{{ addslashes($j->nama) }}', '{{ $j->kategori }}', {{ $j->poin }}, '{{ addslashes($j->deskripsi ?? '') }}')">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form method="POST" action="{{ route('bk.jenis-pelanggaran.destroy', $j) }}"
                                    onsubmit="return confirm('Hapus jenis pelanggaran ini?')" style="display:inline;" id="del-{{ $j->id }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-sm-ico del-btn" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-ico"><i class="bi bi-clipboard-x" style="font-size:22px;color:#9ca3af;"></i></div>
                                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada jenis pelanggaran</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list --}}
        <div class="jp-card-list">
            @forelse($jenis as $j)
            <div class="jp-list-item">
                <div class="jp-list-top">
                    <div>
                        <div class="jp-list-name">{{ $j->nama }}</div>
                        <div class="jp-list-code">{{ $j->kode }}{{ $j->deskripsi ? ' · ' . $j->deskripsi : '' }}</div>
                    </div>
                    <span class="poin-num">{{ $j->poin }}</span>
                </div>
                <div class="jp-list-bottom">
                    <span class="badge badge-{{ $j->kategori }}">{{ $j->kategoriLabel() }}</span>
                    <div style="display:flex;gap:6px;">
                        <button class="btn-sm-ico edit-btn" title="Edit"
                            onclick="openDrawer('edit', {{ $j->id }}, '{{ $j->kode }}', '{{ addslashes($j->nama) }}', '{{ $j->kategori }}', {{ $j->poin }}, '{{ addslashes($j->deskripsi ?? '') }}')">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <form method="POST" action="{{ route('bk.jenis-pelanggaran.destroy', $j) }}"
                            onsubmit="return confirm('Hapus jenis pelanggaran ini?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm-ico del-btn" title="Hapus">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-ico"><i class="bi bi-clipboard-x" style="font-size:22px;color:#9ca3af;"></i></div>
                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada jenis pelanggaran</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($jenis->hasPages())
        <div class="jp-pagination">
            <span class="jp-pag-info">
                Menampilkan {{ $jenis->firstItem() }}–{{ $jenis->lastItem() }} dari {{ $jenis->total() }} data
            </span>
            {{ $jenis->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>

{{-- ══════════════════════════════════════
     DRAWER — Tambah / Edit
══════════════════════════════════════ --}}
<div class="drawer-overlay" id="drawer-overlay" onclick="closeDrawer()"></div>

<div class="drawer" id="drawer-form">
    <div class="drawer-head add-mode" id="drawer-head">
        <div class="drawer-head-left">
            <i class="bi bi-plus-circle" id="drawer-ico" style="color:#fff;font-size:16px;"></i>
            <h3 id="drawer-title">Tambah Jenis Pelanggaran</h3>
        </div>
        <button class="drawer-close" onclick="closeDrawer()">&times;</button>
    </div>

    <div class="drawer-body">

        @if($errors->any())
        <div class="jp-error">
            @foreach($errors->all() as $e)<p>&bull; {{ $e }}</p>@endforeach
        </div>
        @endif

        <form method="POST" id="jenis-form" action="{{ route('bk.jenis-pelanggaran.store') }}">
            @csrf
            <div id="method-field"></div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label>Kode <span style="color:#ef4444">*</span></label>
                    <input type="text" name="kode" id="fKode" value="{{ old('kode') }}" placeholder="P001" required>
                </div>
                <div class="form-field">
                    <label>Kategori <span style="color:#ef4444">*</span></label>
                    <select name="kategori" id="fKategori" required>
                        <option value="ringan" {{ old('kategori') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                        <option value="sedang" {{ old('kategori') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="berat"  {{ old('kategori') == 'berat'  ? 'selected' : '' }}>Berat</option>
                    </select>
                </div>
            </div>

            <div class="form-field">
                <label>Nama Pelanggaran <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" id="fNama" value="{{ old('nama') }}"
                    placeholder="Contoh: Terlambat masuk kelas" required>
            </div>

            <div class="form-field">
                <label>Poin <span style="color:#ef4444">*</span></label>
                <input type="number" name="poin" id="fPoin" value="{{ old('poin') }}" min="1" placeholder="5" required>
            </div>

            <div class="form-field">
                <label>Deskripsi</label>
                <textarea name="deskripsi" id="fDeskripsi" rows="3"
                    placeholder="Keterangan tambahan...">{{ old('deskripsi') }}</textarea>
            </div>

        </form>
    </div>

    <div class="drawer-foot">
        <div style="display:flex;gap:10px;">
            <button type="button" class="btn-pv btn-ghost" onclick="closeDrawer()" style="flex:1;">
                Batal
            </button>
            <button type="submit" form="jenis-form" id="btn-submit" class="btn-submit-red" style="flex:2;">
                <i class="bi bi-check-lg me-1"></i><span id="btn-text">Tambah Jenis</span>
            </button>
        </div>
    </div>
</div>

<script>
const baseRoute = "{{ route('bk.jenis-pelanggaran.store') }}";

function openDrawer(mode, id, kode, nama, kategori, poin, deskripsi) {
    const head   = document.getElementById('drawer-head');
    const ico    = document.getElementById('drawer-ico');
    const title  = document.getElementById('drawer-title');
    const submit = document.getElementById('btn-submit');
    const btnTxt = document.getElementById('btn-text');
    const form   = document.getElementById('jenis-form');

    if (mode === 'edit') {
        head.className  = 'drawer-head edit-mode';
        ico.className   = 'bi bi-pencil-fill';
        title.textContent = 'Edit Jenis Pelanggaran';
        btnTxt.textContent = 'Simpan Perubahan';
        submit.className = 'btn-submit-purple';

        document.getElementById('fKode').value     = kode;
        document.getElementById('fNama').value     = nama;
        document.getElementById('fKategori').value = kategori;
        document.getElementById('fPoin').value     = poin;
        document.getElementById('fDeskripsi').value= deskripsi;

        form.action = `/bk/jenis-pelanggaran/${id}`;
        document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    } else {
        head.className  = 'drawer-head add-mode';
        ico.className   = 'bi bi-plus-circle';
        title.textContent = 'Tambah Jenis Pelanggaran';
        btnTxt.textContent = 'Tambah Jenis';
        submit.className = 'btn-submit-red';

        form.reset();
        form.action = baseRoute;
        document.getElementById('method-field').innerHTML = '';
    }

    document.getElementById('drawer-overlay').classList.add('open');
    document.getElementById('drawer-form').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeDrawer() {
    document.getElementById('drawer-overlay').classList.remove('open');
    document.getElementById('drawer-form').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeDrawer();
});

// Buka drawer otomatis kalau ada error validasi
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openDrawer('add'));
@endif
</script>
@endsection