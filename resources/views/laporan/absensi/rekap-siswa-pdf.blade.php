{{-- ============================================================ --}}
{{-- FILE: resources/views/laporan/absensi/rekap-siswa-pdf.blade.php --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Absensi Per Siswa</title>
<style>
    @page { margin: 1.5cm 2cm; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Times New Roman',serif; font-size:10pt; color:#000; padding:8; }
    .kop-table { width:100%; border-collapse:collapse; margin-bottom:4px; }
    .kop-table td { vertical-align:middle; padding:0; }
    .kop-logo-cell { width:75px; text-align:center; }
    .kop-logo { width:65px; height:65px; object-fit:contain; }
    .kop-text-cell { text-align:center; padding:0 8px; }
    .kop-instansi { font-size:9pt; text-transform:uppercase; }
    .kop-nama { font-size:15pt; font-weight:bold; text-transform:uppercase; letter-spacing:1px; }
    .kop-alamat { font-size:8.5pt; margin-top:2px; }
    /*.kop-divider { border:none; border-top:3px double #000; margin:6px 0 10px; }*/
    .kop-image-wrap { text-align:center; margin-bottom:4px; }
    .kop-image-wrap img { max-height:200px; max-width:150%; object-fit:contain; display:block; margin:0 auto; }
    .judul { text-align:center; margin-bottom:12px; }
    .judul h2 { font-size:12pt; font-weight:bold; text-transform:uppercase; text-decoration:underline; }
    .judul p { font-size:9.5pt; margin-top:4px; }
    .tabel { width:100%; border-collapse:collapse; margin-bottom:14px; font-size:9pt; }
    .tabel th { background:#1e3a5f; color:#fff; padding:5px 6px; text-align:center; border:1px solid #000; }
    .tabel td { padding:4px 6px; border:1px solid #aaa; vertical-align:middle; }
    .tabel tr:nth-child(even) td { background:#f5f8ff; }
    .center { text-align:center; }
    .bold { font-weight:bold; }
    .ttd-outer { width:100%; margin-top:20px; }
    .ttd-inner { width:100%; border-collapse:collapse; }
    .ttd-inner td { width:50%; text-align:center; vertical-align:top; padding:0 20px; font-size:10pt; }
    .ttd-inner td p { margin-bottom:2px; line-height:1.6; }
    .ttd-ruang { height:65px; }
    .ttd-garis { display:inline-block; min-width:180px; border-top:1.5px solid #000; padding-top:3px; font-weight:bold; font-size:10.5pt; }
    .ttd-nip { font-size:9pt; margin-top:2px; }
    .footer { font-size:7.5pt; color:#555; text-align:center; margin-top:16px; border-top:5px solid #ccc; padding-top:5px; }
    .badge { padding:1px 6px; border-radius:8px; font-size:8pt; font-weight:bold; }
</style>
</head>
<body>
@php
    $kopMode     = $settings['kop_mode'] ?? 'auto';
    $namaSekolah = $settings['nama_sekolah'] ?? 'NAMA SEKOLAH';
    $tanggalTtd  = $ttd['tanggal_ttd'] ? \Carbon\Carbon::parse($ttd['tanggal_ttd'])->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
@endphp

@if($kopMode === 'image' && !empty($settings['kop_surat']))
    <div class="kop-image-wrap"><img src="{{ storage_path('app/public/'.$settings['kop_surat']) }}"></div>
@else
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">@if(!empty($settings['logo']))<img class="kop-logo" src="{{ storage_path('app/public/'.$settings['logo']) }}">@endif</td>
            <td class="kop-text-cell">
                <p class="kop-instansi">PEMERINTAH PROVINSI {{ strtoupper($settings['provinsi'] ?? '') }}</p>
                <p class="kop-nama">{{ $namaSekolah }}</p>
                <p class="kop-alamat">{{ $settings['alamat'] ?? '' }}@if(!empty($settings['kabupaten'])), {{ $settings['kabupaten'] }}@endif</p>
            </td>
            <td class="kop-logo-cell"></td>
        </tr>
    </table>
@endif
<hr class="kop-divider">

<div class="judul">
    <h2>Rekap Absensi Siswa</h2>
    <p>{{ $semester?->nama ?? 'Semua Semester' }} @if($rombel) &mdash; {{ $rombel->nama_rombel }}@endif</p>
</div>

<table class="tabel">
    <thead>
        <tr>
            <th style="width:25px;">No</th>
            <th>Nama Siswa</th>
            <th style="width:65px;">NISN</th>
            <th style="width:40px;">Kelas</th>
            <th style="width:25px;">H</th>
            <th style="width:25px;">S</th>
            <th style="width:25px;">I</th>
            <th style="width:25px;">A</th>
            <th style="width:25px;">D</th>
            <th style="width:40px;">Total</th>
            <th style="width:50px;">%Hadir</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekapSiswa as $i => $r)
        @php $pct = $r->total > 0 ? round(($r->hadir / $r->total) * 100) : 0; @endphp
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td class="bold">{{ $r->siswa->nama }}</td>
            <td class="center">{{ $r->siswa->nisn ?? '-' }}</td>
            <td class="center">{{ $r->siswa->nama_rombel }}</td>
            <td class="center bold" style="color:#16a34a;">{{ $r->hadir }}</td>
            <td class="center bold" style="color:#0284c7;">{{ $r->sakit }}</td>
            <td class="center bold" style="color:#d97706;">{{ $r->izin }}</td>
            <td class="center bold" style="color:#dc2626;">{{ $r->alpa }}</td>
            <td class="center bold" style="color:#7c3aed;">{{ $r->dispensasi }}</td>
            <td class="center">{{ $r->total }}</td>
            <td class="center bold" style="color:{{ $pct >= 90 ? '#16a34a' : ($pct >= 75 ? '#d97706' : '#dc2626') }};">{{ $pct }}%</td>
        </tr>
        @empty
        <tr><td colspan="11" class="center" style="padding:14px;">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="ttd-outer">
    <table class="ttd-inner">
        <tr>
            <td></td>
            <td>
                <p>{{ $ttd['tempat_ttd'] ?? '' }}, {{ $tanggalTtd }}</p>
                <p>Kepala {{ $namaSekolah }}</p>
                <div class="ttd-ruang"></div>
                <span class="ttd-garis">{{ $ttd['nama_kepsek'] ?? '.....................................' }}</span>

                @if(!empty($ttd['golongan_kepsek']))<p class="ttd-nip">Gol. {{ $ttd['golongan_kepsek'] }}</p>@endif
                <p class="ttd-nip">NIP. {{ $ttd['nip_kepsek'] ?? '-' }}</p>
            </td>
        </tr>
    </table>
</div>
<div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} | {{ $namaSekolah }}</div>
</body>
</html>
