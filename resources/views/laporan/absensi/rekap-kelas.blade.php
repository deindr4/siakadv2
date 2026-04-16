@extends('layouts.app')
@section('page-title', 'Rekap Absensi Per Kelas')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
<div class="page-title">
    <h1>📊 Rekap Absensi Per Kelas</h1>
    <p>Rekap jumlah kehadiran per kelas per bulan</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">✅ {{ session('success') }}</div>
@endif

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" id="form-filter" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
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
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">KELAS</label>
                <select name="rombel_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">Semua Kelas</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
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
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('laporan.absensi.rekap-kelas') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Export --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('laporan.absensi.rekap-kelas.excel', request()->all()) }}"
                class="btn" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>
            <button type="button" onclick="document.getElementById('modal-cetak').style.display='flex'"
                class="btn" style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;">
                <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
            </button>
            <span style="font-size:13px;color:#94a3b8;">{{ $rekap->count() }} data</span>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header"><h3>📋 Rekap Kehadiran Per Kelas</h3></div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kelas</th>
                        <th>Bulan</th>
                        <th>Total Hari</th>
                        <th style="color:#16a34a;">Hadir</th>
                        <th style="color:#0284c7;">Sakit</th>
                        <th style="color:#d97706;">Izin</th>
                        <th style="color:#dc2626;">Alpa</th>
                        <th style="color:#7c3aed;">Dispensasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $i => $r)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td style="font-weight:600;">{{ $r->nama_rombel }}</td>
                        <td>{{ \Carbon\Carbon::create()->month($r->bulan)->translatedFormat('F') }} {{ $r->tahun }}</td>
                        <td style="text-align:center;font-weight:600;">{{ $r->total_hari }}</td>
                        <td style="text-align:center;font-weight:700;color:#16a34a;">{{ $r->hadir }}</td>
                        <td style="text-align:center;font-weight:700;color:#0284c7;">{{ $r->sakit }}</td>
                        <td style="text-align:center;font-weight:700;color:#d97706;">{{ $r->izin }}</td>
                        <td style="text-align:center;font-weight:700;color:#dc2626;">{{ $r->alpa }}</td>
                        <td style="text-align:center;font-weight:700;color:#7c3aed;">{{ $r->dispensasi }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Cetak --}}
<div id="modal-cetak" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:460px;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;">🖨️ Cetak PDF Rekap Kelas</h3>
            <button onclick="document.getElementById('modal-cetak').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div style="padding:24px;">
            <form id="form-pdf" target="_blank">
                <input type="hidden" name="semester_id" value="{{ $semesterId }}">
                <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                <input type="hidden" name="rombel_id" value="{{ request('rombel_id') }}">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="background:#f8fafc;border-radius:8px;padding:14px;">
                        <p style="font-size:12px;font-weight:700;color:#dc2626;margin-bottom:10px;">KEPALA SEKOLAH</p>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">NAMA <span style="color:red">*</span></label>
                                <input type="text" name="nama_kepsek" required placeholder="Nama Kepala Sekolah"
                                    style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                            </div>
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
    window.open('{{ route("laporan.absensi.rekap-kelas.pdf") }}?' + params.toString(), '_blank');
    document.getElementById('modal-cetak').style.display = 'none';
});
document.getElementById('modal-cetak').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endsection
