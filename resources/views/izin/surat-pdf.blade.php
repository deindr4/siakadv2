<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Surat Izin {{ $izin->nomor_izin }}</title>
<style>
<?php echo '@page { margin: 1.5cm 2cm; }'; ?>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Times New Roman',serif; font-size:11pt; color:#000; }

/* KOP */
.kop-table { width:100%; border-collapse:collapse; margin-bottom:4px; }
.kop-table td { vertical-align:middle; padding:0; }
.kop-logo-cell { width:70px; text-align:center; }
.kop-logo { width:62px; height:62px; object-fit:contain; }
.kop-text-cell { text-align:center; padding:0 8px; }
.kop-instansi { font-size:9pt; text-transform:uppercase; }
.kop-nama { font-size:14pt; font-weight:bold; text-transform:uppercase; letter-spacing:1px; }
.kop-alamat { font-size:8.5pt; margin-top:2px; }
/*.kop-divider { border:none; border-top:3px double #000; margin:6px 0 10px; }*/
.kop-image-wrap { text-align:center; margin-bottom:4px; }
.kop-image-wrap img { max-height:200px; max-width:100%; object-fit:contain; display:block; margin:0 auto; }

/* JUDUL */
.judul { text-align:center; margin-bottom:16px; }
.judul h2 { font-size:13pt; font-weight:bold; text-transform:uppercase; text-decoration:underline; letter-spacing:1px; }
.judul .nomor { font-size:10pt; margin-top:4px; }

/* BODY SURAT */

.salam { margin-bottom:8px; line-height:1.1; font-size:11pt; padding-left:20px;}
.isi { line-height:1.5; font-size:11pt; padding-left:20px;}
.penutup { margin-top:8px; line-height:1.5; font-size:11pt; padding-left:20px;}

/* TABEL DATA */
.data-table { width:100%; border-collapse:collapse; margin:6px 0; font-size:11pt; }
.data-table td { padding:4px 0; vertical-align:top; }
.data-table td.label { width:180px; font-weight:normal; }
.data-table td.sep { width:8px; }

