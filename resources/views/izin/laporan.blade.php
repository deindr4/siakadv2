@extends('layouts.app')
@section('title', 'Laporan Izin Berencana')

@section('content')
<style>
    /* Responsive Grid untuk Statistik */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    /* Responsive Grid untuk Rekap Table */
    .rekap-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    /* Table Wrapper agar bisa di-scroll horizontal di HP */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Penyesuaian Mobile (Layar di bawah 768px) */
    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: repeat(2, 1fr); /* 2 kolom di HP */
            gap: 10px;
        }

        .rekap-grid {
            grid-template-columns: 1fr; /* Stack menumpuk ke bawah */
        }

        .page-title h1 { font-size: 1.5rem; }

        /* Form filter agar lebih lega di HP */
        .filter-form {
            flex-direction: column;
            align-items: stretch !important;
        }

        .filter-item {
            flex: none !important;
            width: 100%;
        }
    }
</style>

<div class="page-title">
    <h1>📊 Laporan Izin Berencana</h1>
    <p>Rekap dan statistik pengajuan izin siswa</p>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px; border-radius: 12px;">
    <div class="card-body" style="padding:14px 16px;">
        <form method="GET" class="filter-form" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            @foreach([
                ['semester_id', 'SEMESTER', $semesters, 'nama'],
                ['status', 'STATUS', ['pending' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'dibatalkan' => 'Dibatalkan'], null],
                ['jenis', 'JENIS', App\Models\IzinBerencana::jenisList(), null],
                ['bulan', 'BULAN', array_combine(range(1,12), array_map(fn($m) => \Carbon\Carbon::create()->month($m)->translatedFormat('F'), range(1,12))), null]
            ] as [$name, $label, $options, $labelKey])
            <div class="filter-item" style="flex:1; min-width:140px;">
                <label style="font-size:11px; font-weight:700; color:#64748b; display:block; margin-bottom:5px;">{{ $label }}</label>
                <select name="{{ $name }}" style="width:100%; padding:10px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; background:#fff;" onchange="this.form.submit()">
                    @if($name !== 'semester_id') <option value="">Semua</option> @endif
                    @foreach($options as $k => $v)
                        @php
                            $val = $labelKey ? $v->id : $k;
                            $text = $labelKey ? ($v->$labelKey . ($v->is_aktif ? ' (Aktif)' : '')) : $v;
                            $selected = request($name) == $val || ($name == 'semester_id' && $semesterId == $val);
                        @endphp
                        <option value="{{ $val }}" {{ $selected ? 'selected' : '' }}>{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach

            <a href="{{ route('izin.laporan') }}" class="btn" style="background:#f1f5f9; color:#475569; padding:9px 16px; border-radius:8px; font-size:13px; font-weight:600;">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </form>
    </div>
</div>

{{-- Stats --}}
@php
    $totalSemua = $data->count();
    $totalDisetujui = $data->where('status','disetujui')->count();
    $totalPending = $data->where('status','pending')->count();
    $totalHari = $data->where('status','disetujui')->sum('jumlah_hari_disetujui');
@endphp

<div class="stats-container">
    @foreach([
        ['bi-file-earmark-text', '#6366f1', '#eef2ff', $totalSemua, 'Total'],
        ['bi-check-circle-fill', '#16a34a', '#dcfce7', $totalDisetujui, 'Setuju'],
        ['bi-hourglass-split', '#d97706', '#fef3c7', $totalPending, 'Pending'],
        ['bi-calendar-day', '#0284c7', '#e0f2fe', $totalHari, 'Hari'],
    ] as [$icon, $color, $bg, $val, $label])
    <div class="card" style="border:0; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); border-radius:12px; overflow:hidden;">
        <div style="background:{{ $bg }}; padding:15px; text-align:center;">
            <i class="bi {{ $icon }}" style="font-size:20px; color:{{ $color }};"></i>
            <div style="font-size:22px; font-weight:800; color:{{ $color }}; margin:5px 0;">{{ $val }}</div>
            <div style="font-size:11px; font-weight:700; color:{{ $color }}; text-transform:uppercase; opacity:0.8;">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="rekap-grid">
    {{-- Rekap per Jenis --}}
    <div class="card" style="border-radius:12px; border:none; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div class="card-header" style="background:transparent; border-bottom:1px solid #f1f5f9; padding:15px;">
            <h3 style="font-size:14px; margin:0; font-weight:700;"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Rekap per Jenis</h3>
        </div>
        <div class="table-responsive">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead style="background:#f8fafc; color:#64748b;">
                    <tr>
                        <th style="padding:12px; text-align:left;">JENIS</th>
                        <th style="padding:12px; text-align:center;">DISETUJUI</th>
                        <th style="padding:12px; text-align:center;">HARI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(App\Models\IzinBerencana::jenisList() as $k => $label)
                    @php $r = $rekapJenis[$k] ?? ['total'=>0,'disetujui'=>0,'hari'=>0]; @endphp
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:12px;">{{ $label }}</td>
                        <td style="padding:12px; text-align:center;"><span style="background:#dcfce7; color:#15803d; font-weight:700; padding:2px 8px; border-radius:10px;">{{ $r['disetujui'] }}</span></td>
                        <td style="padding:12px; text-align:center; font-weight:600; color:#0284c7;">{{ $r['hari'] }} h</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Rekap per Bulan --}}
    <div class="card" style="border-radius:12px; border:none; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div class="card-header" style="background:transparent; border-bottom:1px solid #f1f5f9; padding:15px;">
            <h3 style="font-size:14px; margin:0; font-weight:700;"><i class="bi bi-calendar3 me-2 text-primary"></i>Rekap per Bulan</h3>
        </div>
        <div class="table-responsive">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead style="background:#f8fafc; color:#64748b;">
                    <tr>
                        <th style="padding:12px; text-align:left;">BULAN</th>
                        <th style="padding:12px; text-align:center;">PENGAJUAN</th>
                        <th style="padding:12px; text-align:center;">TOTAL HARI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapBulan as $bulan => $r)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:12px; font-weight:600;">{{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</td>
                        <td style="padding:12px; text-align:center;"><span style="background:#dcfce7; color:#15803d; font-weight:700; padding:2px 8px; border-radius:10px;">{{ $r['total'] }}</span></td>
                        <td style="padding:12px; text-align:center; font-weight:600; color:#0284c7;">{{ $r['hari'] }} h</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="padding:20px; text-align:center; color:#94a3b8;">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Detail Table --}}
