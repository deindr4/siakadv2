@extends('layouts.app')

@section('page-title', 'Laporan Jurnal Mengajar')
@section('page-subtitle', 'Rekap dan cetak laporan jurnal mengajar')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>📊 Laporan Jurnal Mengajar</h1>
    <p>Rekap, cetak, dan export laporan jurnal mengajar</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">
    ✅ {{ session('success') }}
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
                        <option value="">Semua Semester</option>
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
                        <option value="">Semua Bulan</option>
                        @foreach(range(1,12) as $b)
                            <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(!$isGuru)
                <div style="flex:1;min-width:200px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">GURU</label>
                    <select name="guru_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Guru</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div style="flex:1;min-width:160px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">KELAS</label>
                    <select name="rombel" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->nama_rombel }}" {{ request('rombel') == $r->nama_rombel ? 'selected' : '' }}>
                                {{ $r->nama_rombel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
                    <a href="{{ route('laporan.jurnal') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tombol Export & Cetak --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h3>📤 Export & Cetak</h3></div>
    <div class="card-body">
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">

            {{-- Excel --}}
            <a href="{{ route('laporan.jurnal.excel', request()->all()) }}"
                class="btn" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>

            {{-- Tombol buka modal cetak PDF --}}
            <button type="button" onclick="document.getElementById('modal-cetak').style.display='flex'"
                class="btn" style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;">
                <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
            </button>

            {{-- Print langsung --}}
            <button type="button" onclick="printLaporan()"
                class="btn" style="background:linear-gradient(135deg,#0284c7,#0369a1);color:#fff;">
                <i class="bi bi-printer-fill"></i> Print Langsung
            </button>

            <span style="font-size:13px;color:#94a3b8;margin-left:8px;">
                Total: <strong>{{ $totalJurnal }}</strong> jurnal
            </span>
        </div>
    </div>
</div>

{{-- Tabel Rekap --}}
<div class="card">
    <div class="card-header">
        <h3>📋 Daftar Jurnal
            <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $jurnals->total() }} data</span>
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table id="tabel-laporan">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        @if(!$isGuru)<th>Guru</th>@endif
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Pertemuan</th>
                        <th>Jam</th>
                        <th>Materi</th>
                        <th>Hadir</th>
                        <th>TTD</th>
                        @if($isAdmin)<th>Scan</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurnals as $i => $j)
                    <tr>
                        <td>{{ $jurnals->firstItem() + $i }}</td>
                        <td>
                            <div style="font-size:13px;font-weight:600;">{{ $j->tanggal?->format('d/m/Y') }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $j->tanggal?->translatedFormat('l') }}</div>
                        </td>
                        @if(!$isGuru)
                        <td style="font-size:13px;font-weight:600;">{{ $j->guru?->nama }}</td>
                        @endif
                        <td>
                            <div style="font-size:13px;font-weight:600;">{{ $j->mataPelajaran?->nama }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $j->mataPelajaran?->kode }}</div>
                        </td>
                        <td style="font-size:13px;">{{ $j->nama_rombel }}</td>
                        <td style="text-align:center;">
                            <span style="font-size:15px;font-weight:800;color:#6366f1;">{{ $j->pertemuan_ke ?? '-' }}</span>
                        </td>
                        <td style="font-size:12px;color:#64748b;">
                            {{ $j->jam_mulai?->format('H:i') }}
                            @if($j->jam_selesai) - {{ $j->jam_selesai->format('H:i') }} @endif
                        </td>
                        <td style="max-width:200px;">
                            <div style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;" title="{{ $j->materi }}">
                                {{ $j->materi }}
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-size:13px;font-weight:700;color:#16a34a;">{{ $j->jumlah_hadir ?? '-' }}</span>
                        </td>
                        <td style="text-align:center;">
                            @if($j->tanda_tangan)
                                <img src="{{ $j->tanda_tangan }}" style="height:28px;max-width:70px;object-fit:contain;">
                            @else
                                <span style="color:#fca5a5;font-size:11px;">Belum</span>
                            @endif
                        </td>
                        @if($isAdmin)
                        <td style="text-align:center;">
                            @if($j->scan_file)
                                <a href="{{ Storage::url($j->scan_file) }}" target="_blank"
                                    class="btn btn-sm btn-primary" title="Lihat scan">
                                    <i class="bi bi-file-earmark-check-fill"></i>
                                </a>
                            @else
                                <button onclick="uploadScan({{ $j->id }})"
                                    class="btn btn-sm" style="background:#fef3c7;color:#d97706;" title="Upload scan">
                                    <i class="bi bi-upload"></i>
                                </button>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isGuru ? ($isAdmin ? 10 : 9) : ($isAdmin ? 11 : 10) }}"
                            style="text-align:center;padding:50px;color:#94a3b8;">
                            <i class="bi bi-journal-x" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                            Belum ada data jurnal
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jurnals->hasPages())
        <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
            {{ $jurnals->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal Cetak PDF --}}
