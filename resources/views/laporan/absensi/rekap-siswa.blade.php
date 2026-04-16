@extends('layouts.app')
@section('page-title', 'Rekap Absensi Per Siswa')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
<div class="page-title">
    <h1>👥 Rekap Absensi Per Siswa</h1>
    <p>Rekap total kehadiran per siswa — pilih kelas terlebih dahulu</p>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:180px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">SEMESTER</label>
                <select name="semester_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                            {{ $sem->nama }} {{ $sem->is_aktif ? '✅' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:160px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">KELAS <span style="color:#dc2626;">*</span></label>
                <select name="rombel_id" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
                @if($sudahFilter)
                <a href="{{ route('laporan.absensi.rekap-siswa', ['semester_id' => $semesterId]) }}"
                    class="btn" style="background:#f1f5f9;color:#374151;">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

@if(!$sudahFilter)
{{-- Placeholder belum filter --}}
<div class="card">
    <div class="card-body" style="text-align:center;padding:60px 20px;">
        <i class="bi bi-funnel" style="font-size:52px;color:#cbd5e1;display:block;margin-bottom:14px;"></i>
        <p style="font-size:16px;font-weight:700;color:#374151;margin-bottom:6px;">Pilih Kelas untuk Menampilkan Data</p>
        <p style="font-size:13px;color:#94a3b8;">Pilih semester dan kelas di atas, lalu klik <strong>Tampilkan</strong>.<br>Data hanya dimuat setelah kelas dipilih untuk menjaga performa.</p>
    </div>
</div>

@else
{{-- Export --}}
@if($rekapSiswa->isNotEmpty())
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between;">
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('laporan.absensi.rekap-siswa.excel', request()->all()) }}"
                    class="btn" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;">
                    <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
                </a>
                <button type="button" onclick="document.getElementById('modal-cetak').style.display='flex'"
                    class="btn" style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
                </button>
            </div>
            <span style="font-size:13px;color:#94a3b8;font-weight:600;">
                {{ $rekapSiswa->count() }} siswa
                @if($semester) — {{ $semester->nama }} @endif
            </span>
        </div>
    </div>
</div>
@endif

{{-- Stat ringkas --}}
@if($rekapSiswa->isNotEmpty())
@php
    $totalH = $rekapSiswa->sum('hadir');
    $totalS = $rekapSiswa->sum('sakit');
    $totalI = $rekapSiswa->sum('izin');
    $totalA = $rekapSiswa->sum('alpa');
    $totalD = $rekapSiswa->sum('dispensasi');
@endphp
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:20px;">
    @foreach(['H'=>['Hadir',$totalH,'#16a34a','#dcfce7'], 'S'=>['Sakit',$totalS,'#0284c7','#e0f2fe'], 'I'=>['Izin',$totalI,'#d97706','#fef3c7'], 'A'=>['Alpa',$totalA,'#dc2626','#fee2e2'], 'D'=>['Dispensasi',$totalD,'#7c3aed','#ede9fe']] as $info)
    <div style="background:{{ $info[3] }};border-radius:12px;padding:14px;text-align:center;">
        <p style="font-size:11px;color:{{ $info[2] }};font-weight:700;">{{ $info[0] }}</p>
        <p style="font-size:24px;font-weight:800;color:{{ $info[2] }};">{{ $info[1] }}</p>
    </div>
    @endforeach
</div>
@endif

{{-- Tabel --}}
<div class="card">
    <div class="card-header">
        <h3>📋 Rekap Kehadiran Siswa</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th style="color:#16a34a;text-align:center;">H</th>
                        <th style="color:#0284c7;text-align:center;">S</th>
                        <th style="color:#d97706;text-align:center;">I</th>
                        <th style="color:#dc2626;text-align:center;">A</th>
                        <th style="color:#7c3aed;text-align:center;">D</th>
                        <th style="text-align:center;">Total</th>
                        <th style="text-align:center;">%Hadir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapSiswa as $i => $r)
                    @php
                        $pct = $r->total > 0 ? round(($r->hadir / $r->total) * 100) : 0;
                        $pctBg    = $pct >= 90 ? '#dcfce7' : ($pct >= 75 ? '#fef3c7' : '#fee2e2');
                        $pctColor = $pct >= 90 ? '#16a34a' : ($pct >= 75 ? '#d97706' : '#dc2626');
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="font-weight:600;font-size:13px;">{{ $r->siswa->nama }}</td>
                        <td style="font-size:12px;color:#64748b;">{{ $r->siswa->nisn ?? '-' }}</td>
                        <td style="font-size:13px;">{{ $r->siswa->nama_rombel }}</td>
                        <td style="text-align:center;font-weight:700;color:#16a34a;">{{ $r->hadir }}</td>
                        <td style="text-align:center;font-weight:700;color:#0284c7;">{{ $r->sakit }}</td>
                        <td style="text-align:center;font-weight:700;color:#d97706;">{{ $r->izin }}</td>
                        <td style="text-align:center;font-weight:700;color:#dc2626;">{{ $r->alpa }}</td>
                        <td style="text-align:center;font-weight:700;color:#7c3aed;">{{ $r->dispensasi }}</td>
                        <td style="text-align:center;font-weight:600;">{{ $r->total }}</td>
                        <td style="text-align:center;">
                            <span style="font-size:12px;padding:3px 10px;border-radius:20px;font-weight:700;background:{{ $pctBg }};color:{{ $pctColor }};">
                                {{ $pct }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align:center;padding:50px;color:#94a3b8;">
                            <i class="bi bi-clipboard-x" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                            Tidak ada data absensi untuk kelas ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Cetak PDF --}}
<div id="modal-cetak" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:460px;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;">🖨️ Cetak PDF Rekap Siswa</h3>
            <button onclick="document.getElementById('modal-cetak').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div style="padding:24px;">
            <form id="form-pdf">
                <input type="hidden" name="semester_id" value="{{ $semesterId }}">
                <input type="hidden" name="rombel_id" value="{{ $rombelId }}">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="background:#f8fafc;border-radius:8px;padding:14px;">
                        <p style="font-size:12px;font-weight:700;color:#dc2626;margin-bottom:10px;">KEPALA SEKOLAH</p>
                        <div>
                            <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">NAMA <span style="color:red">*</span></label>
                            <input type="text" name="nama_kepsek" required placeholder="Nama Kepala Sekolah"
                                style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;margin-bottom:8px;">
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
                            <input type="text" name="tempat_ttd" value="{{ \App\Models\Setting::get('kabupaten') }}"
                                style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">TANGGAL</label>
                            <input type="date" name="tanggal_ttd" value="{{ date('Y-m-d') }}"
                                style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" onclick="document.getElementById('modal-cetak').style.display='none'"
                            class="btn" style="background:#f1f5f9;color:#374151;">Batal</button>
                        <button type="submit" class="btn" style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Generate PDF
                        </button>
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
    window.open('{{ route("laporan.absensi.rekap-siswa.pdf") }}?' + params.toString(), '_blank');
    document.getElementById('modal-cetak').style.display = 'none';
});
document.getElementById('modal-cetak').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endif

@endsection
