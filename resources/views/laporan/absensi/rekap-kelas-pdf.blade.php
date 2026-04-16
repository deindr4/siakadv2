<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Absensi Per Kelas</title>
<style>
<?php echo '@page { margin: 1.5cm 2cm; }'; ?>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Times New Roman',serif; font-size:10pt; color:#000; padding:8;}
.kop-table { width:100%; border-collapse:collapse; margin-bottom:4px; }
.kop-table td { vertical-align:middle; padding:0; }
.kop-logo-cell { width:75px; text-align:center; }
.kop-logo { width:65px; height:65px; object-fit:contain; }
.kop-text-cell { text-align:center; padding:0 8px; }
.kop-instansi { font-size:9pt; text-transform:uppercase; }
.kop-nama { font-size:15pt; font-weight:bold; text-transform:uppercase; letter-spacing:1px; }
.kop-alamat { font-size:8.5pt; margin-top:2px; }
.kop-divider { border:none; border-top:3px double #000; margin:6px 0 10px; }
.kop-image-wrap { text-align:center; margin-bottom:4px; }
.kop-image-wrap img { max-height:200px; max-width:100%; object-fit:contain; display:block; margin:0 auto; }
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
.footer { font-size:7.5pt; color:#555; text-align:center; margin-top:16px; border-top:1px solid #ccc; padding-top:5px; }
</style>
</head>
<body>
@php
    $kopMode     = $settings['kop_mode'] ?? 'auto';
    $namaSekolah = $settings['nama_sekolah'] ?? 'NAMA SEKOLAH';
    $tanggalTtd  = $ttd['tanggal_ttd'] ? \Carbon\Carbon::parse($ttd['tanggal_ttd'])->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
    $bulanLabel  = $bulan ? \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') : 'Semua Bulan';

    $logoPath    = !empty($settings['logo']) ? storage_path('app/public/'.$settings['logo']) : null;
    $kopPath     = !empty($settings['kop_surat']) ? storage_path('app/public/'.$settings['kop_surat']) : null;
    $alamatFull  = trim(($settings['alamat'] ?? '') . (!empty($settings['kabupaten']) ? ', '.$settings['kabupaten'] : ''));
    $kontakLine  = trim(
        (!empty($settings['telepon']) ? 'Telp: '.$settings['telepon'] : '') .
        (!empty($settings['email']) ? ' | '.$settings['email'] : '')
    );
    $provinsi    = strtoupper($settings['provinsi'] ?? '');
@endphp

<?php if($kopMode === 'image' && $kopPath): ?>
<div class="kop-image-wrap"><img src="{{ $kopPath }}"></div>
<?php else: ?>
<table class="kop-table">
    <tr>
        <td class="kop-logo-cell">
            <?php if($logoPath): ?><img class="kop-logo" src="{{ $logoPath }}">  <?php endif; ?>
        </td>
        <td class="kop-text-cell">
            <p class="kop-instansi">PEMERINTAH PROVINSI {{ $provinsi }}</p>
            <p class="kop-nama">{{ $namaSekolah }}</p>
            <p class="kop-alamat">{{ $alamatFull }}</p>
            <?php if($kontakLine): ?><p class="kop-alamat">{{ $kontakLine }}</p><?php endif; ?>
        </td>
        <td class="kop-logo-cell"></td>
    </tr>
</table>
<?php endif; ?>
<hr class="kop-divider">

<div class="judul">
    <h2>Rekap Absensi Per Kelas</h2>
    <p>{{ $semester?->nama ?? 'Semua Semester' }} &mdash; {{ $bulanLabel }}</p>
</div>

<table class="tabel">
    <thead>
        <tr>
            <th style="width:25px;">No</th>
            <th>Kelas</th>
            <th>Bulan</th>
            <th style="width:55px;">Total Hari</th>
            <th style="width:45px;">Hadir</th>
            <th style="width:40px;">Sakit</th>
            <th style="width:35px;">Izin</th>
            <th style="width:35px;">Alpa</th>
            <th style="width:55px;">Dispensasi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekap as $i => $r)
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td class="bold">{{ $r->nama_rombel }}</td>
            <td>{{ \Carbon\Carbon::create()->month($r->bulan)->translatedFormat('F') }} {{ $r->tahun }}</td>
            <td class="center">{{ $r->total_hari }}</td>
            <td class="center bold" style="color:#16a34a;">{{ $r->hadir }}</td>
            <td class="center bold" style="color:#0284c7;">{{ $r->sakit }}</td>
            <td class="center bold" style="color:#d97706;">{{ $r->izin }}</td>
            <td class="center bold" style="color:#dc2626;">{{ $r->alpa }}</td>
            <td class="center bold" style="color:#7c3aed;">{{ $r->dispensasi }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="center" style="padding:14px;">Tidak ada data</td></tr>
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
                <p><span class="ttd-garis">{{ $ttd['nama_kepsek'] ?? '.....................................' }}</span></p>

                <?php if(!empty($ttd['golongan_kepsek'])): ?><p class="ttd-nip">Gol. {{ $ttd['golongan_kepsek'] }}</p><?php endif; ?>
                <p class="ttd-nip">NIP. {{ $ttd['nip_kepsek'] ?? '-' }}</p>
            </td>
        </tr>
    </table>
</div>
<div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} | {{ $namaSekolah }}</div>
</body>
</html>
