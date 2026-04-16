@extends('layouts.app')
@section('page-title', 'Poin Kebaikan Siswa')
@section('sidebar-menu') @include('partials.sidebar_bk') @endsection
@section('content')

<div class="page-title">
    <h1>⭐ Poin Kebaikan Siswa</h1>
    <p>Catat kegiatan positif siswa untuk mengurangi poin pelanggaran</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
</div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;" class="stats-grid">
    @foreach([
        ['bi-star-fill',      '#16a34a', '#f0fdf4', $totalSemester,        'Total Semester'],
        ['bi-calendar-check', '#d97706', '#fffbeb', $totalBulanIni,        'Bulan Ini'],
        ['bi-calendar-day',   '#6366f1', '#eef2ff', $totalHariIni,         'Hari Ini'],
        ['bi-people-fill',    '#16a34a', '#f0fdf4', $siswaList->count(),   'Siswa Aktif'],
    ] as [$icon, $color, $bg, $val, $label])
    <div class="card" style="border:0;box-shadow:0 1px 6px rgba(0,0,0,.06);">
        <div style="background:{{ $bg }};border-radius:12px;padding:20px 16px;text-align:center;">
            <i class="bi {{ $icon }}" style="font-size:24px;color:{{ $color }};display:block;margin-bottom:8px;"></i>
            <div style="font-size:30px;font-weight:800;color:{{ $color }};line-height:1;">{{ $val }}</div>
            <div style="font-size:12px;font-weight:600;color:{{ $color }};margin-top:4px;opacity:.85;">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="poin-layout">

    {{-- Daftar --}}
    <div>
        {{-- Filter --}}
        <div class="card" style="margin-bottom:16px;">
            <div class="card-body" style="padding:14px 16px;">
                <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;width:100%;">
                    <input type="hidden" name="semester_id" value="{{ $semesterId }}">
                    <div style="flex:2;min-width:160px;">
                        <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:3px;">CARI SISWA</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NISN..."
                            style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;">
                    </div>
                    <div style="flex:1;min-width:130px;">
                        <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:3px;">KATEGORI</label>
                        <select name="kategori" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#fff;" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            @foreach(App\Models\JenisKegiatanPositif::kategoriList() as $k => $label)
                            <option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex:1;min-width:130px;">
                        <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:3px;">SEMESTER</label>
                        <select name="semester_id" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#fff;" onchange="this.form.submit()">
                            @foreach($semesters as $s)
                            <option value="{{ $s->id }}" {{ $semesterId == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                    <a href="{{ route('bk.poin-positif.index') }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i></a>
                </form>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card">
            <div class="card-header" style="flex-wrap:wrap;gap:8px;">
                <h3><i class="bi bi-star me-2 text-warning"></i>Riwayat Poin Kebaikan</h3>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="{{ route('bk.poin-positif.rekap', ['semester_id' => $semesterId]) }}"
                        class="btn btn-sm" style="background:#eef2ff;color:#6366f1;">
                        <i class="bi bi-bar-chart-fill"></i> Rekap Net Poin
                    </a>
                    <a href="{{ route('bk.poin-positif.jenis') }}"
                        class="btn btn-sm" style="background:#f0fdf4;color:#16a34a;">
                        <i class="bi bi-list-check"></i> Master Kegiatan
                    </a>
                </div>
            </div>
            <div class="card-body" style="padding:0;">
                @if($records->isEmpty())
                <div style="text-align:center;padding:48px;color:#94a3b8;">
                    <i class="bi bi-star" style="font-size:3rem;display:block;margin-bottom:10px;color:#fbbf24;"></i>
                    <p>Belum ada poin kebaikan dicatat.</p>
                </div>
                @else
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Siswa</th>
                                <th>Kegiatan</th>
                                <th>Kategori</th>
                                <th>Poin</th>
                                <th>Tanggal</th>
                                <th>Dicatat Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $i => $r)
                            <tr>
                                <td>{{ $records->firstItem() + $i }}</td>
                                <td>
                                    <div style="font-weight:600;font-size:13px;">{{ $r->siswa?->nama }}</div>
                                    <div style="font-size:11px;color:#94a3b8;">{{ $r->siswa?->nisn }}</div>
                                </td>
                                <td>
                                    <div style="font-size:13px;font-weight:600;">{{ $r->jenisKegiatan?->nama }}</div>
                                    @if($r->keterangan)
                                    <div style="font-size:11px;color:#64748b;">{{ $r->keterangan }}</div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $kat = $r->jenisKegiatan?->kategori;
                                        $katLabel = App\Models\JenisKegiatanPositif::kategoriList()[$kat] ?? $kat;
                                    @endphp
                                    <span style="font-size:11px;background:#f0fdf4;color:#16a34a;padding:2px 8px;border-radius:20px;font-weight:600;">
                                        {{ $katLabel }}
                                    </span>
                                </td>
                                <td>
                                    <span style="background:#dcfce7;color:#15803d;font-weight:800;padding:3px 10px;border-radius:20px;font-size:13px;">
                                        +{{ $r->poin }}
                                    </span>
                                </td>
                                <td style="font-size:12px;">{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}</td>
                                <td style="font-size:12px;color:#64748b;">{{ $r->dicatatOleh?->name }}</td>
                                <td>
                                    <form action="{{ route('bk.poin-positif.destroy', $r) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmDelete(this.closest('form'))">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($records->hasPages())
                <div style="padding:14px 16px;border-top:1px solid #f1f5f9;">{{ $records->links() }}</div>
                @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Form Input --}}
    <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;">
            <h3 style="color:#fff;"><i class="bi bi-plus-circle me-2"></i>Catat Poin Kebaikan</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('bk.poin-positif.store') }}">
                @csrf
                <input type="hidden" name="semester_id" value="{{ $semesterAktif?->id }}">

                {{-- Pilih Kelas dulu --}}
                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">KELAS <span style="color:#ef4444">*</span></label>
                    <select id="filterKelas" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#fff;" onchange="filterSiswa(this.value)">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($rombels as $r)
                        <option value="{{ $r->rombongan_belajar_id }}">{{ $r->nama_rombel }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Siswa (filter by kelas) --}}
                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">SISWA <span style="color:#ef4444">*</span></label>
                    <select name="siswa_id" id="selectSiswa" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#fff;" required>
                        <option value="">-- Pilih Kelas dulu --</option>
                    </select>
                    {{-- Data siswa tersembunyi untuk JS --}}
                    <div id="dataSiswa" style="display:none">
                        @foreach($siswaList as $s)
                        <span data-id="{{ $s->id }}" data-nama="{{ $s->nama }}" data-rombel="{{ $s->rombongan_belajar_id }}"></span>
                        @endforeach
                    </div>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">JENIS KEGIATAN <span style="color:#ef4444">*</span></label>
                    <select name="jenis_kegiatan_id" id="jenisSelect"
                        style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#fff;" required
                        onchange="updatePoin(this)">
                        <option value="">-- Pilih Kegiatan --</option>
                        @foreach(App\Models\JenisKegiatanPositif::kategoriList() as $k => $label)
                        @php $items = $jenisList->where('kategori', $k); @endphp
                        @if($items->count())
                        <optgroup label="{{ $label }}">
                            @foreach($items as $j)
                            <option value="{{ $j->id }}" data-poin="{{ $j->poin }}">
                                {{ $j->nama }} (+{{ $j->poin }} poin)
                            </option>
                            @endforeach
                        </optgroup>
                        @endif
                        @endforeach
                    </select>
                </div>

                {{-- Preview poin --}}
                <div id="poinPreview" style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;margin-bottom:14px;text-align:center;">
                    <div style="font-size:11px;color:#16a34a;font-weight:600;">POIN PENGURANGAN</div>
                    <div id="poinValue" style="font-size:28px;font-weight:800;color:#15803d;"></div>
                    <div style="font-size:11px;color:#16a34a;">poin pelanggaran akan dikurangi</div>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">TANGGAL <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                        style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;" required>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">KETERANGAN</label>
                    <textarea name="keterangan" rows="2"
                        placeholder="Nama lomba, tingkat, hasil, dll..."
                        style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;resize:vertical;"></textarea>
                </div>

                <button type="submit" class="btn" style="width:100%;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-weight:700;padding:10px;">
                    <i class="bi bi-star-fill me-2"></i>Simpan Poin Kebaikan
                </button>
            </form>
        </div>
    </div>