/* BOX KETERANGAN */
.info-box { border:1px solid #ffffff; padding:10px 14px; margin:5px 0; font-size:10.5pt; line-height:1.1; padding-left:50px; }
.info-box .title { font-weight:bold; text-align:center; margin-bottom:8px; font-size:11pt; border-bottom:1px solid #ccc; padding-bottom:4px; }

/* PENUTUP */
.penutup { margin-top:12px; line-height:1.8; font-size:11pt; }


/* === TTD === */
.ttd-outer {
    width: 100%;
    margin-top: 20px;
}

.ttd-inner {
    border-collapse: collapse;
    width: auto;              /* penting */
    margin-left: auto;       /* INI yang mendorong ke kanan */
    margin-right: 80px;
}

.ttd-inner td {
    text-align: left;
    vertical-align: top;
    padding: 15px 20px;
    font-size: 11pt;
}

.ttd-inner td p {
    margin-bottom: 2px;
    line-height: 1.6;
}

.ttd-ruang {
    height: 65px;
}

.ttd-garis {
    display: inline-block;
    min-width: 180px;
    border-top: 1.5px solid #000;
    padding-top: 3px;
    font-weight: bold;
    font-size: 11pt;
}

.ttd-nip {
    font-size: 9.5pt;
    margin-top: 2px;
}

.ttd-nip { font-size:9.5pt; margin-top:2px; }

/* TTD DIGITAL ORTU */
.ttd-ortu-img { max-width:140px; max-height:60px; display:block; margin:0 auto; }

/* STEMPEL AREA */
.stempel-box {
    border:1px dashed #999; width:100px; height:100px;
    margin:0 auto; display:flex; align-items:center;
    justify-content:center; font-size:8pt; color:#aaa;
    text-align:center;
}


.footer { font-size:7.5pt; color:#555; text-align:center; margin-top:16px; border-top:1px solid #ccc; padding-top:5px; }
</style>
</head>
<body>

@php
    use Carbon\Carbon;
    $kopMode     = $settings['kop_mode'] ?? 'auto';
    $namaSekolah = $settings['nama_sekolah'] ?? 'NAMA SEKOLAH';
    $logoPath    = !empty($settings['logo']) ? storage_path('app/public/'.$settings['logo']) : null;
    $kopPath     = !empty($settings['kop_surat']) ? storage_path('app/public/'.$settings['kop_surat']) : null;
    $alamatFull  = trim(($settings['alamat'] ?? '') . (!empty($settings['kabupaten']) ? ', '.$settings['kabupaten'] : ''));
    $kontakLine  = trim(
        (!empty($settings['telepon']) ? 'Telp: '.$settings['telepon'] : '') .
        (!empty($settings['email']) ? ' | '.$settings['email'] : '')
    );
    $provinsi    = strtoupper($settings['provinsi'] ?? '');
    $kabupaten   = $settings['kabupaten'] ?? $settings['kota'] ?? '';
    $tanggalTtd  = now()->translatedFormat('d F Y');

    $siswa       = $izin->siswa;
    $tglMulai    = $izin->tanggal_mulai->translatedFormat('d F Y');
    $tglSelesai  = $izin->tanggal_selesai->translatedFormat('d F Y');
    $hariEfektif = $izin->hariEfektif();
    $jenisLabel  = \App\Models\IzinBerencana::jenisList()[$izin->jenis] ?? $izin->jenis;
    $kepsekNama  = $ttd['nama_kepsek'] ?? $izin->disetujuiOleh?->name ?? '';
    $kepsekNip   = $ttd['nip_kepsek']  ?? '';
    $kepsekGol   = $ttd['golongan_kepsek'] ?? '';
    $tempat      = $ttd['tempat_ttd']  ?? $kabupaten;

    $tanggalSurat = !empty($ttd['tanggal_surat'])
        ? Carbon::parse($ttd['tanggal_surat'])->translatedFormat('j F Y')
        : now()->translatedFormat('j F Y');


// ✅ SIGNATURE ID
    $kodeSekolah = 'SMAN1KUTSEL'; //sesuaikan
    $signatureId = strtoupper(
        substr(sha1($izin->id . $izin->nomor_izin), 0, 4)
        . '-' .
        substr(sha1($izin->approved_at), 0, 4)
        . '-IZN-' .
        now()->format('Y')
        . '-' .
        str_pad($izin->id, 4, '0', STR_PAD_LEFT)
        . '-' .
        $kodeSekolah
    );

@endphp

{{-- KOP SURAT --}}
@if($kopMode === 'image' && $kopPath)
<div class="kop-image-wrap"><img src="{{ $kopPath }}"></div>
@else
<table class="kop-table">
    <tr>
        <td class="kop-logo-cell">
            @if($logoPath)<img class="kop-logo" src="{{ $logoPath }}">@endif
        </td>
        <td class="kop-text-cell">
            <p class="kop-instansi">PEMERINTAH PROVINSI {{ $provinsi }}</p>
            <p class="kop-nama">{{ $namaSekolah }}</p>
            <p class="kop-alamat">{{ $alamatFull }}</p>
            @if($kontakLine)<p class="kop-alamat">{{ $kontakLine }}</p>@endif
        </td>
        <td class="kop-logo-cell"></td>
    </tr>
</table>
@endif
{{--<hr class="kop-divider">--}}

{{-- JUDUL SURAT --}}
<div class="judul">
    <h2>SURAT PERMOHONAN IZIN BERENCANA</h2>
    <div class="nomor">Nomor: {{ $izin->nomor_izin }}</div>
</div>

{{-- SALAM --}}
<div class="salam">
    Yang bertanda tangan di bawah ini, Kepala {{ $namaSekolah }}, dengan ini menerangkan bahwa:
</div>

{{-- DATA SISWA --}}
<div class="info-box">
    {{--<div class="title">Data Siswa</div>--}}
    <table class="data-table">
        <tr>
            <td class="label">Nama Siswa</td>
            <td class="sep">:</td>
            <td><strong>{{ $siswa?->nama ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">NISN</td>
            <td class="sep">:</td>
            <td>{{ $siswa?->nisn ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="sep">:</td>
            <td>{{ $siswa?->nama_rombel ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Orang Tua/Wali</td>
            <td class="sep">:</td>
            <td>{{ $izin->nama_ortu }}</td>
        </tr>
        <tr>
            <td class="label">No. HP Orang Tua</td>
            <td class="sep">:</td>
            <td>{{ $izin->no_hp_ortu }}</td>
        </tr>
    </table>
</div>

{{-- ISI SURAT --}}
<div class="isi">
    <p>
        Siswa tersebut di atas diberikan <strong>izin tidak masuk sekolah</strong> dengan keterangan sebagai berikut:
    </p>
</div>

{{-- DATA IZIN --}}
<div class="info-box">
    {{--<div class="title">Keterangan Izin</div>--}}
    <table class="data-table">
        <tr>
            <td class="label">Jenis Keperluan</td>
            <td class="sep">:</td>
            <td>{{ $jenisLabel }}</td>
        </tr>
        <tr>
            <td class="label">Alasan</td>
            <td class="sep">:</td>
            <td>{{ $izin->alasan }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Mulai</td>
            <td class="sep">:</td>
            <td>{{ $tglMulai }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Selesai</td>
            <td class="sep">:</td>
            <td>{{ $tglSelesai }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah Hari</td>
            <td class="sep">:</td>
            <td><strong>{{ $hariEfektif }} hari</strong></td>
        </tr>
    </table>
</div>

{{-- PENUTUP --}}
<div class="penutup">
    <p>
        Demikian surat izin ini dibuat untuk dipergunakan sebagaimana mestinya.
        Siswa yang bersangkutan wajib hadir kembali ke sekolah pada hari berikutnya setelah masa izin berakhir.
    </p>
</div>


{{-- TTD --}}
<div class="ttd-outer">
    <table class="ttd-inner ttd-kanan">
        <tr>
            <td>
                <p>Badung, {{ $tanggalSurat }}</p>
                <p>Orang Tua / Wali Murid,</p>

                <div class="ttd-ruang">
                    @if($izin->ttd_ortu)
                        <img src="{{ $izin->ttd_ortu }}" class="ttd-ortu-img">
                    @endif
                </div>

                <p class="ttd-garis">{{ $izin->nama_ortu }}</p>
                <p class="ttd-nip">HP: {{ $izin->no_hp_ortu }}</p>
            </td>
        </tr>
    </table>
</div>



<p style="font-size:7pt;color:#555;text-align:center;margin-top:10px;">
    <span style="text-transform:uppercase;font-style:timesnewroman;">
        Dokumen ini sah dan diterbitkan secara elektronik oleh Sistem Akademik SMA Negeri 1 Kuta Selatan dan telah disetujui oleh Kepala Sekolah.
    </span>
    <br>
    <strong>Signature ID: {{ $signatureId }}</strong>
</p>


<div class="footer">
    Dicetak oleh sistem SIAKAD — {{ $namaSekolah }} — {{ now()->translatedFormat('d F Y H:i') }}
</div>

</body>
</html>
