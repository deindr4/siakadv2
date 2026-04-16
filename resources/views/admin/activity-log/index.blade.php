@extends('layouts.app')
@section('page-title', 'Log Aktivitas')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
<style>
/* ===== ACTIVITY LOG ===== */

/* Toolbar */
.alog-toolbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
.alog-filter-row { display: contents; }
.alog-field { display: flex; flex-direction: column; gap: 3px; }
.alog-field label { font-size: 11px; font-weight: 600; color: #64748b; }
.alog-field input,
.alog-field select {
    padding: 8px 10px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    background: #fff;
    width: 100%;
    box-sizing: border-box;
}
.alog-field-search { flex: 2; min-width: 150px; }
.alog-filter-selects { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; min-width: 220px; }
.alog-filter-dates   { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; min-width: 220px; }
.alog-filter-btns    { display: flex; gap: 6px; align-items: flex-end; }
.alog-actions        { margin-left: auto; display: flex; align-items: flex-end; }
.alog-btn-group      { display: flex; gap: 6px; flex-wrap: wrap; }

/* Table */
.alog-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.alog-table-wrap table { min-width: 650px; width: 100%; }

/* Mobile card list */
.alog-card-list { display: none; }
.alog-log-card  { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; }
.alog-log-card:last-child { border-bottom: none; }
.alog-card-top  {
    display: flex; align-items: flex-start;
    justify-content: space-between; gap: 10px; margin-bottom: 6px;
}
.alog-card-user { font-weight: 700; font-size: 13px; color: #1e293b; }
.alog-card-role { font-size: 11px; color: #94a3b8; }
.alog-card-time { font-size: 11px; color: #94a3b8; text-align: right; white-space: nowrap; }
.alog-card-body { font-size: 13px; color: #374151; margin-bottom: 8px; line-height: 1.5; }
.alog-card-meta { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
.alog-card-ip   { font-size: 11px; color: #94a3b8; font-family: monospace; }
.alog-detail-toggle {
    background: none; border: none; color: #6366f1;
    font-size: 11px; cursor: pointer; text-decoration: underline;
    padding: 0; margin-top: 6px; display: inline-block;
}

/* Pagination */
.alog-pagination {
    padding: 14px 16px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.alog-pagination-info { font-size: 12px; color: #94a3b8; white-space: nowrap; }

/* Modal */
.alog-modal {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.5); z-index: 1000;
    align-items: center; justify-content: center; padding: 16px;
}
.alog-modal-box {
    background: #fff; border-radius: 16px;
    width: 100%; max-width: 400px;
    max-height: 90vh; overflow-y: auto;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    /* Stats */
    .alog-stat-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 10px !important; }
    .alog-stat-grid > div { padding: 12px 14px !important; gap: 10px !important; }
    .alog-stat-icon-box { width: 38px !important; height: 38px !important; }
    .alog-stat-icon-box i { font-size: 16px !important; }
    .alog-stat-num { font-size: 20px !important; }

    /* Toolbar */
    .alog-toolbar    { flex-direction: column; align-items: stretch; }
    .alog-filter-row { display: flex; flex-direction: column; gap: 8px; }
    .alog-field-search { min-width: unset; }
    .alog-filter-selects,
    .alog-filter-dates { min-width: unset; }
    .alog-actions  { margin-left: 0; }
    .alog-btn-group { width: 100%; }
    .alog-btn-group .btn { flex: 1; text-align: center; font-size: 12px; padding: 8px 4px; }

    /* Tabel → card */
    .alog-table-wrap table { display: none; }
    .alog-card-list { display: block; }

    /* Pagination */
    .alog-pagination { justify-content: center; }
    .alog-pagination-info { width: 100%; text-align: center; }

    .page-title h1 { font-size: 20px; }
}

@media (max-width: 480px) {
    .alog-filter-selects,
    .alog-filter-dates { grid-template-columns: 1fr; }
    .alog-stat-num { font-size: 18px !important; }
}
</style>

{{-- Page Title --}}
<div class="page-title" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div>
        <h1>📋 Log Aktivitas</h1>
        <p>Rekam jejak seluruh aktivitas pengguna di sistem &mdash; {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-weight:600;font-size:13px;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Stats --}}
<div class="alog-stat-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    @foreach([
        ['bi-list-ul',            '#6366f1', '#eef2ff', '#e0e7ff', $stats->total,    'Total Log'],
        ['bi-calendar-day',       '#16a34a', '#dcfce7', '#bbf7d0', $stats->hari_ini, 'Hari Ini'],
        ['bi-box-arrow-in-right', '#0284c7', '#e0f2fe', '#bae6fd', $stats->login,    'Login Hari Ini'],
        ['bi-trash-fill',         '#dc2626', '#fee2e2', '#fecaca', $stats->delete,   'Hapus Hari Ini'],
    ] as [$ic, $c, $bg, $border, $v, $l])
    <div style="background:{{ $bg }};border:1.5px solid {{ $border }};border-radius:14px;padding:16px 18px;display:flex;align-items:center;gap:14px;">
        <div class="alog-stat-icon-box" style="width:46px;height:46px;border-radius:12px;background:{{ $c }}22;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-{{ $ic }}" style="font-size:20px;color:{{ $c }};"></i>
        </div>
        <div>
            <p style="font-size:10px;font-weight:700;color:{{ $c }};text-transform:uppercase;letter-spacing:.5px;margin:0 0 2px;">{{ $l }}</p>
            <p class="alog-stat-num" style="font-size:26px;font-weight:800;color:{{ $c }};margin:0;line-height:1;">{{ number_format($v) }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Toolbar Filter --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-body" style="padding:14px 16px;">
        <div class="alog-toolbar">
            <form method="GET" class="alog-filter-row" id="filterForm">

                {{-- Search --}}
                <div class="alog-field alog-field-search">
                    <label>CARI</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama user / deskripsi...">
                </div>

                {{-- Aksi + Modul --}}
                <div class="alog-filter-selects">
                    <div class="alog-field">
                        <label>AKSI</label>
                        <select name="action" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            @foreach($actions as $a)
                            <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alog-field">
                        <label>MODUL</label>
                        <select name="module" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            @foreach($modules as $m)
                            <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="alog-filter-dates">
                    <div class="alog-field">
                        <label>DARI</label>
                        <input type="date" name="tgl_dari" value="{{ request('tgl_dari') }}">
                    </div>
                    <div class="alog-field">
                        <label>SAMPAI</label>
                        <input type="date" name="tgl_sampai" value="{{ request('tgl_sampai') }}">
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="alog-filter-btns">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.activity-log.index') }}" class="btn btn-sm"
                        style="background:#f1f5f9;color:#374151;">
                        <i class="bi bi-x-lg"></i> Reset
                    </a>
                </div>
            </form>

            {{-- Tombol Aksi --}}
            <div class="alog-actions">
                <div class="alog-btn-group">
                    <a href="{{ route('admin.activity-log.download', request()->query()) }}"
                        class="btn btn-sm" style="background:#e0f2fe;color:#0284c7;">
                        <i class="bi bi-download"></i> CSV
                    </a>
                    <button type="button"
                        onclick="document.getElementById('modal-hapus-lama').style.display='flex'"
                        class="btn btn-sm" style="background:#fef3c7;color:#d97706;">
                        <i class="bi bi-clock-history"></i> Log Lama
                    </button>
                    <button type="button"
                        onclick="document.getElementById('modal-hapus-semua').style.display='flex'"
                        class="btn btn-sm btn-danger">
                        <i class="bi bi-trash-fill"></i> Hapus Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel / Card List --}}
<div class="card">
    <div class="card-header">
        <h3><i class="bi bi-list-ul me-2 text-primary"></i>Riwayat Aktivitas</h3>
        <span style="font-size:12px;color:#94a3b8;">{{ number_format($logs->total()) }} entri</span>
    </div>
    <div class="card-body" style="padding:0;">
        @if($logs->isEmpty())
        <div style="text-align:center;padding:48px;color:#94a3b8;">
            <i class="bi bi-journal-x" style="font-size:3rem;display:block;margin-bottom:10px;"></i>
            <p>Belum ada log aktivitas.</p>
        </div>
        @else

        {{-- Desktop: Tabel --}}
        <div class="alog-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Modul</th>
                        <th>Deskripsi</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td style="font-size:12px;color:#64748b;white-space:nowrap;">
                            {{ $log->created_at->translatedFormat('d M Y') }}<br>
                            <span style="font-size:11px;">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px;">{{ $log->name ?? 'System' }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $log->role ?? '-' }}</div>
                        </td>
                        <td>
                            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
                                background:{{ $log->actionBg() }};color:{{ $log->actionColor() }};">
                                {{ $log->actionLabel() }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size:12px;background:#f1f5f9;padding:2px 8px;border-radius:6px;color:#374151;font-weight:600;">
                                {{ $log->module }}
                            </span>
                        </td>
                        <td style="font-size:13px;max-width:280px;">
                            {{ $log->description }}
                            @if($log->old_values || $log->new_values)
                            <button type="button" onclick="toggleDetail('{{ $log->id }}')"
                                class="alog-detail-toggle">detail</button>
                            <div id="detail-{{ $log->id }}" style="display:none;margin-top:6px;">
                                @include('partials.alog-detail', ['log' => $log])
                            </div>
                            @endif
                        </td>
                        <td style="font-size:11px;color:#94a3b8;font-family:monospace;">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile: Card List --}}
        <div class="alog-card-list">
            @foreach($logs as $log)
            <div class="alog-log-card">
                <div class="alog-card-top">
                    <div>
                        <div class="alog-card-user">{{ $log->name ?? 'System' }}</div>
                        <div class="alog-card-role">{{ $log->role ?? '-' }}</div>
                    </div>
                    <div class="alog-card-time">
                        {{ $log->created_at->translatedFormat('d M Y') }}<br>
                        {{ $log->created_at->format('H:i') }}
                    </div>
                </div>
                <div class="alog-card-body">{{ $log->description }}</div>
                <div class="alog-card-meta">
                    <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
                        background:{{ $log->actionBg() }};color:{{ $log->actionColor() }};">
                        {{ $log->actionLabel() }}
                    </span>
                    <span style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:6px;color:#374151;font-weight:600;">
                        {{ $log->module }}
                    </span>
                    @if($log->ip_address)
                    <span class="alog-card-ip">{{ $log->ip_address }}</span>
                    @endif
                </div>
                @if($log->old_values || $log->new_values)
                <button type="button" onclick="toggleDetail('m{{ $log->id }}')"
                    class="alog-detail-toggle">lihat detail perubahan</button>
                <div id="detail-m{{ $log->id }}" style="display:none;margin-top:6px;">
                    @include('partials.alog-detail', ['log' => $log])
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Paginasi --}}
        @if($logs->hasPages())
        <div class="alog-pagination">
            <span class="alog-pagination-info">
                Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }}
                dari {{ number_format($logs->total()) }} entri
            </span>
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif

        @endif
    </div>