<div class="card" style="border-radius:12px; border:none; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px;">
        <h3 style="font-size:14px; margin:0; font-weight:700;"><i class="bi bi-table me-2 text-primary"></i>Detail</h3>
        <span style="font-size:11px; background:#f1f5f9; padding:2px 8px; border-radius:10px; color:#64748b;">{{ $data->count() }} data</span>
    </div>
    <div class="table-responsive">
        <table style="width:100%; border-collapse:collapse; font-size:13px; min-width:800px;">
            <thead style="background:#f8fafc; color:#64748b; font-size:11px; text-transform:uppercase;">
                <tr>
                    <th style="padding:15px; text-align:left;">No. Izin</th>
                    <th style="padding:15px; text-align:left;">Siswa</th>
                    <th style="padding:15px; text-align:left;">Jenis</th>
                    <th style="padding:15px; text-align:left;">Tanggal</th>
                    <th style="padding:15px; text-align:center;">Hari</th>
                    <th style="padding:15px; text-align:left;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $izin)
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:12px 15px;"><a href="{{ route('izin.show', $izin) }}" style="color:#6366f1; font-weight:700; font-family:monospace;">{{ $izin->nomor_izin }}</a></td>
                    <td style="padding:12px 15px;">
                        <div style="font-weight:600;">{{ $izin->siswa?->nama }}</div>
                        <div style="font-size:11px; color:#94a3b8;">{{ $izin->siswa?->nama_rombel }}</div>
                    </td>
                    <td style="padding:12px 15px;">{{ App\Models\IzinBerencana::jenisList()[$izin->jenis] ?? $izin->jenis }}</td>
                    <td style="padding:12px 15px;">{{ $izin->tanggal_mulai->translatedFormat('d/m/y') }}</td>
                    <td style="padding:12px 15px; text-align:center; font-weight:700;">{{ $izin->jumlah_hari_disetujui ?: $izin->jumlah_hari }}</td>
                    <td style="padding:12px 15px;">
                        <span style="background:{{ $izin->statusBg() }}; color:{{ $izin->statusColor() }}; font-weight:700; padding:4px 10px; border-radius:20px; font-size:11px;">
                            {{ $izin->statusLabel() }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
