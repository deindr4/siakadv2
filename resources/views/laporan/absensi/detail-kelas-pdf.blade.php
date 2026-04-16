<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Hadir Siswa</title>
<style>
<?php echo '@page { margin: 1.5cm 2cm; }'; ?>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Times New Roman',serif; font-size:9pt; color:#000; padding:15; }
.kop-table { width:100%; border-collapse:collapse; margin-bottom:4px; }
.kop-table td { vertical-align:middle; padding:0; }
.kop-logo-cell { width:65px; text-align:center; }
.kop-logo { width:58px; height:58px; object-fit:contain; }
.kop-text-cell { text-align:center; padding:0 6px; }
.kop-instansi { font-size:8.5pt; text-transform:uppercase; }
.kop-nama { font-size:13pt; font-weight:bold; text-transform:uppercase; letter-spacing:1px; }
.kop-alamat { font-size:8pt; margin-top:2px; }
/*.kop-divider { border:none; border-top:3px double #000; margin:5px 0 8px; }*/
.kop-image-wrap { text-align:center; margin-bottom:4px; }
.kop-image-wrap img { max-height:200px; max-width:100%; object-fit:contain; display:block; margin:0 auto; }
.judul { text-align:center; margin-bottom:10px; }
.judul h2 { font-size:11pt; font-weight:bold; text-transform:uppercase; text-decoration:underline; }
.judul p { font-size:9pt; margin-top:3px; }
.info-table { width:100%; border-collapse:collapse; border:1px solid #000; margin-bottom:10px; font-size:9pt; }
.info-table td { padding:3px 8px; }
.info-table td.label { font-weight:bold; width:100px; }
.info-table td.sep { width:10px; }
.tabel { width:100%; border-collapse:collapse; margin-bottom:12px; font-size:8pt; }
.tabel th { background:#1e3a5f; color:#fff; padding:4px 3px; text-align:center; border:1px solid #000; }
.tabel td { padding:3px 2px; border:1px solid #aaa; text-align:center; vertical-align:middle; }
.tabel td.nama { text-align:left; padding-left:5px; font-size:8.5pt; min-width:130px; }
.tabel tr:nth-child(even) td { background:#f8f8f8; }
.st-H { background:#dcfce7; color:#16a34a; font-weight:bold; }
.st-S { background:#e0f2fe; color:#0284c7; font-weight:bold; }
.st-I { background:#fef3c7; color:#d97706; font-weight:bold; }
.st-A { background:#fee2e2; color:#dc2626; font-weight:bold; }
.st-D { background:#ede9fe; color:#7c3aed; font-weight:bold; }
.ttd-outer { width:100%; margin-top:16px; }
.ttd-inner { width:100%; border-collapse:collapse; }
.ttd-inner td { width:50%; text-align:center; vertical-align:top; padding:0 16px; font-size:9.5pt; }
.ttd-inner td p { margin-bottom:2px; line-height:1.5; }
.ttd-ruang { height:55px; }
.ttd-garis { display:inline-block; min-width:160px; border-top:1.5px solid #000; padding-top:3px; font-weight:bold; font-size:10pt; }
.ttd-nip { font-size:8.5pt; margin-top:2px; }
.footer { font-size:7pt; color:#555; text-align:center; margin-top:12px; border-top:1px solid #ccc; padding-top:4px; }
.ket-table { width:auto; border-collapse:collapse; font-size:8pt; margin-bottom:10px; }
.ket-table td { padding:2px 8px; border:1px solid #ccc; }
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

    // Build map absensi: [absensi_harian_id][siswa_id] = status
    $absensiMap = [];
    foreach($absensiList as $ah) {
        foreach($ah->absensiSiswa as $as) {
            $absensiMap[$ah->id][$as->siswa_id] = $as->status;
        }
    }
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
    <h2>Daftar Hadir Siswa</h2>
    <p>Kelas {{ $rombel->nama_rombel }} &mdash; {{ $semester?->nama }} &mdash; {{ $bulanLabel }}</p>
</div>

<table class="info-table">
    <tr>
        <td class="label">Kelas</td><td class="sep">:</td><td>{{ $rombel->nama_rombel }}</td>
        <td class="label">Wali Kelas</td><td class="sep">:</td><td>{{ $rombel->wali_kelas ?? ($ttd['nama_wali'] ?? '-') }}</td>
    </tr>
    <tr>
        <td class="label">Semester</td><td class="sep">:</td><td>{{ $semester?->nama }}</td>
        <td class="label">Jumlah Siswa</td><td class="sep">:</td><td>{{ $siswas->count() }} siswa</td>
    </tr>
</table>

<table class="tabel">
    <thead>
        <tr>
            <th style="width:20px;">No</th>
            <th style="text-align:left;padding-left:5px;min-width:100px;">Nama Siswa</th>
            @foreach($absensiList as $ah)
            <th style="min-width:22px;font-size:7.5pt;">{{ $ah->tanggal->format('d') }}</th>
            @endforeach
            <th style="width:22px;background:#16a34a;">H</th>
            <th style="width:22px;background:#0284c7;">S</th>
            <th style="width:22px;background:#d97706;">I</th>
            <th style="width:22px;background:#dc2626;">A</th>
            <th style="width:22px;background:#7c3aed;">D</th>
        </tr>
        <tr style="background:#2d4a6f;">
            <td colspan="2" style="border:1px solid #000000;font-size:7pt;text-align:center;color:#cbd5e1;">Bulan: {{ $bulanLabel }}</td>
            @foreach($absensiList as $ah)
            <td style="border:1px solid #000000;font-size:6.5pt;text-align:center;color:#cbd5e1;">{{ $ah->tanggal->translatedFormat('D') }}</td>
            @endforeach
            <td colspan="5" style="border:1px solid #000000;font-size:7pt;text-align:center;color:#cbd5e1;">Rekap</td>
        </tr>
    </thead>
    <tbody>
        @foreach($siswas as $i => $siswa)
        @php
            $counts = ['H'=>0,'S'=>0,'I'=>0,'A'=>0,'D'=>0];
            foreach($absensiList as $ah) {
                $s = $absensiMap[$ah->id][$siswa->id] ?? null;
                if($s && isset($counts[$s])) $counts[$s]++;
            }
        @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td class="nama">{{ $siswa->nama }}</td>
            @foreach($absensiList as $ah)
            @php $st = $absensiMap[$ah->id][$siswa->id] ?? ''; @endphp
            <td class="st-{{ $st }}">{{ $st ?: '-' }}</td>
            @endforeach
            <td style="font-weight:700;color:#16a34a;">{{ $counts['H'] }}</td>
            <td style="font-weight:700;color:#0284c7;">{{ $counts['S'] }}</td>
            <td style="font-weight:700;color:#d97706;">{{ $counts['I'] }}</td>
            <td style="font-weight:700;color:#dc2626;">{{ $counts['A'] }}</td>
            <td style="font-weight:700;color:#7c3aed;">{{ $counts['D'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="ket-table">
    <tr>
        <td><strong>Keterangan:</strong></td>
        <td style="background:#dcfce7;color:#16a34a;font-weight:bold;">H = Hadir</td>
        <td style="background:#e0f2fe;color:#0284c7;font-weight:bold;">S = Sakit</td>
        <td style="background:#fef3c7;color:#d97706;font-weight:bold;">I = Izin</td>
        <td style="background:#fee2e2;color:#dc2626;font-weight:bold;">A = Alpa</td>
        <td style="background:#ede9fe;color:#7c3aed;font-weight:bold;">D = Dispensasi</td>
    </tr>
</table>

<div class="ttd-outer">
    <table class="ttd-inner">
        <tr>
            <td>
            </br>
                <p>Wali Kelas {{ $rombel->nama_rombel }}</p>
                <div class="ttd-ruang"></div>
                <p><span class="ttd-garis">{{ $ttd['nama_wali'] ?? $rombel->wali_kelas ?? '.......................' }}</span></p>
                <p class="ttd-nip">NIP. {{ $ttd['nip_wali'] ?? '-' }}</p>
            </td>
            <td>
                <p>{{ $ttd['tempat_ttd'] ?? '' }}, {{ $tanggalTtd }}</p>
                <p>Kepala {{ $namaSekolah }}</p>
                <div class="ttd-ruang"></div>
                <p><span class="ttd-garis">{{ $ttd['nama_kepsek'] ?? '.......................' }}</span></p>
                <p class="ttd-nip">NIP. {{ $ttd['nip_kepsek'] ?? '-' }}</p>
                <?php if(!empty($ttd['golongan_kepsek'])): ?><p class="ttd-nip">Gol. {{ $ttd['golongan_kepsek'] }}</p><?php endif; ?>
            </td>
        </tr>
    </table>
</div>
<div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} | {{ $namaSekolah }}</div>
</body>
</html>
