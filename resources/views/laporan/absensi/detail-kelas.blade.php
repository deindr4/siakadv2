{{-- ============================================================ --}}
{{-- FILE: resources/views/laporan/absensi/detail-kelas.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('page-title', 'Detail Absensi Per Kelas')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
<div class="page-title">
    <h1>📅 Detail Absensi Per Kelas</h1>
    <p>Daftar hadir lengkap per siswa per hari</p>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:180px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">SEMESTER</label>
                <select name="semester_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>{{ $sem->nama }} {{ $sem->is_aktif ? '✅' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:160px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">KELAS <span style="color:red">*</span></label>
                <select name="rombel_id" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:120px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">BULAN</label>
                <select name="bulan" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">Semua</option>
                    @foreach(range(1,12) as $b)
                        <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
                <a href="{{ route('laporan.absensi.detail-kelas') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

@if($rombelId && $rombel)

{{-- Info + Export --}}
<div class="card" style="margin-bottom:20px;border-left:4px solid #6366f1;">
    <div class="card-body">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;gap:24px;">
                <div>
                    <p style="font-size:11px;color:#94a3b8;font-weight:700;">KELAS</p>
                    <p style="font-size:18px;font-weight:800;color:#6366f1;">{{ $rombel->nama_rombel }}</p>
                </div>
                <div>
                    <p style="font-size:11px;color:#94a3b8;font-weight:700;">JUMLAH SISWA</p>
                    <p style="font-size:18px;font-weight:800;">{{ $siswas->count() }}</p>
                </div>
                <div>
                    <p style="font-size:11px;color:#94a3b8;font-weight:700;">HARI DIABSEN</p>
                    <p style="font-size:18px;font-weight:800;">{{ $absensiList->count() }}</p>
                </div>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" onclick="document.getElementById('modal-cetak').style.display='flex'"
                    class="btn" style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Tabel daftar hadir (pivot: baris=siswa, kolom=tanggal) --}}
@if($absensiList->isNotEmpty() && $siswas->isNotEmpty())

@php
    // Buat map: absensi_harian_id -> date -> absensiSiswa by siswa_id
    $absensiMap = [];
    foreach($absensiList as $ah) {
        foreach($ah->absensiSiswa as $as) {
            $absensiMap[$ah->id][$as->siswa_id] = $as->status;
        }
    }
    $statusColors = ['H'=>'#dcfce7','S'=>'#e0f2fe','I'=>'#fef3c7','A'=>'#fee2e2','D'=>'#ede9fe'];
    $statusText   = ['H'=>'#16a34a','S'=>'#0284c7','I'=>'#d97706','A'=>'#dc2626','D'=>'#7c3aed'];
@endphp

