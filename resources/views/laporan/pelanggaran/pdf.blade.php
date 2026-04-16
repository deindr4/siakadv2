<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Pelanggaran Siswa</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 10pt;
        color: #000;
        background: #fff;
        width: 100%;
    }

    /* ===== KOP ===== */
    .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    .kop-table td { vertical-align: middle; padding: 0; }
    .kop-logo-cell { width: 75px; text-align: center; }
    .kop-logo { width: 65px; height: 65px; object-fit: contain; }
    .kop-text-cell { text-align: center; padding: 0 8px; }
    .kop-instansi { font-size: 9pt; text-transform: uppercase; letter-spacing: 0.5px; }
    .kop-nama     { font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; line-height: 1.2; }
    .kop-alamat   { font-size: 8.5pt; margin-top: 2px; }
    /*.kop-divider  { border: none; border-top: 0px double #ffffff; margin: 6px 0 10px; }*/

    .kop-image-wrap { text-align: center; margin-bottom: 4px; }
    .kop-image-wrap img { max-height: 200px; max-width: 150%; object-fit: contain; display: block; margin: 0 auto; }

    /* ===== JUDUL ===== */
    .judul { text-align: center; margin-bottom: 12px; }
    .judul h2 { font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; text-decoration: underline; }
    .judul p  { font-size: 9.5pt; margin-top: 4px; }

    /* ===== RINGKASAN ===== */
    .ringkasan-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; padding: 8px; }
    .ringkasan-table td {
        width: 20%;
        text-align: center;
        border: 1px solid #ccc;
        padding: 6px 4px;
        vertical-align: middle;
    }
    .ringkasan-table .ring-label { font-size: 8.5pt; color: #555; }
    .ringkasan-table .ring-value { font-size: 16pt; font-weight: bold; margin-top: 2px; }

    /* ===== TABEL UTAMA ===== */
    .tabel-main { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 8.5pt; padding: 8px; }
    .tabel-main th {
        background: #7f1d1d;
        color: #fff;
        padding: 5px 4px;
        text-align: center;
        border: 1px solid #000;
    }
    .tabel-main td { padding: 4px 5px; border: 1px solid #aaa; vertical-align: top; }
    .tabel-main tr:nth-child(even) td { background: #fef9f9; }
    .tabel-main tfoot td { background: #fde8e8; font-weight: bold; }
    .center { text-align: center; }
    .bold   { font-weight: bold; }

    .badge { padding: 1px 5px; border-radius: 8px; font-size: 8pt; }
    .badge-ringan { background: #fef3c7; color: #92400e; }
    .badge-sedang { background: #fed7aa; color: #9a3412; }
    .badge-berat  { background: #fee2e2; color: #991b1b; }

    /* ===== TTD - TABLE LAYOUT ===== */
    .ttd-outer { width: 100%; margin-top: 24px; page-break-inside: avoid; }
    .ttd-inner { width: 100%; border-collapse: collapse; }
    .ttd-inner td { width: 50%; text-align: center; vertical-align: top; padding: 0 20px; font-size: 10pt; }
    .ttd-inner td p { margin-bottom: 2px; line-height: 1.6; }
    .ttd-ruang { height: 65px; }
    .ttd-garis {
        display: inline-block;
        min-width: 180px;
        border-top: 1.5px solid #000;
        padding-top: 3px;
        font-weight: bold;
        font-size: 10.5pt;
        margin-top: 2px;
    }
    .ttd-nip { font-size: 9pt; margin-top: 2px; }

    /* ===== KETERANGAN ===== */
    .keterangan { font-size: 8.5pt; margin-top: 8px; border: 1px solid #ccc; padding: 6px 10px; background: #fafafa; }

    /* ===== FOOTER ===== */
    .footer { font-size: 7.5pt; color: #555; text-align: center; margin-top: 16px; border-top: 1px solid #ccc; padding-top: 5px; }
</style>
</head>
<body>

@php
    $kopMode     = $settings['kop_mode'] ?? 'auto';
    $namaSekolah = $settings['nama_sekolah'] ?? 'NAMA SEKOLAH';
    $bulanLabel  = $bulan ? \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') : 'Semua Bulan';

    $totalRingan = $pelanggaran->filter(fn($p) => $p->jenisPelanggaran?->kategori === 'ringan')->count();
    $totalSedang = $pelanggaran->filter(fn($p) => $p->jenisPelanggaran?->kategori === 'sedang')->count();
    $totalBerat  = $pelanggaran->filter(fn($p) => $p->jenisPelanggaran?->kategori === 'berat')->count();
    $totalPoin   = $pelanggaran->sum('poin');

    $tanggalTtd  = $ttd['tanggal_ttd']
        ? \Carbon\Carbon::parse($ttd['tanggal_ttd'])->translatedFormat('d F Y')
        : now()->translatedFormat('d F Y');
@endphp

{{-- KOP --}}
@if($kopMode === 'image' && !empty($settings['kop_surat']))
    <div class="kop-image-wrap">
        <img src="{{ storage_path('app/public/'.$settings['kop_surat']) }}">
    </div>
@else
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                @if(!empty($settings['logo']))
                    <img class="kop-logo" src="{{ storage_path('app/public/'.$settings['logo']) }}">
                @endif
            </td>
            <td class="kop-text-cell">
                <p class="kop-instansi">PEMERINTAH PROVINSI {{ strtoupper($settings['provinsi'] ?? '') }}</p>
                <p class="kop-nama">{{ $namaSekolah }}</p>
                <p class="kop-alamat">
                    {{ $settings['alamat'] ?? '' }}
                    @if(!empty($settings['kecamatan'])), Kec. {{ $settings['kecamatan'] }}@endif
                    @if(!empty($settings['kabupaten'])), {{ $settings['kabupaten'] }}@endif
                    @if(!empty($settings['kode_pos'])) {{ $settings['kode_pos'] }}@endif
                </p>
                <p class="kop-alamat">
                    @if(!empty($settings['telepon']))Telp: {{ $settings['telepon'] }}@endif
                    @if(!empty($settings['email'])) | Email: {{ $settings['email'] }}@endif
                    @if(!empty($settings['website'])) | {{ $settings['website'] }}@endif
                </p>
            </td>
            <td class="kop-logo-cell"></td>
        </tr>
    </table>
@endif
{{-- <hr class="kop-divider">--}}

{{-- JUDUL --}}
<div class="judul">
    <h2>Laporan Pelanggaran Siswa</h2>
    <p>{{ $semester?->nama ?? 'Semua Semester' }} &mdash; {{ $bulanLabel }}</p>
</div>

{{-- RINGKASAN --}}
<table class="ringkasan-table">
    <tr>
        <td style="background:#fef2f2;">
            <p class="ring-label">Total Pelanggaran</p>
            <p class="ring-value" style="color:#dc2626;">{{ $pelanggaran->count() }}</p>
        </td>
        <td style="background:#fef9c3;">
            <p class="ring-label">Ringan</p>
            <p class="ring-value" style="color:#ca8a04;">{{ $totalRingan }}</p>
        </td>
        <td style="background:#ffedd5;">
            <p class="ring-label">Sedang</p>
            <p class="ring-value" style="color:#ea580c;">{{ $totalSedang }}</p>
        </td>
        <td style="background:#fee2e2;">
            <p class="ring-label">Berat</p>
            <p class="ring-value" style="color:#dc2626;">{{ $totalBerat }}</p>
        </td>
        <td style="background:#f0f9ff;">
            <p class="ring-label">Total Poin</p>
            <p class="ring-value" style="color:#0369a1;">{{ $totalPoin }}</p>
        </td>
    </tr>
</table>

{{-- TABEL PELANGGARAN --}}
<table class="tabel-main">
    <thead>
        <tr>
            <th style="width:22px;">No</th>
            <th style="width:62px;">Tanggal</th>
            <th>Nama Siswa</th>
            <th style="width:70px;">NISN</th>
            <th style="width:65px;">Kelas</th>
            <th>Jenis Pelanggaran</th>
            <th style="width:52px;">Kategori</th>
            <th style="width:34px;">Poin</th>
            <th>Tindakan</th>
            <th style="width:48px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pelanggaran as $i => $p)
        @php $kat = $p->jenisPelanggaran?->kategori ?? 'ringan'; @endphp
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td class="center">{{ $p->tanggal?->format('d/m/Y') }}</td>
            <td class="bold">{{ $p->siswa?->nama }}</td>
            <td class="center">{{ $p->siswa?->nisn ?? '-' }}</td>
            <td class="center">{{ $p->siswa?->nama_rombel }}</td>
            <td>{{ $p->jenisPelanggaran?->nama }}</td>
            <td class="center"><span class="badge badge-{{ $kat }}">{{ ucfirst($kat) }}</span></td>
            <td class="center bold" style="color:#dc2626;">{{ $p->poin }}</td>
            <td style="font-size:8pt;">{{ $p->tindakan ?? '-' }}</td>
            <td class="center" style="font-size:8pt;">{{ ucfirst($p->status) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="center" style="padding:14px;">Tidak ada data pelanggaran</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" style="text-align:right;">TOTAL POIN KESELURUHAN:</td>
            <td class="center bold" style="color:#dc2626;">{{ $totalPoin }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

{{-- KETERANGAN --}}
<div class="keterangan">
    <span style="font-weight:bold;">Keterangan Kategori:</span>
    &nbsp;&nbsp; 🟡 Ringan : 1&ndash;24 poin
    &nbsp;&nbsp; 🟠 Sedang : 25&ndash;49 poin
    &nbsp;&nbsp; 🔴 Berat : 50+ poin
</div>

{{-- TTD - KEPSEK KANAN --}}
<div class="ttd-outer">
    <table class="ttd-inner">
        <tr>
            {{-- KIRI: kosong / bisa diisi Koordinator BK --}}
            <td>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <div class="ttd-ruang"></div>
                <p><span class="ttd-garis" style="min-width:160px;">&nbsp;</span></p>
            </td>
            {{-- KANAN: KEPALA SEKOLAH --}}
            <td>
                <p>{{ $ttd['tempat_ttd'] ?? '' }}, {{ $tanggalTtd }}</p>
                <p>Kepala {{ $namaSekolah }}</p>
                <div class="ttd-ruang"></div>
                <p><span class="ttd-garis">{{ $ttd['nama_kepsek'] ?? '.....................................' }}</span></p>
                @if(!empty($ttd['golongan_kepsek']))
                <p class="ttd-nip">Gol. {{ $ttd['golongan_kepsek'] }}</p>
                <p class="ttd-nip">NIP. {{ $ttd['nip_kepsek'] ?? '-' }}</p>
                @endif
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} &nbsp;|&nbsp; {{ $namaSekolah }}
</div>

</body>
</html>