</div>

{{-- Modal Hapus Lama --}}
<div id="modal-hapus-lama" class="alog-modal">
    <div class="alog-modal-box">
        <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:15px;font-weight:700;margin:0;">🕐 Hapus Log Lama</h3>
            <button onclick="document.getElementById('modal-hapus-lama').style.display='none'"
                style="background:none;border:none;font-size:20px;cursor:pointer;line-height:1;">✕</button>
        </div>
        <div style="padding:20px;">
            <form method="POST" action="{{ route('admin.activity-log.destroy-old') }}">
                @csrf @method('DELETE')
                <p style="font-size:13px;color:#64748b;margin-bottom:16px;">
                    Hapus log aktivitas yang lebih lama dari berapa hari?
                </p>
                <div style="margin-bottom:16px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">HAPUS LOG LEBIH DARI</label>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input type="number" name="hari" value="30" min="7" max="365"
                            style="flex:1;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;outline:none;text-align:center;font-weight:700;">
                        <span style="font-size:13px;color:#64748b;white-space:nowrap;">hari lalu</span>
                    </div>
                    <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Min 7 hari, maks 365 hari</p>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('modal-hapus-lama').style.display='none'"
                        class="btn" style="background:#f1f5f9;color:#374151;">Batal</button>
                    <button type="submit" class="btn" style="background:#d97706;color:#fff;"
                        onclick="return confirm('Hapus log lama?')">
                        <i class="bi bi-clock-history me-1"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Hapus Semua --}}
