@extends('layouts.app')

@section('page-title', 'Absensi Saya')
@section('sidebar-menu')
    @include('partials.sidebar_siswa')
@endsection

@section('content')
<style>
    /* Responsive Grid untuk Rekap */
    .rekap-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    /* Sembunyikan Tabel di Mobile, tampilkan Card */
    .mobile-card {
        display: none;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    @media (max-width: 768px) {
        .table-wrapper { display: none; }
        .mobile-card { display: block; }
        .rekap-grid { grid-template-columns: repeat(3, 1fr); }
    }

    .badge-status {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 50px;
        font-weight: 700;
        display: inline-block;
    }

    .pagination-wrapper {
        padding: 15px;
        background: #fff;
        border-radius: 0 0 12px 12px;
    }
</style>

<div class="page-title">
    <h1 style="font-size: 24px;">📅 Absensi Saya</h1>
    <p style="color: #64748b;">Riwayat kehadiran <strong>{{ $siswa->nama }}</strong></p>
</div>

{{-- Rekap --}}
<div class="rekap-grid">
    @foreach(['H' => ['Hadir','#16a34a','#dcfce7'], 'S' => ['Sakit','#0284c7','#e0f2fe'], 'I' => ['Izin','#d97706','#fef3c7'], 'A' => ['Alpa','#dc2626','#fee2e2'], 'D' => ['Dispensasi','#7c3aed','#ede9fe']] as $kode => $info)
    <div style="background:{{ $info[2] }}; border-radius:12px; padding:12px; text-align:center; border: 1px solid {{ $info[1] }}20;">
        <p style="font-size:11px; color:{{ $info[1] }}; font-weight:700; margin-bottom:4px;">{{ $info[0] }}</p>
        <p style="font-size:24px; font-weight:800; color:{{ $info[1] }}; margin:0;">{{ $rekap[$kode] ?? 0 }}</p>
    </div>
    @endforeach
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px; border-radius: 12px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
    <div class="card-body">
        <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <div style="flex:1; min-width:140px;">
                <label style="font-size:11px; font-weight:700; color:#64748b; display:block; margin-bottom:4px; text-transform: uppercase;">Semester</label>
                <select name="semester_id" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; background:#f8fafc;">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                            {{ $sem->nama }} {{ $sem->is_aktif ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1; min-width:140px;">
                <label style="font-size:11px; font-weight:700; color:#64748b; display:block; margin-bottom:4px; text-transform: uppercase;">Bulan</label>
                <select name="bulan" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; background:#f8fafc;">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1,12) as $b)
                        <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px; width: 100%; max-width: 200px;">
                <button type="submit" class="btn btn-primary" style="flex:1; border-radius:8px;">Cari</button>
                <a href="{{ route('siswa.absensi') }}" class="btn" style="background:#f1f5f9; color:#475569; border-radius:8px;">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Riwayat --}}
<div class="card" style="border:none; background:transparent;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h3 style="font-size:18px; font-weight:700;">📊 Riwayat Absensi</h3>
        <span style="font-size:12px; background:#e2e8f0; padding:2px 10px; border-radius:20px;">{{ $absensi->total() }} Data</span>
    </div>

    @php
        $statusMap = [
            'H' => ['Hadir',      '#16a34a', '#dcfce7'],
            'S' => ['Sakit',      '#0284c7', '#e0f2fe'],
            'I' => ['Izin',       '#d97706', '#fef3c7'],
            'A' => ['Alpa',       '#dc2626', '#fee2e2'],
            'D' => ['Dispensasi', '#7c3aed', '#ede9fe'],
        ];
    @endphp

    {{-- Desktop Table --}}
    <div class="table-wrapper" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table class="table" style="margin-bottom: 0;">
            <thead style="background: #f8fafc;">
                <tr>
                    <th style="font-size:12px; color:#64748b;">TANGGAL</th>
                    <th style="font-size:12px; color:#64748b;">KELAS / GURU</th>
                    <th style="font-size:12px; color:#64748b; text-align:center;">STATUS</th>
                    <th style="font-size:12px; color:#64748b;">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensi as $a)
                @php $sm = $statusMap[$a->status] ?? ['-','#94a3b8','#f1f5f9']; @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $a->absensiHarian?->tanggal?->translatedFormat('d M Y') }}</div>
                        <small style="color:#94a3b8;">{{ $a->absensiHarian?->tanggal?->translatedFormat('l') }}</small>
                    </td>
                    <td>
                        <div style="font-size:13px; font-weight:500;">{{ $a->absensiHarian?->nama_rombel }}</div>
                        <div style="font-size:11px; color:#64748b;">Oleh: {{ $a->absensiHarian?->nama_guru ?? '-' }}</div>
                    </td>
                    <td style="text-align:center;">
                        <span class="badge-status" style="background:{{ $sm[2] }}; color:{{ $sm[1] }};">
                            {{ $sm[0] }}
                        </span>
                    </td>
                    <td style="font-size:12px; color:#64748b;">{{ $a->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center; padding:40px; color:#94a3b8;">Data tidak ditemukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    @foreach($absensi as $a)
    @php $sm = $statusMap[$a->status] ?? ['-','#94a3b8','#f1f5f9']; @endphp
    <div class="mobile-card">
        <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:10px;">
            <div>
                <div style="font-weight:700; color:#1e293b;">{{ $a->absensiHarian?->tanggal?->translatedFormat('d F Y') }}</div>
                <div style="font-size:12px; color:#64748b;">{{ $a->absensiHarian?->tanggal?->translatedFormat('l') }}</div>
            </div>
            <span class="badge-status" style="background:{{ $sm[2] }}; color:{{ $sm[1] }};">
                {{ $sm[0] }}
            </span>
        </div>
        <div style="font-size:13px; border-top:1px dashed #e2e8f0; padding-top:10px;">
            <div style="margin-bottom:4px;"><strong>📍 Kelas:</strong> {{ $a->absensiHarian?->nama_rombel }}</div>
            <div style="margin-bottom:4px;"><strong>👨‍🏫 Guru:</strong> {{ $a->absensiHarian?->nama_guru ?? '-' }}</div>
            <div style="color:#64748b;"><strong>💬 Ket:</strong> {{ $a->keterangan ?? '-' }}</div>
        </div>
    </div>
    @endforeach

    {{-- Pagination --}}
    @if($absensi->hasPages())
    <div class="pagination-wrapper">
        {{ $absensi->links() }}
    </div>
    @endif
</div>
@endsection
