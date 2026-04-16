@extends('layouts.app')
@section('page-title', 'Master Jenis Kegiatan Positif')
@section('sidebar-menu') @include('partials.sidebar_bk') @endsection

@section('content')
<style>
    .mk-page { width: 100%; padding: 1.5rem 2.5rem; }
    @media (max-width: 768px) { .mk-page { padding: 1rem; } }

    /* Header */
    .mk-header { display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .mk-header-left h1 { font-size: 22px; font-weight: 700; color: #111827; }
    .mk-header-left p  { font-size: 13px; color: #6b7280; margin-top: 3px; }
    @media (max-width: 480px) { .mk-header { flex-direction: column; align-items: flex-start; } }

    /* Alert */
    .mk-alert { display: flex; align-items: center; gap: 10px; padding: 13px 16px;
        border-radius: 10px; border: 1px solid #bbf7d0; background: #f0fdf4;
        color: #166534; font-size: 13px; font-weight: 600; margin-bottom: 1.25rem; }
    .mk-alert-close { margin-left: auto; background: none; border: none; font-size: 18px; cursor: pointer; color: inherit; opacity: .6; }
    .mk-alert-close:hover { opacity: 1; }

    /* Stat cards */
    .mk-stat-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 14px; margin-bottom: 1.5rem; }
    @media (max-width: 600px) { .mk-stat-grid { grid-template-columns: 1fr; gap: 10px; } }
    .mk-stat { border-radius: 14px; padding: 18px 20px; display: flex; align-items: center;
        gap: 14px; position: relative; overflow: hidden; }
    .mk-stat.c-green  { background: linear-gradient(135deg, #1D9E75 0%, #0F6E56 100%); }
    .mk-stat.c-teal   { background: linear-gradient(135deg, #1D9E75 0%, #085041 100%); }
    .mk-stat.c-purple { background: linear-gradient(135deg, #534AB7 0%, #3C3489 100%); }
    .mk-stat::after { content:''; position:absolute; right:-20px; top:-20px; width:90px; height:90px;
        border-radius:50%; background:rgba(255,255,255,.08); }
    .mk-stat-ico { width: 46px; height: 46px; border-radius: 12px; background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .mk-stat-num { font-size: 28px; font-weight: 800; color: #fff; line-height: 1; }
    .mk-stat-lbl { font-size: 13px; color: rgba(255,255,255,.8); margin-top: 3px; }

    /* Card */
    .mk-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
    .mk-card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid #f3f4f6; }
    .mk-card-title { font-size: 14px; font-weight: 700; color: #111827; }
    .mk-card-body  { padding: 18px; }

    /* Form fields */
    .form-field { display: flex; flex-direction: column; margin-bottom: 14px; }
    .form-field label { font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 5px; letter-spacing: .05em; text-transform: uppercase; }
    .form-field input,
    .form-field select,
    .form-field textarea { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; font-family: inherit; background: #fff; color: #111827;
        outline: none; transition: border-color .15s; box-sizing: border-box; }
    .form-field input:focus,
    .form-field select:focus,
    .form-field textarea:focus { border-color: #16a34a; }
    .form-field textarea { resize: vertical; }
    .form-hint { font-size: 11px; color: #9ca3af; margin-top: 3px; }

    /* Drawer */
    .drawer-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 1000; }
    .drawer-overlay.open { display: block; }
    .drawer { position: fixed; top: 0; right: 0; bottom: 0; width: 420px; max-width: 100vw;
        background: #fff; z-index: 1001; display: flex; flex-direction: column;
        transform: translateX(110%); transition: transform .28s cubic-bezier(.4,0,.2,1);
        border-left: 1px solid #e5e7eb; }
    .drawer.open { transform: translateX(0); }
    .drawer-head { display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6; flex-shrink: 0; }
    .drawer-head.add-mode { background: linear-gradient(135deg, #1D9E75, #0F6E56); }
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
        background: linear-gradient(135deg, #1D9E75, #0F6E56); color: #fff; border: none;
        border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;
        font-family: inherit; transition: opacity .15s; white-space: nowrap; }
    .btn-add:hover { opacity: .88; }
    .btn-back { display: inline-flex; align-items: center; gap: 6px; padding: 9px 14px;
        background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; border-radius: 9px;
        font-size: 13px; font-weight: 600; text-decoration: none; transition: background .15s; }
    .btn-back:hover { background: #e2e8f0; color: #374151; }
    .btn-submit-green { width: 100%; padding: 10px; background: linear-gradient(135deg, #1D9E75, #0F6E56);
        color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 700;
        cursor: pointer; font-family: inherit; transition: opacity .15s; }
    .btn-submit-green:hover { opacity: .88; }
    .btn-submit-purple { width: 100%; padding: 10px; background: linear-gradient(135deg, #534AB7, #3C3489);
        color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 700;
        cursor: pointer; font-family: inherit; transition: opacity .15s; }
    .btn-submit-purple:hover { opacity: .88; }
    .btn-ghost { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
        background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; border-radius: 8px;
        font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; transition: background .15s; }
    .btn-ghost:hover { background: #e2e8f0; }
    .btn-sm-ico { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer;
        font-size: 13px; transition: all .15s; color: #6b7280; }
    .btn-sm-ico.edit-btn:hover  { background: #ede9fe; border-color: #a5b4fc; color: #4f46e5; }
    .btn-sm-ico.del-btn:hover   { background: #FCEBEB; border-color: #F09595; color: #A32D2D; }

    /* Table */
    .mk-table-wrap { overflow-x: auto; }
    .mk-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 600px; }
    .mk-table th { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase;
        letter-spacing: .05em; padding: 10px 16px; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6; text-align: left; white-space: nowrap; }
    .mk-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .mk-table tr:last-child td { border-bottom: none; }
    .mk-table tr:hover td { background: #f8fff8; }
    .mk-table tr.inactive td { opacity: .5; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 99px; white-space: nowrap; }
    .badge-aktif    { background: #dcfce7; color: #166534; }
    .badge-nonaktif { background: #f1f5f9; color: #64748b; }
    .badge-kat      { background: #f0fdf4; color: #15803d; }
    .poin-badge { background: #dcfce7; color: #15803d; font-weight: 800;
        padding: 3px 10px; border-radius: 99px; font-size: 12px; }

    /* Mobile card list */
    .mk-card-list { display: none; }
    @media (max-width: 640px) {
        .mk-table-wrap { display: none; }
        .mk-card-list  { display: block; }
    }
    .mk-list-item { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; }
    .mk-list-item:last-child { border-bottom: none; }
    .mk-list-item.inactive { opacity: .5; }
    .mk-list-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
    .mk-list-name { font-weight: 700; font-size: 13px; color: #111827; }
    .mk-list-bottom { display: flex; justify-content: space-between; align-items: center;
        margin-top: 8px; flex-wrap: wrap; gap: 6px; }

    /* Pagination */
    .mk-pagination { padding: 14px 18px; border-top: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .mk-pagination .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; flex-wrap: wrap; }
    .mk-pagination .page-item .page-link { display: inline-flex; align-items: center; justify-content: center;
        min-width: 34px; height: 34px; padding: 0 8px; border-radius: 8px; border: 1px solid #e5e7eb;
        background: #fff; color: #374151; font-size: 13px; font-weight: 600; text-decoration: none; transition: all .15s; }
    .mk-pagination .page-item .page-link:hover { background: #f0fdf4; }
    .mk-pagination .page-item.active .page-link { background: #16a34a; border-color: #16a34a; color: #fff; }
    .mk-pagination .page-item.disabled .page-link { color: #d1d5db; pointer-events: none; }
    .mk-pag-info { font-size: 12px; color: #6b7280; }

    .empty-state { text-align: center; padding: 3rem 1rem; }
    .empty-ico { width: 52px; height: 52px; background: #f3f4f6; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
</style>

<div class="mk-page">

    {{-- Header --}}
    <div class="mk-header">
        <div class="mk-header-left">
            <h1><i class="bi bi-award-fill me-2" style="color:#1D9E75;font-size:20px;"></i>Master Kegiatan Positif</h1>
            <p>Kelola daftar kegiatan positif dan poin pengurangannya</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('bk.poin-positif.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button class="btn-add" onclick="openDrawer('add')">
                <i class="bi bi-plus-lg"></i> Tambah Kegiatan
            </button>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="mk-alert">
        <i class="bi bi-check-circle-fill" style="font-size:16px;flex-shrink:0;"></i>
        <span>{{ session('success') }}</span>
        <button class="mk-alert-close" onclick="this.closest('.mk-alert').remove()">&times;</button>
    </div>
    @endif

    {{-- Stat cards --}}
    <div class="mk-stat-grid">
        <div class="mk-stat c-green">
            <div class="mk-stat-ico"><i class="bi bi-list-check" style="color:#fff;font-size:20px;"></i></div>
            <div>
                <div class="mk-stat-num">{{ $jenis->total() }}</div>
                <div class="mk-stat-lbl">Total Kegiatan</div>
            </div>
        </div>
        <div class="mk-stat c-teal">
            <div class="mk-stat-ico"><i class="bi bi-toggle-on" style="color:#fff;font-size:20px;"></i></div>
            <div>
                <div class="mk-stat-num">{{ $jenis->getCollection()->where('is_active', true)->count() }}</div>
                <div class="mk-stat-lbl">Aktif</div>
            </div>
        </div>
        <div class="mk-stat c-purple">
            <div class="mk-stat-ico"><i class="bi bi-star-fill" style="color:#fff;font-size:20px;"></i></div>
            <div>
                <div class="mk-stat-num">{{ $jenis->getCollection()->max('poin') ?? 0 }}</div>
                <div class="mk-stat-lbl">Poin Tertinggi</div>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="mk-card">
        <div class="mk-card-head">
            <span class="mk-card-title">
                <i class="bi bi-list-check me-2" style="color:#16a34a;"></i>Daftar Kegiatan Positif
            </span>
            <span style="font-size:12px;color:#6b7280;background:#f3f4f6;padding:3px 12px;border-radius:99px;font-weight:600;">
                {{ $jenis->total() }} data
            </span>
        </div>

        {{-- Desktop table --}}
        <div class="mk-table-wrap">
            <table class="mk-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Kegiatan</th>
                        <th>Kategori</th>
                        <th style="width:80px;">Poin</th>
                        <th>Keterangan</th>
                        <th style="width:80px;">Status</th>
                        <th style="width:80px;text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenis as $i => $j)
                    <tr class="{{ !$j->is_active ? 'inactive' : '' }}">
                        <td style="color:#9ca3af;font-size:12px;">{{ $jenis->firstItem() + $i }}</td>
                        <td style="font-weight:700;font-size:13px;color:#111827;">{{ $j->nama }}</td>
                        <td>
                            <span class="badge badge-kat">
                                {{ App\Models\JenisKegiatanPositif::kategoriList()[$j->kategori] ?? $j->kategori }}
                            </span>
                        </td>
                        <td>
                            <span class="poin-badge">+{{ $j->poin }}</span>
                        </td>
                        <td style="font-size:12px;color:#6b7280;">{{ $j->keterangan ?? '-' }}</td>
                        <td>
                            @if($j->is_active)
                                <span class="badge badge-aktif">Aktif</span>
                            @else
                                <span class="badge badge-nonaktif">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <button class="btn-sm-ico edit-btn" title="Edit"
                                    onclick="openDrawer('edit', {{ $j->id }}, '{{ addslashes($j->nama) }}', '{{ $j->kategori }}', {{ $j->poin }}, '{{ addslashes($j->keterangan ?? '') }}', {{ $j->is_active ? 1 : 0 }})">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form action="{{ route('bk.poin-positif.jenis.destroy', $j) }}" method="POST" style="display:inline;" id="del-form-{{ $j->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-sm-ico del-btn" title="Nonaktifkan"
                                        onclick="confirmDelete({{ $j->id }})">
                                        <i class="bi bi-eye-slash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-ico"><i class="bi bi-award" style="font-size:22px;color:#9ca3af;"></i></div>
                                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada data kegiatan positif</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list --}}
        <div class="mk-card-list">
            @forelse($jenis as $j)
            <div class="mk-list-item {{ !$j->is_active ? 'inactive' : '' }}">
                <div class="mk-list-top">
                    <div>
                        <div class="mk-list-name">{{ $j->nama }}</div>
                        <div style="font-size:11px;color:#6b7280;margin-top:2px;">
                            {{ $j->keterangan ?? '-' }}
                        </div>
                    </div>
                    <span class="poin-badge" style="flex-shrink:0;">+{{ $j->poin }}</span>
                </div>
                <div class="mk-list-bottom">
                    <div style="display:flex;gap:5px;flex-wrap:wrap;">
                        <span class="badge badge-kat">{{ App\Models\JenisKegiatanPositif::kategoriList()[$j->kategori] ?? $j->kategori }}</span>
                        @if($j->is_active)
                            <span class="badge badge-aktif">Aktif</span>
                        @else
                            <span class="badge badge-nonaktif">Nonaktif</span>
                        @endif
                    </div>
                    <div style="display:flex;gap:6px;">
                        <button class="btn-sm-ico edit-btn" title="Edit"
                            onclick="openDrawer('edit', {{ $j->id }}, '{{ addslashes($j->nama) }}', '{{ $j->kategori }}', {{ $j->poin }}, '{{ addslashes($j->keterangan ?? '') }}', {{ $j->is_active ? 1 : 0 }})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <form action="{{ route('bk.poin-positif.jenis.destroy', $j) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-sm-ico del-btn" onclick="this.closest('form').submit()">
                                <i class="bi bi-eye-slash-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-ico"><i class="bi bi-award" style="font-size:22px;color:#9ca3af;"></i></div>
                <p style="font-size:13px;color:#9ca3af;margin:0;">Belum ada data kegiatan positif</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($jenis->hasPages())
        <div class="mk-pagination">
            <span class="mk-pag-info">
                Menampilkan {{ $jenis->firstItem() }}–{{ $jenis->lastItem() }} dari {{ $jenis->total() }} data
            </span>
            {{ $jenis->links() }}
        </div>
        @endif
    </div>

</div>

{{-- ══════════════════════════════════════
     DRAWER — Tambah / Edit Kegiatan
══════════════════════════════════════ --}}
<div class="drawer-overlay" id="drawer-overlay" onclick="closeDrawer()"></div>

<div class="drawer" id="drawer-form">
    <div class="drawer-head add-mode" id="drawer-head">
        <div class="drawer-head-left">
            <i class="bi bi-plus-circle" id="drawer-head-ico" style="color:#fff;font-size:16px;"></i>
            <h3 id="drawer-title">Tambah Kegiatan</h3>
        </div>
        <button class="drawer-close" onclick="closeDrawer()">&times;</button>
    </div>

    <div class="drawer-body">
        <form method="POST" id="jenis-form" action="{{ route('bk.poin-positif.jenis.store') }}">
            @csrf
            <div id="method-field"></div>

            <div class="form-field">
                <label>Nama Kegiatan <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" id="fNama"
                    placeholder="Contoh: Juara Olimpiade Sains Nasional" required>
            </div>

            <div class="form-field">
                <label>Kategori <span style="color:#ef4444">*</span></label>
                <select name="kategori" id="fKategori" required>
                    <option value="">-- Pilih --</option>
                    @foreach(App\Models\JenisKegiatanPositif::kategoriList() as $k => $label)
                    <option value="{{ $k }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-field">
                <label>Poin Pengurangan <span style="color:#ef4444">*</span></label>
                <input type="number" name="poin" id="fPoin" min="1" max="50" value="5" required>
                <span class="form-hint">Maksimal 50 poin per kegiatan</span>
            </div>

            <div class="form-field">
                <label>Keterangan</label>
                <textarea name="keterangan" id="fKeterangan" rows="3" placeholder="Deskripsi kegiatan..."></textarea>
            </div>

            <div class="form-field" id="status-field" style="display:none;">
                <label>Status</label>
                <select name="is_active" id="fStatus">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

        </form>
    </div>

    <div class="drawer-foot">
        <div style="display:flex;gap:10px;">
            <button type="button" class="btn-ghost" onclick="closeDrawer()" style="flex:1;">
                Batal
            </button>
            <button type="submit" form="jenis-form" id="btn-submit" class="btn-submit-green" style="flex:2;">
                <i class="bi bi-check-lg me-1"></i><span id="btn-text">Tambah Kegiatan</span>
            </button>
        </div>
    </div>
</div>

<script>
const baseRoute = "{{ route('bk.poin-positif.jenis.store') }}";

function openDrawer(mode, id, nama, kategori, poin, keterangan, isActive) {
    const head   = document.getElementById('drawer-head');
    const ico    = document.getElementById('drawer-head-ico');
    const title  = document.getElementById('drawer-title');
    const submit = document.getElementById('btn-submit');
    const btnTxt = document.getElementById('btn-text');
    const form   = document.getElementById('jenis-form');

    if (mode === 'edit') {
        head.className   = 'drawer-head edit-mode';
        ico.className    = 'bi bi-pencil-fill';
        title.textContent = 'Edit Kegiatan';
        btnTxt.textContent = 'Simpan Perubahan';
        submit.className = 'btn-submit-purple';
        submit.style.flex = '2';

        document.getElementById('fNama').value      = nama;
        document.getElementById('fKategori').value  = kategori;
        document.getElementById('fPoin').value      = poin;
        document.getElementById('fKeterangan').value = keterangan;
        document.getElementById('fStatus').value    = isActive;
        document.getElementById('status-field').style.display = 'flex';

        form.action = `/bk/poin-positif/jenis/${id}`;
        document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    } else {
        head.className   = 'drawer-head add-mode';
        ico.className    = 'bi bi-plus-circle';
        title.textContent = 'Tambah Kegiatan';
        btnTxt.textContent = 'Tambah Kegiatan';
        submit.className = 'btn-submit-green';
        submit.style.flex = '2';

        form.reset();
        document.getElementById('fPoin').value = 5;
        document.getElementById('status-field').style.display = 'none';
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

function confirmDelete(id) {
    if (confirm('Nonaktifkan kegiatan ini?')) {
        document.getElementById('del-form-' + id).submit();
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeDrawer();
});
</script>
@endsection