<div id="modal-hapus-semua" class="alog-modal">
    <div class="alog-modal-box" style="border:2px solid #fee2e2;">
        <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;background:#fef2f2;border-radius:14px 14px 0 0;">
            <h3 style="font-size:15px;font-weight:700;color:#dc2626;margin:0;">⚠️ Hapus Semua Log</h3>
            <button onclick="document.getElementById('modal-hapus-semua').style.display='none'"
                style="background:none;border:none;font-size:20px;cursor:pointer;line-height:1;">✕</button>
        </div>
        <div style="padding:20px;">
            <form method="POST" action="{{ route('admin.activity-log.destroy-all') }}">
                @csrf @method('DELETE')
                <p style="font-size:13px;color:#dc2626;font-weight:600;margin-bottom:8px;">
                    ⚠️ Tindakan ini akan menghapus <strong>{{ number_format($total) }} log</strong> secara permanen!
                </p>
                <p style="font-size:13px;color:#64748b;margin-bottom:12px;">
                    Ketik <strong>HAPUS SEMUA</strong> untuk konfirmasi:
                </p>
                <input type="text" name="konfirmasi" placeholder="HAPUS SEMUA"
                    style="width:100%;padding:10px 14px;border:1.5px solid #fecaca;border-radius:8px;font-size:14px;outline:none;font-family:inherit;margin-bottom:16px;text-align:center;font-weight:700;color:#dc2626;box-sizing:border-box;">
                @error('konfirmasi')
                <p style="color:#dc2626;font-size:12px;margin-bottom:10px;">{{ $message }}</p>
                @enderror
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('modal-hapus-semua').style.display='none'"
                        class="btn" style="background:#f1f5f9;color:#374151;">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash-fill me-1"></i>Hapus Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDetail(id) {
    const el = document.getElementById('detail-' + id);
    if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
['modal-hapus-lama','modal-hapus-semua'].forEach(function(id) {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});
</script>
@endsection
