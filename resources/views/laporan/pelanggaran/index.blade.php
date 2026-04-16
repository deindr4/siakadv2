@extends('layouts.app')

@section('page-title', 'Laporan Pelanggaran')
@section('page-subtitle', 'Rekap dan cetak laporan pelanggaran siswa')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>📊 Laporan Pelanggaran Siswa</h1>
    <p>Rekap, cetak, dan export laporan pelanggaran siswa</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Top 10 Rekap --}}
@if($rekapSiswa->count())
<div class="card" style="margin-bottom:20px;border-left:4px solid #ef4444;">
    <div class="card-header"><h3>🏆 Top 10 Poin Pelanggaran Tertinggi</h3></div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Total Kasus</th>
                        <th>Total Poin</th>
                        <th>Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapSiswa as $i => $r)
                    @php
                        $poin = $r->total_poin ?? 0;
                        if ($poin >= 100)    { $level = '🔴 Kritis'; $ls = 'background:#fee2e2;color:#dc2626;'; }
                        elseif ($poin >= 50) { $level = '🟠 Tinggi'; $ls = 'background:#fed7aa;color:#ea580c;'; }
                        elseif ($poin >= 25) { $level = '🟡 Sedang'; $ls = 'background:#fef3c7;color:#d97706;'; }
                        else                 { $level = '🟢 Rendah'; $ls = 'background:#dcfce7;color:#16a34a;'; }
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="font-weight:600;font-size:13px;">{{ $r->siswa?->nama }}</td>
                        <td>{{ $r->siswa?->nama_rombel }}</td>
                        <td style="text-align:center;font-weight:700;color:#6366f1;">{{ $r->total_kasus }}</td>
                        <td style="text-align:center;font-size:18px;font-weight:800;color:#ef4444;">{{ $poin }}</td>
                        <td>
                            <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:600;{{ $ls }}">{{ $level }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h3>🔍 Filter Laporan</h3></div>
    <div class="card-body">
        <form method="GET" id="form-filter">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">

                <div style="flex:1;min-width:180px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">SEMESTER</label>
                    <select name="semester_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                                {{ $sem->nama }} {{ $sem->is_aktif ? '✅' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="flex:1;min-width:120px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">BULAN</label>
                    <select name="bulan" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua</option>
                        @foreach(range(1,12) as $b)
                            <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="flex:1;min-width:160px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">KELAS</label>
                    <select name="rombel" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->rombongan_belajar_id }}" {{ request('rombel') == $r->rombongan_belajar_id ? 'selected' : '' }}>
                                {{ $r->nama_rombel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="flex:1;min-width:120px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">KATEGORI</label>
                    <select name="kategori" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua</option>
                        <option value="ringan" {{ request('kategori') == 'ringan' ? 'selected' : '' }}>🟡 Ringan</option>
                        <option value="sedang" {{ request('kategori') == 'sedang' ? 'selected' : '' }}>🟠 Sedang</option>
                        <option value="berat"  {{ request('kategori') == 'berat'  ? 'selected' : '' }}>🔴 Berat</option>
                    </select>
                </div>

                <div style="flex:2;min-width:160px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">CARI SISWA</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama siswa..."
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
                    <a href="{{ route('laporan.pelanggaran') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tombol Export --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('laporan.pelanggaran.excel', request()->all()) }}"
                class="btn" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>
            <button type="button" onclick="document.getElementById('modal-cetak-pelanggaran').style.display='flex'"
                class="btn" style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;">
                <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
            </button>
            <button type="button" onclick="window.print()"
                class="btn" style="background:linear-gradient(135deg,#0284c7,#0369a1);color:#fff;">
                <i class="bi bi-printer-fill"></i> Print
            </button>
            <span style="font-size:13px;color:#94a3b8;margin-left:8px;">
                Total: <strong>{{ $pelanggaran->total() }}</strong> pelanggaran
            </span>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header">
        <h3>📋 Data Pelanggaran
            <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $pelanggaran->total() }} data</span>
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Pelanggaran</th>
                        <th>Kategori</th>
                        <th>Poin</th>
                        <th>Tindakan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggaran as $i => $p)
                    @php
                        $statusStyle = match($p->status) {
                            'aktif'   => 'background:#fee2e2;color:#dc2626;',
                            'selesai' => 'background:#dcfce7;color:#16a34a;',
                            default   => 'background:#f1f5f9;color:#94a3b8;',
                        };
                    @endphp
                    <tr>
                        <td>{{ $pelanggaran->firstItem() + $i }}</td>
                        <td style="font-size:13px;font-weight:600;">{{ $p->tanggal?->format('d/m/Y') }}</td>
                        <td>
                            <div style="font-size:13px;font-weight:600;">{{ $p->siswa?->nama }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $p->siswa?->nisn }}</div>
                        </td>
                        <td style="font-size:13px;">{{ $p->siswa?->nama_rombel }}</td>
                        <td style="font-size:13px;">{{ $p->jenisPelanggaran?->nama }}</td>
                        <td>
                            <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:600;{{ $p->jenisPelanggaran?->kategoriBadgeStyle() }}">
                                {{ $p->jenisPelanggaran?->kategoriLabel() }}
                            </span>
                        </td>
                        <td style="text-align:center;font-size:18px;font-weight:800;color:#ef4444;">{{ $p->poin }}</td>
                        <td style="font-size:12px;color:#64748b;max-width:150px;">{{ $p->tindakan ?? '-' }}</td>
                        <td>
                            <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:600;{{ $statusStyle }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:50px;color:#94a3b8;">
                            <i class="bi bi-clipboard-x" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                            Tidak ada data pelanggaran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pelanggaran->hasPages())
        <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
            {{ $pelanggaran->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal Cetak PDF Pelanggaran --}}
<div id="modal-cetak-pelanggaran" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;">🖨️ Cetak Laporan Pelanggaran</h3>
            <button onclick="document.getElementById('modal-cetak-pelanggaran').style.display='none'"
                style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <div style="padding:24px;">
            <form id="form-cetak-pelanggaran" target="_blank">
                <input type="hidden" name="semester_id" value="{{ $semesterId }}">
                <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                <input type="hidden" name="rombel" value="{{ request('rombel') }}">

                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div style="background:#f8fafc;border-radius:8px;padding:14px;">
                        <p style="font-size:12px;font-weight:700;color:#dc2626;margin-bottom:10px;">KEPALA SEKOLAH</p>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">NAMA KEPALA SEKOLAH <span style="color:red">*</span></label>
                                <input type="text" name="nama_kepsek" required placeholder="Nama Kepala Sekolah"
                                    style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                <div>
                                    <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">NIP</label>
                                    <input type="text" name="nip_kepsek"
                                        style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">GOLONGAN</label>
                                    <input type="text" name="golongan_kepsek" placeholder="IV/a"
                                        style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">TEMPAT</label>
                            <input type="text" name="tempat_ttd" value="{{ \App\Models\Setting::get('kabupaten') }}"
                                style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">TANGGAL TTD</label>
                            <input type="date" name="tanggal_ttd" value="{{ date('Y-m-d') }}"
                                style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" onclick="document.getElementById('modal-cetak-pelanggaran').style.display='none'"
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
document.getElementById('form-cetak-pelanggaran').addEventListener('submit', function(e) {
    e.preventDefault();
    const params = new URLSearchParams(new FormData(this));
    window.open('{{ route("laporan.pelanggaran.pdf") }}?' + params.toString(), '_blank');
    document.getElementById('modal-cetak-pelanggaran').style.display = 'none';
});

document.getElementById('modal-cetak-pelanggaran').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endsection
