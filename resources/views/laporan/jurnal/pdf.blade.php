<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Jurnal Mengajar</title>
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
    /*.kop-divider  { border: none; border-top: 3px double #000; margin: 6px 0 10px; }*/

    /* Kop gambar */
    .kop-image-wrap { text-align: center; margin-bottom: 4px; }
    .kop-image-wrap img { max-height: 200px; max-width: 300%; object-fit: contain; display: block; margin: 0 auto; }

    /* ===== JUDUL ===== */
    .judul { text-align: center; margin-bottom: 12px; }
    .judul h2 { font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; text-decoration: underline; }
    .judul p  { font-size: 9.5pt; margin-top: 4px; }

    /* ===== INFO GURU ===== */
    .info-table { width: 100%; border-collapse: collapse; border: 1px solid #000; margin-bottom: 12px; font-size: 9.5pt; padding: 8px; }
    .info-table td { padding: 3px 8px; vertical-align: top; }
    .info-table td.label { font-weight: bold; width: 120px; white-space: nowrap; }
    .info-table td.sep   { width: 10px; }

    /* ===== TABEL REKAP ===== */
    .tabel-rekap { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 8.5pt; padding: 15; }
    .tabel-rekap th {
        background: #1e3a5f;
        color: #fff;
        padding: 5px 4px;
        text-align: center;
        border: 1px solid #000;
    }
    .tabel-rekap td { padding: 4px 5px; border: 1px solid #aaa; vertical-align: top; }
    .tabel-rekap tr:nth-child(even) td { background: #f5f5f5; }
    .tabel-rekap tfoot td { background: #e8e8e8; font-weight: bold; }
    .center { text-align: center; }
    .bold   { font-weight: bold; }

    /* ===== TTD - TABLE LAYOUT (DomPDF tidak support flex) ===== */
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

    /* ===== DETAIL PERTEMUAN ===== */
    .page-break   { page-break-after: always; }
    .detail-block { border: 1px solid #000; margin-bottom: 14px; page-break-inside: avoid; padding: 15px;}
    .detail-head  { background: #1e3a5f; color: #fff; padding: 5px 10px; }
    .detail-head-tbl { width: 100%; border-collapse: collapse; padding: 15px;}
    .detail-head-tbl td { color: #fff; vertical-align: middle; padding: 0; font-size: 9pt; padding: 15px; }
    .detail-body  { padding: 8px 10px; }
    .drow-table   { width: 100%; border-collapse: collapse; margin-bottom: 6px; font-size: 9pt; }
    .drow-table td { vertical-align: top; padding: 2px 0; }
    .drow-label   { font-weight: bold; width: 120px; white-space: nowrap; }
    .drow-sep     { width: 10px; }
    .detail-kotak { border: 1px solid #ccc; padding: 5px 8px; background: #fafafa; min-height: 26px; margin-bottom: 6px; font-size: 9pt; line-height: 1.5; }
    .detail-note  { border: 1px solid #ccc; padding: 5px 8px; background: #fffbeb; min-height: 22px; margin-bottom: 6px; font-size: 9pt; }

    /* ===== FOOTER ===== */
    .footer { font-size: 7.5pt; color: #555; text-align: center; margin-top: 16px; border-top: 1px solid #ccc; padding-top: 5px; }
</style>
</head>
<body>

@php
    $kopMode     = $settings['kop_mode'] ?? 'auto';
    $namaSekolah = $settings['nama_sekolah'] ?? 'NAMA SEKOLAH';
    $bulanLabel  = $bulan ? \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') : 'Semua Bulan';
    $tahun       = $jurnals->first()?->tanggal?->format('Y') ?? now()->format('Y');
    $tanggalTtd  = $ttd['tanggal_ttd']
        ? \Carbon\Carbon::parse($ttd['tanggal_ttd'])->translatedFormat('d F Y')
        : now()->translatedFormat('d F Y');
@endphp

{{-- ============================================================ --}}
{{-- HALAMAN 1 : REKAP                                            --}}
{{-- ============================================================ --}}

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
    <h2>Laporan Jurnal Mengajar</h2>
    <p>
        {{ $semester?->nama ?? 'Semua Semester' }}
        &mdash; {{ $bulanLabel }} {{ $tahun }}
        @if($guruData) &mdash; {{ $guruData->nama }} @endif
    </p>
</div>

{{-- INFO GURU --}}
@if($guruData)
<table class="info-table">
    <tr>
        <td class="label">Nama Guru</td>
        <td class="sep">:</td>
        <td>{{ $ttd['nama_guru'] ?? $guruData->nama }}</td>
        <td class="label">Mata Pelajaran</td>
        <td class="sep">:</td>
        <td>{{ $jurnals->first()?->mataPelajaran?->nama ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">NIP</td>
        <td class="sep">:</td>
        <td>{{ $ttd['nip_guru'] ?? $guruData->nip ?? '-' }}</td>
        <td class="label">Kelas</td>
        <td class="sep">:</td>
        <td>{{ $jurnals->pluck('nama_rombel')->unique()->implode(', ') }}</td>
    </tr>
    <tr>
        <td class="label">Golongan</td>
        <td class="sep">:</td>
        <td>{{ $ttd['golongan_guru'] ?? '-' }}</td>
        <td class="label">Semester</td>
        <td class="sep">:</td>
        <td>{{ $semester?->nama ?? '-' }}</td>
    </tr>
</table>
@endif

{{-- TABEL REKAP --}}
<table class="tabel-rekap">
    <thead>
        <tr>
            <th style="width:22px;">No</th>
            <th style="width:60px;">Tanggal</th>
            <th style="width:40px;">Hari</th>
            @if(!$guruData)<th>Guru</th>@endif
            <th>Mata Pelajaran</th>
            <th style="width:70px;">Kelas</th>
            <th style="width:36px;">Ptm</th>
            <th style="width:52px;">Jam</th>
            <th>Materi / KD</th>
            <th style="width:34px;">Hadir</th>
            <th style="width:40px;">Tdk Hadir</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jurnals as $i => $j)
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td class="center">{{ $j->tanggal?->format('d/m/Y') }}</td>
            <td class="center">{{ $j->tanggal?->translatedFormat('D') }}</td>
            @if(!$guruData)<td>{{ $j->guru?->nama }}</td>@endif
            <td>{{ $j->mataPelajaran?->nama }}</td>
            <td class="center">{{ $j->nama_rombel }}</td>
            <td class="center bold">{{ $j->pertemuan_ke ?? '-' }}</td>
            <td class="center">
                {{ $j->jam_mulai?->format('H:i') }}
                @if($j->jam_selesai)-{{ $j->jam_selesai->format('H:i') }}@endif
            </td>
            <td>{{ \Illuminate\Support\Str::limit($j->materi, 55) }}</td>
            <td class="center">{{ $j->jumlah_hadir ?? '-' }}</td>
            <td class="center">{{ $j->jumlah_tidak_hadir ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="{{ $guruData ? 9 : 10 }}" class="center" style="padding:14px;">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="{{ $guruData ? 7 : 8 }}" style="text-align:right;">TOTAL PERTEMUAN:</td>
            <td class="center">{{ $jurnals->count() }}</td>
            <td class="center">{{ $jurnals->sum('jumlah_hadir') }}</td>
            <td class="center">{{ $jurnals->sum('jumlah_tidak_hadir') }}</td>
        </tr>
    </tfoot>
</table>

{{-- TTD REKAP - GURU KIRI / KEPSEK KANAN --}}
<div class="ttd-outer">
    <table class="ttd-inner">
        <tr>
            {{-- KIRI: GURU --}}
            <td>
                </br>
                </br>
                <p>Guru Mata Pelajaran</p>
                <div class="ttd-ruang">
                    @if($jurnals->first()?->tanda_tangan)
                        <img src="{{ $jurnals->first()->tanda_tangan }}" style="max-height:55px;max-width:120px;margin-top:6px;">
                    @endif
                </div>
                <p><span class="ttd-garis">{{ $ttd['nama_guru'] ?? '.....................................' }}</span></p>
                @if(!empty($ttd['golongan_guru']))
                <p class="ttd-nip">Gol. {{ $ttd['golongan_guru'] }}</p>
                <p class="ttd-nip">NIP. {{ $ttd['nip_guru'] ?? '-' }}</p>
                @endif
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

{{-- ============================================================ --}}
{{-- HALAMAN 2+ : DETAIL PER PERTEMUAN                           --}}
{{-- ============================================================ --}}
<div class="page-break"></div>

{{-- KOP ulang --}}
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
                    @if(!empty($settings['kabupaten'])), {{ $settings['kabupaten'] }}@endif
                </p>
            </td>
            <td class="kop-logo-cell"></td>
        </tr>
    </table>
@endif
<hr class="kop-divider">

<div class="judul">
    <h2>Detail Jurnal Mengajar</h2>
    <p>{{ $semester?->nama ?? '' }} &mdash; {{ $bulanLabel }} {{ $tahun }}</p>
</div>

@foreach($jurnals as $j)
<div class="detail-block">

    {{-- Header pertemuan --}}
    <div class="detail-head">
        <table class="detail-head-tbl">
            <tr>
                <td style="font-weight:bold;">
                    Pertemuan ke-{{ $j->pertemuan_ke ?? $loop->iteration }}
                    &mdash; {{ $j->tanggal?->translatedFormat('l, d F Y') }}
                </td>
                <td style="text-align:right;">
                    {{ $j->mataPelajaran?->nama }} | {{ $j->nama_rombel }}
                    @if($j->jam_ke) | Jam ke-{{ $j->jam_ke }}@endif
                </td>
            </tr>
        </table>
    </div>

    <div class="detail-body">

        <table class="drow-table">
            <tr>
                <td class="drow-label">Guru Pengajar</td>
                <td class="drow-sep">:</td>
                <td>{{ $j->guru?->nama }}</td>
                <td class="drow-label">Jam Pelajaran</td>
                <td class="drow-sep">:</td>
                <td>
                    {{ $j->jam_mulai?->format('H:i') ?? '-' }}
                    @if($j->jam_selesai) s/d {{ $j->jam_selesai->format('H:i') }}@endif
                </td>
            </tr>
            <tr>
                <td class="drow-label">Jumlah Hadir</td>
                <td class="drow-sep">:</td>
                <td>{{ $j->jumlah_hadir ?? '-' }} siswa</td>
                <td class="drow-label">Tidak Hadir</td>
                <td class="drow-sep">:</td>
                <td>{{ $j->jumlah_tidak_hadir ?? '-' }} siswa</td>
            </tr>
        </table>

        <p style="font-weight:bold;font-size:9pt;margin-bottom:3px;">Materi / Kompetensi Dasar:</p>
        <div class="detail-kotak">{{ $j->materi }}</div>

        <p style="font-weight:bold;font-size:9pt;margin-bottom:3px;">Kegiatan Pembelajaran:</p>
        <div class="detail-kotak">{{ $j->kegiatan }}</div>

        @if($j->catatan)
        <p style="font-weight:bold;font-size:9pt;margin-bottom:3px;">Catatan:</p>
        <div class="detail-note">{{ $j->catatan }}</div>
        @endif

        {{-- TTD per pertemuan - GURU KIRI / KEPSEK KANAN --}}
        <div class="ttd-outer" style="margin-top:12px;">
            <table class="ttd-inner">
                <tr>
                    {{-- KIRI: GURU --}}
                    <td>
                        <p>{{ $ttd['tempat_ttd'] ?? '' }}, {{ $tanggalTtd }}</p>
                        <p>Guru Mata Pelajaran</p>
                        <div style="height:50px;">
                            @if($j->tanda_tangan)
                                <img src="{{ $j->tanda_tangan }}" style="max-height:46px;max-width:110px;margin-top:4px;">
                            @endif
                        </div>
                        <p><span class="ttd-garis" style="min-width:160px;">{{ $ttd['nama_guru'] ?? '.......................' }}</span></p>
                        <p class="ttd-nip">NIP. {{ $ttd['nip_guru'] ?? '-' }}</p>
                        @if(!empty($ttd['golongan_guru']))
                        <p class="ttd-nip">Gol. {{ $ttd['golongan_guru'] }}</p>
                        @endif
                    </td>
                    {{-- KANAN: KEPALA SEKOLAH --}}
                    <td>
                        <p>Mengetahui,</p>
                        <p>Kepala {{ $namaSekolah }}</p>
                        <div style="height:50px;"></div>
                        <p><span class="ttd-garis" style="min-width:160px;">{{ $ttd['nama_kepsek'] ?? '.......................' }}</span></p>
                        <p class="ttd-nip">NIP. {{ $ttd['nip_kepsek'] ?? '-' }}</p>
                        @if(!empty($ttd['golongan_kepsek']))
                        <p class="ttd-nip">Gol. {{ $ttd['golongan_kepsek'] }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>

@if(!$loop->last && $loop->iteration % 2 === 0)
    <!-- <div class="page-break"></div> -->
@endif

@endforeach

<div class="footer">
    Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} &nbsp;|&nbsp; {{ $namaSekolah }}
</div>

</body>
</html>