<div class="card">
    <div class="card-header"><h3>📋 Daftar Hadir — {{ $rombel->nama_rombel }}</h3></div>
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <table style="border-collapse:collapse;font-size:11px;min-width:max-content;width:100%;">
            <thead>
                <tr style="background:#1e3a5f;color:#fff;">
                    <th style="padding:8px 10px;text-align:left;border:1px solid #2d4a6f;min-width:200px;position:sticky;left:0;background:#1e3a5f;z-index:2;">Nama Siswa</th>
                    @foreach($absensiList as $ah)
                    <th style="padding:6px 4px;text-align:center;border:1px solid #2d4a6f;min-width:40px;">
                        {{ $ah->tanggal->format('d') }}<br>
                        <span style="font-size:9px;opacity:.8;">{{ $ah->tanggal->translatedFormat('D') }}</span>
                    </th>
                    @endforeach
                    <th style="padding:6px 8px;text-align:center;border:1px solid #2d4a6f;min-width:35px;background:#16a34a;">H</th>
                    <th style="padding:6px 8px;text-align:center;border:1px solid #2d4a6f;min-width:35px;background:#0284c7;">S</th>
                    <th style="padding:6px 8px;text-align:center;border:1px solid #2d4a6f;min-width:35px;background:#d97706;">I</th>
                    <th style="padding:6px 8px;text-align:center;border:1px solid #2d4a6f;min-width:35px;background:#dc2626;">A</th>
                    <th style="padding:6px 8px;text-align:center;border:1px solid #2d4a6f;min-width:35px;background:#7c3aed;">D</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswas as $siswa)
                @php
                    $counts = ['H'=>0,'S'=>0,'I'=>0,'A'=>0,'D'=>0];
                    foreach($absensiList as $ah) {
                        $s = $absensiMap[$ah->id][$siswa->id] ?? null;
                        if($s && isset($counts[$s])) $counts[$s]++;
                    }
                @endphp
                <tr>
                    <td style="padding:5px 10px;border:1px solid #e2e8f0;font-weight:600;font-size:12px;position:sticky;left:0;background:#fff;z-index:1;">
                        {{ $siswa->nama }}
                    </td>
                    @foreach($absensiList as $ah)
                    @php $st = $absensiMap[$ah->id][$siswa->id] ?? '-'; @endphp
                    <td style="padding:4px;text-align:center;border:1px solid #e2e8f0;
                        background:{{ $statusColors[$st] ?? '#fff' }};
                        color:{{ $statusText[$st] ?? '#94a3b8' }};
                        font-weight:700;font-size:11px;">
                        {{ $st }}
                    </td>
                    @endforeach
                    <td style="padding:4px;text-align:center;border:1px solid #e2e8f0;font-weight:700;color:#16a34a;">{{ $counts['H'] }}</td>
                    <td style="padding:4px;text-align:center;border:1px solid #e2e8f0;font-weight:700;color:#0284c7;">{{ $counts['S'] }}</td>
                    <td style="padding:4px;text-align:center;border:1px solid #e2e8f0;font-weight:700;color:#d97706;">{{ $counts['I'] }}</td>
                    <td style="padding:4px;text-align:center;border:1px solid #e2e8f0;font-weight:700;color:#dc2626;">{{ $counts['A'] }}</td>
                    <td style="padding:4px;text-align:center;border:1px solid #e2e8f0;font-weight:700;color:#7c3aed;">{{ $counts['D'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@else
<div class="card">
    <div class="card-body" style="text-align:center;padding:50px;color:#94a3b8;">
        <i class="bi bi-calendar-x" style="font-size:40px;display:block;margin-bottom:10px;"></i>
        Belum ada data absensi untuk kelas ini
    </div>
</div>
@endif

{{-- Modal Cetak --}}
<div id="modal-cetak" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:460px;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;">🖨️ Cetak Daftar Hadir</h3>
            <button onclick="document.getElementById('modal-cetak').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div style="padding:24px;">
            <form id="form-pdf" target="_blank">
                <input type="hidden" name="semester_id" value="{{ $semesterId }}">
                <input type="hidden" name="rombel_id" value="{{ $rombelId }}">
                <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="background:#f8fafc;border-radius:8px;padding:14px;">
                        <p style="font-size:12px;font-weight:700;color:#6366f1;margin-bottom:10px;">WALI KELAS</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">NAMA</label>
                                <input type="text" name="nama_wali" value="{{ $rombel->wali_kelas }}" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                            </div>
                            <div>
                                <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">NIP</label>
                                <input type="text" name="nip_wali" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                            </div>
                        </div>
                    </div>
                    <div style="background:#f8fafc;border-radius:8px;padding:14px;">
                        <p style="font-size:12px;font-weight:700;color:#dc2626;margin-bottom:10px;">KEPALA SEKOLAH</p>
                        <div>
                            <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">NAMA <span style="color:red">*</span></label>
                            <input type="text" name="nama_kepsek" required placeholder="Nama Kepala Sekolah" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;margin-bottom:8px;">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                <div>
                                    <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">NIP</label>
                                    <input type="text" name="nip_kepsek" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">GOLONGAN</label>
                                    <input type="text" name="golongan_kepsek" placeholder="IV/a" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">TEMPAT</label>
                            <input type="text" name="tempat_ttd" value="{{ \App\Models\Setting::get('kabupaten') }}" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">TANGGAL</label>
                            <input type="date" name="tanggal_ttd" value="{{ date('Y-m-d') }}" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" onclick="document.getElementById('modal-cetak').style.display='none'" class="btn" style="background:#f1f5f9;color:#374151;">Batal</button>
                        <button type="submit" class="btn" style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;"><i class="bi bi-file-earmark-pdf-fill"></i> Generate PDF</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('form-pdf').addEventListener('submit', function(e) {
    e.preventDefault();
    const params = new URLSearchParams(new FormData(this));
    window.open('{{ route("laporan.absensi.detail-kelas.pdf") }}?' + params.toString(), '_blank');
    document.getElementById('modal-cetak').style.display = 'none';
});
document.getElementById('modal-cetak').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endif
@endsection
