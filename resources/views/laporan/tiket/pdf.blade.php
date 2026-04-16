<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Tiket</title>
<style>
<?php echo '@page { margin: 1.5cm 2cm; }'; ?>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Times New Roman',serif; font-size:10pt; color:#000; }
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
.kop-image-wrap img { max-height:100px; max-width:100%; object-fit:contain; display:block; margin:0 auto; }
.judul { text-align:center; margin-bottom:14px; }
.judul h2 { font-size:12pt; font-weight:bold; text-transform:uppercase; text-decoration:underline; }
.judul p { font-size:9.5pt; margin-top:4px; }
.tabel { width:100%; border-collapse:collapse; margin-bottom:14px; font-size:8.5pt; }
.tabel th { background:#1e3a5f; color:#fff; padding:5px 6px; text-align:center; border:1px solid #000; }
.tabel td { padding:4px 6px; border:1px solid #aaa; vertical-align:middle; }
.tabel tr:nth-child(even) td { background:#f5f8ff; }
.tabel tfoot td { font-weight:bold; background:#e8ecf5; border:1px solid #999; }
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
.section-title { font-size:11pt; font-weight:bold; text-decoration:underline; margin:14px 0 6px; }
.badge-kategori { display:inline-block; padding:1px 6px; border-radius:6px; font-size:8pt; font-weight:bold; }
</style>
</head>
<body>
@php
    $kopMode     = $settings['kop_mode'] ?? 'auto';
    $namaSekolah = $settings['nama_sekolah'] ?? 'NAMA SEKOLAH';
    $tanggalTtd  = !empty($ttd['tanggal_ttd']) ? \Carbon\Carbon::parse($ttd['tanggal_ttd'])->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
    $logoPath    = !empty($settings['logo'])      ? storage_path('app/public/'.$settings['logo'])      : null;
    $kopPath     = !empty($settings['kop_surat']) ? storage_path('app/public/'.$settings['kop_surat']) : null;
    $alamatFull  = trim(($settings['alamat'] ?? '') . (!empty($settings['kabupaten']) ? ', '.$settings['kabupaten'] : ''));
    $kontakLine  = trim((!empty($settings['telepon']) ? 'Telp: '.$settings['telepon'] : '') . (!empty($settings['email']) ? ' | '.$settings['email'] : ''));
    $provinsi    = strtoupper($settings['provinsi'] ?? '');
    $judulPdf    = $jenis === 'detail' ? 'Daftar Detail Tiket' : 'Rekap Tiket Kritik, Saran & Pengaduan';
@endphp

<?php if($kopMode === 'image' && $kopPath): ?>
<div class="kop-image-wrap"><img src="{{ $kopPath }}"></div>
<?php else: ?>
<table class="kop-table">
    <tr>
        <td class="kop-logo-cell"><?php if($logoPath): ?><img class="kop-logo" src="{{ $logoPath }}"><?php endif; ?></td>
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
    <p>Tahun {{ $tahun }}<?php if($bulanNama): ?> &mdash; Bulan {{ $bulanNama }}<?php endif; ?></p>
</div>

<?php if($jenis === 'rekap'): ?>

{{-- REKAP PER KATEGORI --}}
<p class="section-title">A. Rekap Per Kategori</p>
<table class="tabel">
    <thead>
        <tr>
            <th style="width:25px;">No</th>
            <th>Kategori</th>
            <th style="width:45px;">Total</th>
            <th style="width:50px;">Terbuka</th>
            <th style="width:55px;">Diproses</th>
            <th style="width:50px;">Selesai</th>
            <th style="width:55px;">Terkunci</th>
            <th style="width:55px;">Dari Siswa</th>
            <th style="width:50px;">Dari Guru</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekapKategori as $i => $r)
        @php
            $dummy = new \App\Models\Tiket(['kategori'=>$r->kategori,'kategori_lainnya'=>$r->kategori_lainnya]);
        @endphp
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td class="bold">{{ $dummy->kategoriLabel() }}</td>
            <td class="center bold" style="font-size:12pt;color:#1e3a5f;">{{ $r->total }}</td>
            <td class="center">{{ $r->open }}</td>
            <td class="center">{{ $r->diproses }}</td>
            <td class="center">{{ $r->selesai }}</td>
            <td class="center">{{ $r->terkunci }}</td>
            <td class="center">{{ $r->dari_siswa }}</td>
            <td class="center">{{ $r->dari_guru }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="center" style="padding:10px;">Tidak ada data</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align:right;padding-right:8px;">TOTAL</td>
            <td class="center">{{ $rekapKategori->sum('total') }}</td>
            <td class="center">{{ $rekapKategori->sum('open') }}</td>
            <td class="center">{{ $rekapKategori->sum('diproses') }}</td>
            <td class="center">{{ $rekapKategori->sum('selesai') }}</td>
            <td class="center">{{ $rekapKategori->sum('terkunci') }}</td>
            <td class="center">{{ $rekapKategori->sum('dari_siswa') }}</td>
            <td class="center">{{ $rekapKategori->sum('dari_guru') }}</td>
        </tr>
    </tfoot>
</table>

{{-- REKAP PER BULAN --}}
<p class="section-title">B. Rekap Per Bulan</p>
<table class="tabel">
    <thead>
        <tr>
            <th style="width:25px;">No</th>
            <th>Bulan</th>
            <th style="width:45px;">Total</th>
            <th style="width:50px;">Aktif</th>
            <th style="width:55px;">Selesai</th>
            <th style="width:65px;">% Selesai</th>
            <th style="width:60px;">Dari Siswa</th>
            <th style="width:55px;">Dari Guru</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekapBulan as $i => $r)
        @php $pct = $r->total > 0 ? round(($r->selesai / $r->total) * 100) : 0; @endphp
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td class="bold">{{ $r->bulan_nama }}</td>
            <td class="center bold" style="font-size:12pt;color:#1e3a5f;">{{ $r->total }}</td>
            <td class="center">{{ $r->aktif }}</td>
            <td class="center">{{ $r->selesai }}</td>
            <td class="center">{{ $pct }}%</td>
            <td class="center">{{ $r->dari_siswa }}</td>
            <td class="center">{{ $r->dari_guru }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="center" style="padding:10px;">Tidak ada data</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align:right;padding-right:8px;">TOTAL</td>
            <td class="center">{{ $rekapBulan->sum('total') }}</td>
            <td class="center">{{ $rekapBulan->sum('aktif') }}</td>
            <td class="center">{{ $rekapBulan->sum('selesai') }}</td>
            <td class="center">
                @php $totalAll = $rekapBulan->sum('total'); @endphp
                {{ $totalAll > 0 ? round(($rekapBulan->sum('selesai') / $totalAll) * 100) : 0 }}%
            </td>
            <td class="center">{{ $rekapBulan->sum('dari_siswa') }}</td>
            <td class="center">{{ $rekapBulan->sum('dari_guru') }}</td>
        </tr>
    </tfoot>
</table>

<?php else: ?>

{{-- DETAIL TIKET --}}
<table class="tabel">
    <thead>
        <tr>
            <th style="width:22px;">No</th>
            <th>Judul Tiket</th>
            <th style="width:70px;">Kategori</th>
            <th style="width:80px;">Dari</th>
            <th style="width:45px;">Prior.</th>
            <th style="width:55px;">Status</th>
            <th style="width:35px;">Respon</th>
            <th style="width:60px;">Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detailTiket as $i => $t)
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td class="bold" style="font-size:8pt;">{{ \Str::limit($t->judul, 50) }}</td>
            <td class="center" style="font-size:8pt;">{{ $t->kategoriLabel() }}</td>
            <td style="font-size:8pt;">
                {{ $t->is_anonim ? 'Anonim' : ($t->user?->name ?? '-') }}
                <br><span style="color:#555;font-size:7.5pt;">{{ ucfirst($t->role_pembuat) }}</span>
            </td>
            <td class="center" style="font-size:8pt;">{{ ucfirst($t->prioritas) }}</td>
            <td class="center bold" style="font-size:8pt;">{{ $t->statusLabel() }}</td>
            <td class="center">{{ $t->respon->count() }}</td>
            <td class="center" style="font-size:8pt;">{{ $t->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="center" style="padding:10px;">Tidak ada data</td></tr>
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
                <p class="ttd-nip">NIP. {{ $ttd['nip_kepsek'] ?? '-' }}</p>
                <?php if(!empty($ttd['golongan_kepsek'])): ?><p class="ttd-nip">Gol. {{ $ttd['golongan_kepsek'] }}</p><?php endif; ?>
            </td>
        </tr>
    </table>
</div>
<div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} | {{ $namaSekolah }}</div>
</body>
</html>
