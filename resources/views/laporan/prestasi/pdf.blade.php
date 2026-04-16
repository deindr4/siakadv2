<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Prestasi</title>
<style>
<?php echo '@page { margin: 1.5cm 2cm; }'; ?>
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
.kop-divider { border:none; border-top:3px double #000; margin:6px 0 10px; }
.kop-image-wrap { text-align:center; margin-bottom:4px; }
.kop-image-wrap img { max-height:200px; max-width:100%; object-fit:contain; display:block; margin:0 auto; }
.judul { text-align:center; margin-bottom:12px; }
.judul h2 { font-size:12pt; font-weight:bold; text-transform:uppercase; text-decoration:underline; }
.judul p { font-size:9.5pt; margin-top:4px; }
.tabel { width:100%; border-collapse:collapse; margin-bottom:14px; font-size:8.5pt; }
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
.badge { display:inline-block; padding:1px 6px; border-radius:6px; font-size:8pt; font-weight:bold; }
.filter-info { font-size:8.5pt; color:#555; margin-bottom:10px; }
</style>
</head>
<body>
@php
    $kopMode     = $settings['kop_mode'] ?? 'auto';
    $namaSekolah = $settings['nama_sekolah'] ?? 'NAMA SEKOLAH';
    $tanggalTtd  = !empty($ttd['tanggal_ttd']) ? \Carbon\Carbon::parse($ttd['tanggal_ttd'])->translatedFormat('d F Y') : now()->translatedFormat('d F Y');

    $logoPath    = !empty($settings['logo']) ? storage_path('app/public/'.$settings['logo']) : null;
    $kopPath     = !empty($settings['kop_surat']) ? storage_path('app/public/'.$settings['kop_surat']) : null;
    $alamatFull  = trim(($settings['alamat'] ?? '') . (!empty($settings['kabupaten']) ? ', '.$settings['kabupaten'] : ''));
    $kontakLine  = trim(
        (!empty($settings['telepon']) ? 'Telp: '.$settings['telepon'] : '') .
        (!empty($settings['email'])   ? ' | '.$settings['email'] : '')
    );
    $provinsi    = strtoupper($settings['provinsi'] ?? '');

    $judulMap = [
        'detail'   => 'Daftar Prestasi Siswa',
        'kategori' => 'Rekap Prestasi Per Kategori',
        'tingkat'  => 'Rekap Prestasi Per Tingkat',
        'siswa'    => 'Rekap Prestasi Per Siswa',
    ];
    $judulPdf = $judulMap[$jenis] ?? 'Laporan Prestasi Siswa';

    $tingkatColors = ['sekolah'=>'gray','kecamatan'=>'blue','kabupaten'=>'green','provinsi'=>'orange','nasional'=>'red','internasional'=>'purple'];
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
    <h2>{{ $judulPdf }}</h2>
    <p>
        {{ $semester?->nama ?? 'Semua Semester' }}
        <?php if($kategori): ?> &mdash; {{ $kategori->nama }}<?php endif; ?>
        <?php if($tingkat): ?> &mdash; Tingkat {{ ucfirst($tingkat) }}<?php endif; ?>
        <?php if($rombel): ?> &mdash; Kelas {{ $rombel->nama_rombel }}<?php endif; ?>
    </p>
</div>

{{-- ====================== DETAIL ====================== --}}
<?php if($jenis === 'detail'): ?>
<table class="tabel">
    <thead>
        <tr>
            <th style="width:22px;">No</th>
            <th>Nama Lomba</th>
            <th style="width:70px;">Kategori</th>
            <th style="width:65px;">Tingkat</th>
            <th style="width:55px;">Juara</th>
            <th>Siswa</th>
            <th style="width:35px;">Kelas</th>
            <th style="width:60px;">Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detailPrestasi as $i => $p)
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td class="bold">{{ $p->nama_lomba }}
                <?php if($p->penyelenggara): ?><br><span style="font-weight:normal;font-size:7.5pt;color:#555;">{{ $p->penyelenggara }}</span><?php endif; ?>
            </td>
            <td class="center" style="font-size:8pt;">{{ $p->kategori?->nama ?? '-' }}</td>
            <td class="center bold">{{ $p->tingkatLabel() }}</td>
            <td class="center bold" style="color:#b45309;">{{ $p->juara }}</td>
            <td style="font-size:8pt;">{{ $p->siswas->pluck('nama')->join(', ') }}</td>
            <td class="center" style="font-size:8pt;">{{ $p->siswas->pluck('nama_rombel')->unique()->join(', ') }}</td>
            <td class="center" style="font-size:8pt;">{{ $p->tanggal->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="center" style="padding:12px;">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>
<?php endif; ?>

{{-- ====================== REKAP KATEGORI ====================== --}}
<?php if($jenis === 'kategori'): ?>
<table class="tabel">
    <thead>
        <tr>
            <th style="width:25px;">No</th>
            <th>Kategori</th>
            <th style="width:80px;">Jenis</th>
            <th style="width:55px;">Total</th>
            <th style="width:60px;">Individu</th>
            <th style="width:50px;">Tim</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekapKategori as $i => $r)
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td class="bold">{{ $r->kategori?->nama ?? 'Tanpa Kategori' }}</td>
            <td class="center">{{ $r->kategori?->jenisLabel() ?? '-' }}</td>
            <td class="center bold" style="font-size:12pt;color:#4f46e5;">{{ $r->total }}</td>
            <td class="center">{{ $r->individu }}</td>
            <td class="center">{{ $r->tim }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="center" style="padding:12px;">Tidak ada data</td></tr>
        @endforelse
        <tr style="background:#f0f4ff;">
            <td colspan="3" class="bold" style="text-align:right;padding-right:8px;">TOTAL</td>
            <td class="center bold" style="font-size:13pt;color:#4f46e5;">{{ $rekapKategori->sum('total') }}</td>
            <td class="center bold">{{ $rekapKategori->sum('individu') }}</td>
            <td class="center bold">{{ $rekapKategori->sum('tim') }}</td>
        </tr>
    </tbody>
</table>
<?php endif; ?>

{{-- ====================== REKAP TINGKAT ====================== --}}
<?php if($jenis === 'tingkat'): ?>
<table class="tabel">
    <thead>
        <tr>
            <th style="width:25px;">No</th>
            <th>Tingkat</th>
            <th style="width:50px;">Total</th>
            <th style="width:50px;">Juara 1</th>
            <th style="width:50px;">Juara 2</th>
            <th style="width:50px;">Juara 3</th>
            <th style="width:55px;">Lainnya</th>
            <th style="width:55px;">Individu</th>
            <th style="width:45px;">Tim</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekapTingkat as $i => $r)
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td class="bold">{{ ucfirst($r->tingkat) }}</td>
            <td class="center bold" style="font-size:12pt;color:#1e3a5f;">{{ $r->total }}</td>
            <td class="center bold" style="color:#d97706;">{{ $r->juara1 }}</td>
            <td class="center bold" style="color:#64748b;">{{ $r->juara2 }}</td>
            <td class="center bold" style="color:#92400e;">{{ $r->juara3 }}</td>
            <td class="center">{{ $r->lainnya }}</td>
            <td class="center">{{ $r->individu ?? '' }}</td>
            <td class="center">{{ $r->tim ?? '' }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="center" style="padding:12px;">Tidak ada data</td></tr>
        @endforelse
        <tr style="background:#f0f4ff;">
            <td colspan="2" class="bold" style="text-align:right;padding-right:8px;">TOTAL</td>
            <td class="center bold" style="font-size:13pt;color:#1e3a5f;">{{ $rekapTingkat->sum('total') }}</td>
            <td class="center bold" style="color:#d97706;">{{ $rekapTingkat->sum('juara1') }}</td>
            <td class="center bold" style="color:#64748b;">{{ $rekapTingkat->sum('juara2') }}</td>
            <td class="center bold" style="color:#92400e;">{{ $rekapTingkat->sum('juara3') }}</td>
            <td class="center bold">{{ $rekapTingkat->sum('lainnya') }}</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>
<?php endif; ?>

{{-- ====================== REKAP SISWA ====================== --}}
<?php if($jenis === 'siswa'): ?>
<table class="tabel">
    <thead>
        <tr>
            <th style="width:25px;">No</th>
            <th>Nama Siswa</th>
            <th style="width:70px;">NISN</th>
            <th style="width:45px;">Kelas</th>
            <th style="width:50px;">Total</th>
            <th style="width:65px;">Nasional/Int</th>
            <th style="width:55px;">Provinsi</th>
            <th style="width:55px;">Kab/Kota</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekapSiswa as $i => $r)
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td class="bold">{{ $r->siswa?->nama }}</td>
            <td class="center" style="font-size:8.5pt;">{{ $r->siswa?->nisn ?? '-' }}</td>
            <td class="center">{{ $r->siswa?->nama_rombel }}</td>
            <td class="center bold" style="font-size:12pt;color:#4f46e5;">{{ $r->total }}</td>
            <td class="center bold" style="color:#dc2626;">{{ $r->nasional ?: '-' }}</td>
            <td class="center bold" style="color:#d97706;">{{ $r->provinsi ?: '-' }}</td>
            <td class="center">{{ $r->prestasi->filter(fn($i) => $i->prestasi?->tingkat === 'kabupaten')->count() ?: '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="center" style="padding:12px;">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>
<?php endif; ?>

{{-- TTD --}}
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
