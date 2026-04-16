@extends('layouts.app')
@section('page-title', 'Jurnal Kelas Saya')
@section('sidebar-menu') @include('partials.sidebar_siswa') @endsection

@section('content')
<style>
/* ===== JURNAL KELAS ===== */
.jk-info-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.jk-info-box {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
}
.jk-info-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.jk-info-val   { font-size: 18px; font-weight: 800; color: #1e293b; }
.jk-info-val.accent { color: #6366f1; }

/* Filter */
.jk-filter { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
.jk-filter-field { display: flex; flex-direction: column; gap: 3px; flex: 1; min-width: 140px; }
.jk-filter-field label { font-size: 11px; font-weight: 700; color: #64748b; }
.jk-filter-field select {
    padding: 9px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    background: #fff;
    font-family: inherit;
}
.jk-filter-btns { display: flex; gap: 8px; align-items: flex-end; }

/* Desktop Table */
.jk-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.jk-table-wrap table { width: 100%; min-width: 600px; }

/* Card List (mobile) */
.jk-card-list { display: none; }
.jk-card {
    padding: 16px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.jk-card:last-child { border-bottom: none; }
.jk-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
}
.jk-card-mapel { font-weight: 700; font-size: 14px; color: #1e293b; }
.jk-card-kode  { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.jk-card-date  {
    text-align: right;
    flex-shrink: 0;
    font-size: 12px;
    color: #64748b;
    background: #f1f5f9;
    padding: 4px 10px;
    border-radius: 8px;
    white-space: nowrap;
}
.jk-card-materi {
    font-size: 13px;
    color: #374151;
    line-height: 1.5;
    background: #f8fafc;
    border-left: 3px solid #6366f1;
    padding: 8px 12px;
    border-radius: 0 8px 8px 0;
}
.jk-card-meta {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}
.jk-chip {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.jk-chip.guru   { background: #eef2ff; color: #4f46e5; }
.jk-chip.ptm    { background: #f0fdf4; color: #16a34a; }
.jk-chip.jam    { background: #fef3c7; color: #d97706; }

/* Pagination */
.jk-paging {
    padding: 14px 16px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.jk-paging-info { font-size: 12px; color: #94a3b8; }

/* Responsive */
@media (max-width: 768px) {
    .jk-info-grid { grid-template-columns: repeat(2, 1fr); }
    .jk-filter { flex-direction: column; }
    .jk-filter-field { min-width: unset; width: 100%; }
    .jk-filter-btns { width: 100%; }
    .jk-filter-btns .btn { flex: 1; text-align: center; }
    .jk-table-wrap table { display: none; }
    .jk-card-list { display: block; }
    .jk-paging { justify-content: center; }
    .jk-paging-info { width: 100%; text-align: center; }
}
@media (max-width: 400px) {
    .jk-info-grid { grid-template-columns: repeat(2, 1fr); }
    .jk-info-val { font-size: 16px; }
}
</style>

<div class="page-title">
    <h1><i class="bi bi-journal-text me-2" style="color:#6366f1;"></i>Jurnal Kelas</h1>
    <p>Daftar jurnal mengajar di kelas <strong>{{ $siswa->nama_rombel ?? '-' }}</strong></p>
</div>

{{-- Info Siswa --}}
<div class="jk-info-grid">
    <div class="jk-info-box" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);border-color:transparent;">
        <div class="jk-info-label" style="color:#c7d2fe;"><i class="bi bi-person-fill me-1"></i>Nama Siswa</div>
        <div class="jk-info-val" style="font-size:14px;color:#fff;">{{ $siswa->nama }}</div>
    </div>
    <div class="jk-info-box" style="background:linear-gradient(135deg,#0284c7,#38bdf8);border-color:transparent;">
        <div class="jk-info-label" style="color:#bae6fd;"><i class="bi bi-diagram-3-fill me-1"></i>Kelas</div>
        <div class="jk-info-val" style="font-size:14px;color:#fff;">{{ $siswa->nama_rombel ?? '-' }}</div>
    </div>
    <div class="jk-info-box" style="background:linear-gradient(135deg,#16a34a,#4ade80);border-color:transparent;">
        <div class="jk-info-label" style="color:#bbf7d0;"><i class="bi bi-card-text me-1"></i>NISN</div>
        <div class="jk-info-val" style="font-size:14px;color:#fff;">{{ $siswa->nisn ?? '-' }}</div>
    </div>
    <div class="jk-info-box" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);border-color:transparent;">
        <div class="jk-info-label" style="color:#fef3c7;"><i class="bi bi-journal-check me-1"></i>Total Jurnal</div>
        <div class="jk-info-val" style="color:#fff;">{{ $jurnals->total() }}</div>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:14px 16px;">
        <form method="GET" class="jk-filter">
            <div class="jk-filter-field">
                <label>SEMESTER</label>
                <select name="semester_id">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                        {{ $sem->nama }}{{ $sem->is_aktif ? ' (Aktif)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="jk-filter-field">
                <label>BULAN</label>
                <select name="bulan">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1,12) as $b)
                    <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="jk-filter-btns">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('siswa.jurnal.kelas') }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;">
                    <i class="bi bi-x-lg"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel + Card --}}
<div class="card">
    <div class="card-header">
        <h3>
            <i class="bi bi-list-ul me-2 text-primary"></i>Daftar Jurnal
        </h3>
        <span style="font-size:12px;color:#94a3b8;">{{ number_format($jurnals->total()) }} jurnal</span>
    </div>
    <div class="card-body" style="padding:0;">

        @if($jurnals->isEmpty())
        <div style="text-align:center;padding:48px;color:#94a3b8;">
            <i class="bi bi-journal-x" style="font-size:3rem;display:block;margin-bottom:10px;"></i>
            <p>Belum ada jurnal untuk kelas ini</p>
        </div>
        @else

        {{-- Desktop: Tabel --}}
        <div class="jk-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th style="text-align:center;">Ptm</th>
                        <th>Jam</th>
                        <th>Materi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurnals as $i => $j)
                    <tr>
                        <td style="font-size:12px;color:#94a3b8;">{{ $jurnals->firstItem() + $i }}</td>
                        <td>
                            <div style="font-size:13px;font-weight:600;white-space:nowrap;">{{ $j->tanggal?->format('d/m/Y') }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $j->tanggal?->translatedFormat('l') }}</div>
                        </td>
                        <td>
                            <div style="font-size:13px;font-weight:600;">{{ $j->mataPelajaran?->nama }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $j->mataPelajaran?->kode }}</div>
                        </td>
                        <td style="font-size:13px;">{{ $j->guru?->nama }}</td>
                        <td style="text-align:center;">
                            <span style="font-size:15px;font-weight:800;color:#6366f1;">{{ $j->pertemuan_ke ?? '-' }}</span>
                        </td>
                        <td style="font-size:12px;color:#64748b;white-space:nowrap;">
                            {{ $j->jam_mulai?->format('H:i') }}
                            @if($j->jam_selesai) &ndash; {{ $j->jam_selesai->format('H:i') }} @endif
                        </td>
        <td style="font-size:13px;max-width:250px;">{!! preg_replace('/(https?:\/\/[^\s<>"]+)/i','<a href="$1" target="_blank" rel="noopener noreferrer" style="color:#6366f1;text-decoration:underline;word-break:break-all;">$1</a>', e($j->materi ?? '')) !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile: Card List --}}
        <div class="jk-card-list">
            @foreach($jurnals as $j)
            <div class="jk-card">
                <div class="jk-card-top">
                    <div>
                        <div class="jk-card-mapel">{{ $j->mataPelajaran?->nama }}</div>
                        <div class="jk-card-kode">{{ $j->mataPelajaran?->kode }}</div>
                    </div>
                    <div class="jk-card-date">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $j->tanggal?->format('d/m/Y') }}<br>
                        <span style="font-size:10px;">{{ $j->tanggal?->translatedFormat('l') }}</span>
                    </div>
                </div>

                @if($j->materi)
                <div class="jk-card-materi">{!! preg_replace('/(https?:\/\/[^\s<>"]+)/i','<a href="$1" target="_blank" rel="noopener noreferrer" style="color:#6366f1;text-decoration:underline;word-break:break-all;">$1</a>', e($j->materi)) !!}</div>
                @endif

                <div class="jk-card-meta">
                    <span class="jk-chip guru">
                        <i class="bi bi-person-fill"></i> {{ $j->guru?->nama }}
                    </span>
                    @if($j->pertemuan_ke)
                    <span class="jk-chip ptm">
                        <i class="bi bi-hash"></i> Ptm {{ $j->pertemuan_ke }}
                    </span>
                    @endif
                    @if($j->jam_mulai)
                    <span class="jk-chip jam">
                        <i class="bi bi-clock"></i>
                        {{ $j->jam_mulai?->format('H:i') }}
                        @if($j->jam_selesai) &ndash; {{ $j->jam_selesai->format('H:i') }} @endif
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($jurnals->hasPages())
        <div class="jk-paging">
            <span class="jk-paging-info">
                Menampilkan {{ $jurnals->firstItem() }}&#8211;{{ $jurnals->lastItem() }}
                dari {{ number_format($jurnals->total()) }} jurnal
            </span>
            {{ $jurnals->withQueryString()->links() }}
        </div>
        @endif

        @endif
    </div>
</div>
@endsection
