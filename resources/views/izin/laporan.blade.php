@extends('layouts.app')
@section('title', 'Laporan Izin Berencana')

@section('content')
<style>
    .lb-page { width: 100%; padding: 1.5rem 2.5rem; }
    @media (max-width: 768px) { .lb-page { padding: 1rem; } }

    /* Header */
    .lb-header { margin-bottom: 1.5rem; }
    .lb-header h1 { font-size: 22px; font-weight: 700; color: #111827; }
    .lb-header p  { font-size: 13px; color: #6b7280; margin-top: 3px; }

    /* Stat cards */
    .lb-stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 14px; margin-bottom: 1.5rem; }
    @media (max-width: 768px) { .lb-stat-grid { grid-template-columns: repeat(2, minmax(0,1fr)); gap: 10px; } }
    @media (max-width: 400px) { .lb-stat-grid { grid-template-columns: 1fr; } }
    .lb-stat { border-radius: 14px; padding: 18px 20px; display: flex; align-items: center;
        gap: 14px; position: relative; overflow: hidden; }
    .lb-stat.c-purple { background: linear-gradient(135deg, #534AB7 0%, #3C3489 100%); }
    .lb-stat.c-green  { background: linear-gradient(135deg, #1D9E75 0%, #0F6E56 100%); }
    .lb-stat.c-amber  { background: linear-gradient(135deg, #EF9F27 0%, #854F0B 100%); }
    .lb-stat.c-blue   { background: linear-gradient(135deg, #378ADD 0%, #0C447C 100%); }
    .lb-stat::after { content:''; position:absolute; right:-16px; top:-16px; width:80px; height:80px;
        border-radius:50%; background:rgba(255,255,255,.08); }
    .lb-stat-ico { width: 42px; height: 42px; border-radius: 11px; background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .lb-stat-num { font-size: 26px; font-weight: 800; color: #fff; line-height: 1; }
    .lb-stat-lbl { font-size: 12px; color: rgba(255,255,255,.8); margin-top: 3px; }

    /* Card */
    .lb-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
    .lb-card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid #f3f4f6; }
    .lb-card-title { font-size: 14px; font-weight: 700; color: #111827; }
    .lb-card-body  { padding: 18px; }

    /* Form fields */
    .form-field { display: flex; flex-direction: column; }
    .form-field label { font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 5px; letter-spacing: .05em; text-transform: uppercase; }
    .form-field select { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; font-family: inherit; background: #fff; color: #111827;
        outline: none; transition: border-color .15s; box-sizing: border-box; }
    .form-field select:focus { border-color: #6366f1; }

    /* Filter grid */
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; align-items: end; }

    /* Buttons */
    .btn-pv { display: inline-flex; align-items: center; gap: 6px; padding: 9px 14px;
        border-radius: 8px; border: none; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all .15s; font-family: inherit; text-decoration: none; }
    .btn-ghost { background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; }
    .btn-ghost:hover { background: #e2e8f0; color: #374151; }

    /* Rekap grid */
    .rekap-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 14px; margin-bottom: 1.25rem; }
    @media (max-width: 640px) { .rekap-grid { grid-template-columns: 1fr; } }

    /* Inner tables (rekap) */
    .rekap-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .rekap-table th { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase;
        letter-spacing: .04em; padding: 10px 16px; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6; text-align: left; white-space: nowrap; }
    .rekap-table td { padding: 10px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; color: #374151; }
    .rekap-table tr:last-child td { border-bottom: none; }
    .rekap-table tr:hover td { background: #f8faff; }

    /* Detail table */
    .lb-table-wrap { overflow-x: auto; }
    .lb-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 700px; }
    .lb-table th { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase;
        letter-spacing: .05em; padding: 10px 16px; background: #f9fafb;
        border-bottom: 1px solid #f3f4f6; text-align: left; white-space: nowrap; }
    .lb-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .lb-table tr:last-child td { border-bottom: none; }
    .lb-table tr:hover td { background: #f8faff; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 99px; white-space: nowrap; }
    .badge-count-green { background: #E1F5EE; color: #0F6E56; }
    .badge-count-blue  { background: #E6F1FB; color: #0C447C; }

    /* Avatar */
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #534AB7, #3C3489);
        display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 12px; flex-shrink: 0; }

    /* Mobile card list for detail */
    .lb-detail-list { display: none; }
    @media (max-width: 640px) {
        .lb-table-wrap { display: none; }
        .lb-detail-list { display: block; }
    }
    .lb-detail-item { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; }
    .lb-detail-item:last-child { border-bottom: none; }
    .lb-detail-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
    .lb-detail-name { font-weight: 700; font-size: 13px; color: #111827; }
    .lb-detail-sub  { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .lb-detail-bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; flex-wrap: wrap; gap: 6px; }

    .empty-state { text-align: center; padding: 2.5rem 1rem; }
    .empty-ico { width: 52px; height: 52px; background: #f3f4f6; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }

    /* Progress bar */
    .progress-bar { height: 5px; background: #f3f4f6; border-radius: 99px; margin-top: 4px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #1D9E75, #0F6E56); }
</style>

@php
    $totalSemua     = $data->count();
    $totalDisetujui = $data->where('status','disetujui')->count();
    $totalPending   = $data->where('status','pending')->count();
    $totalHari      = $data->where('status','disetujui')->sum('jumlah_hari_disetujui');
@endphp

<div class="lb-page">

    {{-- Header --}}
    <div class="lb-header">
        <h1><i class="bi bi-bar-chart-fill me-2" style="color:#534AB7;font-size:20px;"></i>Laporan Izin Berencana</h1>
        <p>Rekap dan statistik pengajuan izin siswa</p>
    </div>

    {{-- Filter --}}
    <div class="lb-card">
        <div class="lb-card-body">
            <form method="GET" action="{{ route('izin.laporan') }}">
                <div class="filter-grid">

                    <div class="form-field">
                        <label>Semester</label>
                        <select name="semester_id" onchange="this.form.submit()">
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->nama }}{{ $sem->is_aktif ? ' ✓' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field">
                        <label>Status</label>
                        <select name="status" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            @foreach(['pending' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'dibatalkan' => 'Dibatalkan'] as $k => $v)
                                <option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field">
                        <label>Jenis</label>
                        <select name="jenis" onchange="this.form.submit()">
                            <option value="">Semua Jenis</option>
                            @foreach(App\Models\IzinBerencana::jenisList() as $k => $v)
                                <option value="{{ $k }}" {{ request('jenis') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field">
                        <label>Bulan</label>
                        <select name="bulan" onchange="this.form.submit()">
                            <option value="">Semua Bulan</option>
                            @foreach(array_combine(range(1,12), array_map(fn($m) => \Carbon\Carbon::create()->month($m)->translatedFormat('F'), range(1,12))) as $k => $v)
                                <option value="{{ $k }}" {{ request('bulan') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:flex;align-items:flex-end;">
                        <a href="{{ route('izin.laporan') }}" class="btn-pv btn-ghost" style="width:100%;justify-content:center;">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="lb-stat-grid">
        <div class="lb-stat c-purple">
            <div class="lb-stat-ico"><i class="bi bi-file-earmark-text-fill" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="lb-stat-num">{{ $totalSemua }}</div>
                <div class="lb-stat-lbl">Total Pengajuan</div>
            </div>
        </div>
        <div class="lb-stat c-green">
            <div class="lb-stat-ico"><i class="bi bi-check-circle-fill" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="lb-stat-num">{{ $totalDisetujui }}</div>
                <div class="lb-stat-lbl">Disetujui</div>
            </div>
        </div>
        <div class="lb-stat c-amber">
            <div class="lb-stat-ico"><i class="bi bi-hourglass-split" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="lb-stat-num">{{ $totalPending }}</div>
                <div class="lb-stat-lbl">Menunggu</div>
            </div>
        </div>
        <div class="lb-stat c-blue">
            <div class="lb-stat-ico"><i class="bi bi-calendar-day-fill" style="color:#fff;font-size:18px;"></i></div>
            <div>
                <div class="lb-stat-num">{{ $totalHari }}</div>
                <div class="lb-stat-lbl">Total Hari Izin</div>
            </div>
        </div>
    </div>

    {{-- Rekap per Jenis & per Bulan --}}
    <div class="rekap-grid">

        {{-- Rekap per Jenis --}}
        <div class="lb-card">
            <div class="lb-card-head">
                <span class="lb-card-title">
                    <i class="bi bi-pie-chart-fill me-2" style="color:#534AB7;"></i>Rekap per Jenis
                </span>
            </div>
            <table class="rekap-table">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th style="text-align:center;">Disetujui</th>
                        <th style="text-align:center;">Hari</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(App\Models\IzinBerencana::jenisList() as $k => $label)
                    @php $r = $rekapJenis[$k] ?? ['total'=>0,'disetujui'=>0,'hari'=>0]; @endphp
                    <tr>
                        <td style="font-size:13px;color:#374151;">{{ $label }}</td>
                        <td style="text-align:center;">
                            <span class="badge badge-count-green">{{ $r['disetujui'] }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-weight:700;color:#0C447C;font-size:13px;">{{ $r['hari'] }}</span>
                            <span style="font-size:11px;color:#9ca3af;">h</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Rekap per Bulan --}}
        <div class="lb-card">
            <div class="lb-card-head">
                <span class="lb-card-title">
                    <i class="bi bi-calendar3 me-2" style="color:#534AB7;"></i>Rekap per Bulan
                </span>
            </div>
            <table class="rekap-table">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th style="text-align:center;">Pengajuan</th>
                        <th style="text-align:center;">Hari</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapBulan as $bulan => $r)
                    <tr>
                        <td style="font-weight:600;font-size:13px;color:#111827;">
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                        </td>
                        <td style="text-align:center;">
                            <span class="badge badge-count-green">{{ $r['total'] }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-weight:700;color:#0C447C;font-size:13px;">{{ $r['hari'] }}</span>
                            <span style="font-size:11px;color:#9ca3af;">h</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state" style="padding:1.5rem;">
                                <p style="font-size:13px;color:#9ca3af;margin:0;">Tidak ada data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- Detail Table --}}
    <div class="lb-card">
        <div class="lb-card-head">
            <span class="lb-card-title">
                <i class="bi bi-table me-2" style="color:#534AB7;"></i>Detail Pengajuan
            </span>
            <span style="font-size:12px;color:#6b7280;background:#f3f4f6;padding:3px 12px;border-radius:99px;font-weight:600;">
                {{ $data->count() }} data
            </span>
        </div>

        {{-- Desktop table --}}
        <div class="lb-table-wrap">
            <table class="lb-table">
                <thead>
                    <tr>
                        <th>No. Izin</th>
                        <th>Siswa</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th style="text-align:center;width:60px;">Hari</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $izin)
                    <tr>
                        <td>
                            <a href="{{ route('izin.show', $izin) }}"
                               style="color:#534AB7;font-weight:700;font-family:monospace;font-size:12px;text-decoration:none;">
                                {{ $izin->nomor_izin }}
                            </a>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="avatar">{{ strtoupper(substr($izin->siswa?->nama ?? '?', 0, 1)) }}</div>
                                <div>
                                    <div style="font-weight:700;font-size:13px;color:#111827;">{{ $izin->siswa?->nama }}</div>
                                    <div style="font-size:11px;color:#9ca3af;">{{ $izin->siswa?->nama_rombel }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;color:#374151;">
                            {{ App\Models\IzinBerencana::jenisList()[$izin->jenis] ?? $izin->jenis }}
                        </td>
                        <td style="font-size:12px;color:#6b7280;white-space:nowrap;">
                            {{ $izin->tanggal_mulai->translatedFormat('d/m/Y') }}
                        </td>
                        <td style="text-align:center;font-weight:700;color:#111827;">
                            {{ $izin->jumlah_hari_disetujui ?: $izin->jumlah_hari }}
                        </td>
                        <td>
                            <span style="background:{{ $izin->statusBg() }};color:{{ $izin->statusColor() }};font-weight:700;padding:3px 10px;border-radius:99px;font-size:11px;">
                                {{ $izin->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-ico"><i class="bi bi-file-earmark-x" style="font-size:22px;color:#9ca3af;"></i></div>
                                <p style="font-size:13px;color:#9ca3af;margin:0;">Tidak ada data izin</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list --}}
        <div class="lb-detail-list">
            @forelse($data as $izin)
            <div class="lb-detail-item">
                <div class="lb-detail-top">
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <div class="avatar" style="margin-top:1px;">
                            {{ strtoupper(substr($izin->siswa?->nama ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div class="lb-detail-name">{{ $izin->siswa?->nama }}</div>
                            <div class="lb-detail-sub">
                                {{ $izin->siswa?->nama_rombel }}
                                &bull; {{ $izin->tanggal_mulai->translatedFormat('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    <span style="font-weight:800;color:#111827;font-size:18px;flex-shrink:0;">
                        {{ $izin->jumlah_hari_disetujui ?: $izin->jumlah_hari }}h
                    </span>
                </div>
                <div class="lb-detail-bottom">
                    <div style="display:flex;gap:5px;flex-wrap:wrap;align-items:center;">
                        <a href="{{ route('izin.show', $izin) }}"
                           style="font-family:monospace;font-size:11px;font-weight:700;color:#534AB7;text-decoration:none;">
                            {{ $izin->nomor_izin }}
                        </a>
                        <span style="color:#d1d5db;">·</span>
                        <span style="font-size:12px;color:#6b7280;">
                            {{ App\Models\IzinBerencana::jenisList()[$izin->jenis] ?? $izin->jenis }}
                        </span>
                    </div>
                    <span style="background:{{ $izin->statusBg() }};color:{{ $izin->statusColor() }};font-weight:700;padding:3px 10px;border-radius:99px;font-size:11px;">
                        {{ $izin->statusLabel() }}
                    </span>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-ico"><i class="bi bi-file-earmark-x" style="font-size:22px;color:#9ca3af;"></i></div>
                <p style="font-size:13px;color:#9ca3af;margin:0;">Tidak ada data izin</p>
            </div>
            @endforelse
        </div>

    </div>

</div>
@endsection