</div>

<style>
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    .poin-layout {
        display: flex !important;
        flex-direction: column-reverse !important;
        gap: 16px;
    }
    .poin-layout > div:first-child { order: 2; }
    .poin-layout > div:last-child  { order: 1; position: static !important; }
}
@media (min-width: 769px) {
    .poin-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 20px;
        align-items: start;
    }
    .poin-layout > div:last-child {
        position: sticky;
        top: 20px;
    }
}
</style>

<script>
function filterSiswa(rombelId) {
    const select = document.getElementById('selectSiswa');
    const spans  = document.querySelectorAll('#dataSiswa span');
    select.innerHTML = '<option value="">-- Pilih Siswa --</option>';

    if (!rombelId) {
        select.innerHTML = '<option value="">-- Pilih Kelas dulu --</option>';
        return;
    }

    let count = 0;
    spans.forEach(s => {
        if (s.dataset.rombel === rombelId) {
            const opt = document.createElement('option');
            opt.value = s.dataset.id;
            opt.textContent = s.dataset.nama;
            select.appendChild(opt);
            count++;
        }
    });

    if (count === 0) {
        select.innerHTML = '<option value="">Tidak ada siswa di kelas ini</option>';
    }
}

function updatePoin(sel) {
    const opt = sel.options[sel.selectedIndex];
    const poin = opt.dataset.poin;
    const preview = document.getElementById('poinPreview');
    const val = document.getElementById('poinValue');
    if (poin) {
        val.textContent = '+' + poin;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}
</script>

@endsection