<div id="modal-cetak" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;z-index:1;">
            <h3 style="font-size:16px;font-weight:700;">🖨️ Pengaturan Cetak PDF</h3>
            <button onclick="document.getElementById('modal-cetak').style.display='none'"
                style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <div style="padding:24px;">
            <form id="form-cetak-pdf" target="_blank">

                {{-- Info filter aktif --}}
                <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:12px;margin-bottom:16px;font-size:12px;color:#0369a1;">
                    <p style="font-weight:700;margin-bottom:4px;">Filter aktif:</p>
                    <p>Semester: {{ $semesters->firstWhere('id', $semesterId)?->nama ?? 'Semua' }}</p>
                    @if(request('bulan'))<p>Bulan: {{ \Carbon\Carbon::create()->month(request('bulan'))->translatedFormat('F') }}</p>@endif
                    @if(request('guru_id') && !$isGuru)<p>Guru: {{ $guruList->firstWhere('id', request('guru_id'))?->nama ?? 'Semua' }}</p>@endif
                </div>

                <input type="hidden" name="semester_id" value="{{ $semesterId }}">
                <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                <input type="hidden" name="guru_id" value="{{ request('guru_id', $guru?->id) }}">
                <input type="hidden" name="rombel" value="{{ request('rombel') }}">

                <div style="display:flex;flex-direction:column;gap:14px;">

                    <p style="font-size:13px;font-weight:700;color:#374151;border-bottom:1px solid #f1f5f9;padding-bottom:8px;">
                        ✍️ Data Tanda Tangan
                    </p>

                    {{-- Guru --}}
                    <div style="background:#f8fafc;border-radius:8px;padding:14px;">
                        <p style="font-size:12px;font-weight:700;color:#6366f1;margin-bottom:10px;">GURU PENGAJAR</p>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <div>
                                <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">NAMA GURU</label>
                                <input type="text" name="nama_guru"
                                    value="{{ $guru?->nama ?? $guruList->firstWhere('id', request('guru_id'))?->nama }}"
                                    style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                <div>
                                    <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">NIP</label>
                                    <input type="text" name="nip_guru"
                                        value="{{ $guru?->nip ?? $guruList->firstWhere('id', request('guru_id'))?->nip }}"
                                        style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">GOLONGAN</label>
                                    <input type="text" name="golongan_guru" placeholder="III/a"
                                        style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kepala Sekolah --}}
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
                                    <input type="text" name="nip_kepsek" placeholder="NIP Kepala Sekolah"
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

                    {{-- Tempat & Tanggal --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">TEMPAT</label>
                            <input type="text" name="tempat_ttd"
                                value="{{ \App\Models\Setting::get('kabupaten') }}"
                                style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">TANGGAL TTD</label>
                            <input type="date" name="tanggal_ttd" value="{{ date('Y-m-d') }}"
                                style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:4px;">
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

{{-- Modal Upload Scan --}}
@if($isAdmin)
<div id="modal-scan" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:420px;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;">📁 Upload Scan Jurnal</h3>
            <button onclick="document.getElementById('modal-scan').style.display='none'"
                style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <div style="padding:24px;">
            <form id="form-scan" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div style="border:2px dashed #e2e8f0;border-radius:10px;padding:20px;text-align:center;cursor:pointer;"
                        onclick="document.getElementById('scan-input').click()">
                        <i class="bi bi-file-earmark-arrow-up" style="font-size:32px;color:#94a3b8;display:block;margin-bottom:8px;"></i>
                        <p style="font-size:13px;color:#64748b;font-weight:600;">Upload file scan jurnal</p>
                        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">PDF, JPG, PNG — Max 10MB</p>
                    </div>
                    <input type="file" id="scan-input" name="scan_file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;"
                        onchange="document.getElementById('scan-filename').textContent = this.files[0]?.name ?? ''">
                    <p id="scan-filename" style="font-size:12px;color:#6366f1;text-align:center;"></p>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" onclick="document.getElementById('modal-scan').style.display='none'"
                            class="btn" style="background:#f1f5f9;color:#374151;">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
// Form cetak PDF - submit ke route dengan params
document.getElementById('form-cetak-pdf').addEventListener('submit', function(e) {
    e.preventDefault();
    const params = new URLSearchParams(new FormData(this));
    window.open('{{ route("laporan.jurnal.pdf") }}?' + params.toString(), '_blank');
    document.getElementById('modal-cetak').style.display = 'none';
});

// Print langsung
function printLaporan() {
    window.print();
}

@if($isAdmin)
function uploadScan(jurnalId) {
    const form = document.getElementById('form-scan');
    form.action = '/laporan/jurnal/' + jurnalId + '/scan';
    document.getElementById('modal-scan').style.display = 'flex';
}
@endif

// Tutup modal klik luar
['modal-cetak', 'modal-scan'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});

// Print style
const style = document.createElement('style');
style.innerHTML = `
@media print {
    .sidebar, .page-title, .card:first-of-type, .card:nth-of-type(2), nav, header { display: none !important; }
    #tabel-laporan { font-size: 11px; }
}`;
document.head.appendChild(style);
</script>
@endsection
