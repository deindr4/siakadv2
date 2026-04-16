@extends('layouts.app')
@section('page-title', 'Laporan Prestasi Siswa')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
<div class="page-title">
    <h1>📊 Laporan Prestasi Siswa</h1>
    <p>Rekap dan detail prestasi akademik & non-akademik siswa</p>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:160px;">
                <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">SEMESTER</label>
                <select name="semester_id" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                            {{ $sem->nama }}{{ $sem->is_aktif ? ' ✅' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:150px;">
                <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">KATEGORI</label>
                <select name="kategori_id" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ $kategoriId == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:140px;">
                <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">TINGKAT</label>
                <select name="tingkat" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">Semua Tingkat</option>
                    @foreach(['sekolah'=>'Sekolah','kecamatan'=>'Kecamatan','kabupaten'=>'Kabupaten/Kota','provinsi'=>'Provinsi','nasional'=>'Nasional','internasional'=>'Internasional'] as $v=>$l)
                        <option value="{{ $v }}" {{ request('tingkat') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:140px;">
                <label style="font-size:11px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">KELAS</label>
                <select name="rombel_id" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">Semua Kelas</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
                <a href="{{ route('laporan.prestasi') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Stats Cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
    <div style="background:linear-gradient(135deg,#6366f1,#4f46e5);border-radius:14px;padding:18px;color:#fff;">
        <p style="font-size:11px;opacity:.8;font-weight:700;text-transform:uppercase;">Total Prestasi</p>
        <p style="font-size:32px;font-weight:800;margin-top:4px;">{{ $stats->total }}</p>
        <p style="font-size:11px;opacity:.7;">Terverifikasi</p>
    </div>
    <div style="background:linear-gradient(135deg,#dc2626,#b91c1c);border-radius:14px;padding:18px;color:#fff;">
        <p style="font-size:11px;opacity:.8;font-weight:700;text-transform:uppercase;">Nasional / Internasional</p>
        <p style="font-size:32px;font-weight:800;margin-top:4px;">{{ $stats->nasional_up }}</p>
        <p style="font-size:11px;opacity:.7;">Prestasi tertinggi</p>
    </div>
    <div style="background:linear-gradient(135deg,#d97706,#b45309);border-radius:14px;padding:18px;color:#fff;">
        <p style="font-size:11px;opacity:.8;font-weight:700;text-transform:uppercase;">Tingkat Provinsi</p>
        <p style="font-size:32px;font-weight:800;margin-top:4px;">{{ $stats->provinsi }}</p>
        <p style="font-size:11px;opacity:.7;">Prestasi</p>
    </div>
    <div style="background:linear-gradient(135deg,#16a34a,#15803d);border-radius:14px;padding:18px;color:#fff;">
        <p style="font-size:11px;opacity:.8;font-weight:700;text-transform:uppercase;">Total Siswa Berprestasi</p>
        <p style="font-size:32px;font-weight:800;margin-top:4px;">{{ $stats->total_siswa }}</p>
        <p style="font-size:11px;opacity:.7;">Siswa</p>
    </div>
</div>

{{-- Export Buttons --}}
@if($stats->total > 0)
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <span style="font-size:13px;font-weight:700;color:#374151;">Export:</span>
            <a href="{{ route('laporan.prestasi.excel', request()->all()) }}"
                class="btn" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;">
                <i class="bi bi-file-earmark-excel-fill"></i> Excel (4 Sheet)
            </a>
            <button onclick="showPdfModal('detail')" class="btn" style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF Detail
            </button>
            <button onclick="showPdfModal('kategori')" class="btn" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF Rekap Kategori
            </button>
            <button onclick="showPdfModal('tingkat')" class="btn" style="background:linear-gradient(135deg,#d97706,#b45309);color:#fff;">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF Rekap Tingkat
            </button>
            <button onclick="showPdfModal('siswa')" class="btn" style="background:linear-gradient(135deg,#0284c7,#0369a1);color:#fff;">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF Rekap Siswa
            </button>
        </div>
    </div>
</div>
@endif

{{-- TAB Navigation --}}
<div style="display:flex;gap:4px;margin-bottom:0;border-bottom:2px solid #e2e8f0;">
    @foreach(['detail'=>'📋 Detail Prestasi','kategori'=>'🏷️ Per Kategori','tingkat'=>'🎯 Per Tingkat','siswa'=>'👥 Per Siswa'] as $tab=>$label)
    <button onclick="switchTab('{{ $tab }}')" id="tab-btn-{{ $tab }}"
        style="padding:10px 18px;border:none;border-radius:8px 8px 0 0;font-size:13px;font-weight:600;cursor:pointer;transition:.15s;
        background:{{ $loop->first ? '#6366f1' : '#f8fafc' }};color:{{ $loop->first ? '#fff' : '#64748b' }};">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- TAB: Detail --}}
<div id="tab-detail" class="tab-content">
    <div class="card" style="border-radius:0 8px 8px 8px;">
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Lomba</th>
                            <th>Kategori</th>
                            <th>Tingkat</th>
                            <th>Juara</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailPrestasi as $i => $p)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>
                                <div style="font-weight:700;font-size:13px;">{{ $p->nama_lomba }}</div>
                                @if($p->penyelenggara)<div style="font-size:11px;color:#94a3b8;">{{ $p->penyelenggara }}</div>@endif
                                @if($p->tipe==='tim' && $p->nama_tim)<div style="font-size:11px;color:#6366f1;"><i class="bi bi-people-fill"></i> {{ $p->nama_tim }}</div>@endif
                            </td>
                            <td>
                                @if($p->kategori)
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $p->kategori->warna }}22;color:{{ $p->kategori->warna }};">{{ $p->kategori->nama }}</span>
                                @else <span style="color:#94a3b8;font-size:12px;">-</span>@endif
                            </td>
                            <td>
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $p->tingkatColor() }}22;color:{{ $p->tingkatColor() }};">{{ $p->tingkatLabel() }}</span>
                            </td>
                            <td style="font-weight:700;color:#f59e0b;">🥇 {{ $p->juara }}</td>
                            <td style="font-size:12px;">
                                @foreach($p->siswas->take(2) as $s)<div>{{ $s->nama }}</div>@endforeach
                                @if($p->siswas->count()>2)<div style="color:#6366f1;font-size:11px;">+{{ $p->siswas->count()-2 }} lainnya</div>@endif
                            </td>
                            <td style="font-size:12px;">{{ $p->siswas->pluck('nama_rombel')->unique()->join(', ') }}</td>
                            <td style="font-size:12px;white-space:nowrap;">{{ $p->tanggal->translatedFormat('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">Tidak ada data prestasi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- TAB: Per Kategori --}}
<div id="tab-kategori" class="tab-content" style="display:none;">
    <div class="card" style="border-radius:0 8px 8px 8px;">
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;">Individu</th>
                            <th style="text-align:center;">Tim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapKategori as $i => $r)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>
                                @if($r->kategori)
                                <span style="padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $r->kategori->warna }}22;color:{{ $r->kategori->warna }};">{{ $r->kategori->nama }}</span>
                                @else <span style="color:#94a3b8;">Tanpa Kategori</span>@endif
                            </td>
                            <td style="font-size:12px;">{{ $r->kategori?->jenisLabel() ?? '-' }}</td>
                            <td style="text-align:center;font-weight:800;font-size:18px;color:#6366f1;">{{ $r->total }}</td>
                            <td style="text-align:center;font-weight:600;">{{ $r->individu }}</td>
                            <td style="text-align:center;font-weight:600;">{{ $r->tim }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- TAB: Per Tingkat --}}
<div id="tab-tingkat" class="tab-content" style="display:none;">
    <div class="card" style="border-radius:0 8px 8px 8px;">
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tingkat</th>
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;color:#f59e0b;">Juara 1</th>
                            <th style="text-align:center;color:#94a3b8;">Juara 2</th>
                            <th style="text-align:center;color:#d97706;">Juara 3</th>
                            <th style="text-align:center;">Lainnya</th>
                            <th style="text-align:center;">Individu</th>
                            <th style="text-align:center;">Tim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapTingkat as $i => $r)
                        @php
                            $tingkatColors = ['sekolah'=>'#64748b','kecamatan'=>'#0891b2','kabupaten'=>'#16a34a','provinsi'=>'#d97706','nasional'=>'#dc2626','internasional'=>'#7c3aed'];
                            $c = $tingkatColors[$r->tingkat] ?? '#374151';
                        @endphp
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td><span style="padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $c }}22;color:{{ $c }};">{{ ucfirst($r->tingkat) }}</span></td>
                            <td style="text-align:center;font-weight:800;font-size:18px;color:{{ $c }};">{{ $r->total }}</td>
                            <td style="text-align:center;font-weight:700;color:#f59e0b;">{{ $r->juara1 }}</td>
                            <td style="text-align:center;font-weight:700;color:#94a3b8;">{{ $r->juara2 }}</td>
                            <td style="text-align:center;font-weight:700;color:#d97706;">{{ $r->juara3 }}</td>
                            <td style="text-align:center;">{{ $r->lainnya }}</td>
                            <td style="text-align:center;">{{ $r->individu ?? ($r->total - ($r->tim ?? 0)) }}</td>
                            <td style="text-align:center;">{{ $r->tim ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- TAB: Per Siswa --}}
<div id="tab-siswa" class="tab-content" style="display:none;">
    <div class="card" style="border-radius:0 8px 8px 8px;">
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;">Nasional/Int</th>
                            <th style="text-align:center;">Provinsi</th>
                            <th>Prestasi Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapSiswa as $i => $r)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td style="font-weight:700;font-size:13px;">{{ $r->siswa?->nama }}</td>
                            <td style="font-size:12px;color:#64748b;">{{ $r->siswa?->nisn ?? '-' }}</td>
                            <td style="font-size:12px;">{{ $r->siswa?->nama_rombel }}</td>
                            <td style="text-align:center;font-weight:800;font-size:20px;color:#6366f1;">{{ $r->total }}</td>
                            <td style="text-align:center;">
                                @if($r->nasional > 0)
                                <span style="padding:2px 8px;border-radius:20px;font-size:12px;font-weight:700;background:#fee2e2;color:#dc2626;">{{ $r->nasional }}</span>
                                @else <span style="color:#94a3b8;">-</span>@endif
                            </td>
                            <td style="text-align:center;">
                                @if($r->provinsi > 0)
                                <span style="padding:2px 8px;border-radius:20px;font-size:12px;font-weight:700;background:#fef3c7;color:#d97706;">{{ $r->provinsi }}</span>
                                @else <span style="color:#94a3b8;">-</span>@endif
                            </td>
                            <td style="font-size:11px;color:#64748b;">
                                {{ $r->prestasi->first()?->prestasi?->nama_lomba ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal PDF --}}
<div id="modal-pdf" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:460px;">
        <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 id="modal-pdf-title" style="font-size:16px;font-weight:700;">🖨️ Cetak PDF</h3>
            <button onclick="document.getElementById('modal-pdf').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div style="padding:24px;">
            <form id="form-pdf">
                <input type="hidden" name="semester_id" value="{{ $semesterId }}">
                <input type="hidden" name="kategori_id" value="{{ $kategoriId }}">
                <input type="hidden" name="tingkat" value="{{ $tingkat }}">
                <input type="hidden" name="rombel_id" value="{{ $rombelId }}">
                <input type="hidden" name="jenis" id="input-jenis-pdf" value="detail">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="background:#f8fafc;border-radius:8px;padding:14px;">
                        <p style="font-size:12px;font-weight:700;color:#dc2626;margin-bottom:10px;">KEPALA SEKOLAH</p>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <input type="text" name="nama_kepsek" required placeholder="Nama Kepala Sekolah *"
                                style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                                <input type="text" name="nip_kepsek" placeholder="NIP"
                                    style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
                                <input type="text" name="golongan_kepsek" placeholder="Golongan"
                                    style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;outline:none;font-family:inherit;">
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
                        <button type="button" onclick="document.getElementById('modal-pdf').style.display='none'"
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
// Tab switching
const tabColors = { detail:'#6366f1', kategori:'#16a34a', tingkat:'#d97706', siswa:'#0284c7' };
function switchTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
        btn.style.background = '#f8fafc';
        btn.style.color = '#64748b';
    });
    const btn = document.getElementById('tab-btn-' + name);
    btn.style.background = tabColors[name] || '#6366f1';
    btn.style.color = '#fff';
}

// PDF Modal
const pdfTitles = { detail:'PDF Detail Prestasi', kategori:'PDF Rekap Per Kategori', tingkat:'PDF Rekap Per Tingkat', siswa:'PDF Rekap Per Siswa' };
function showPdfModal(jenis) {
    document.getElementById('input-jenis-pdf').value = jenis;
    document.getElementById('modal-pdf-title').textContent = '🖨️ ' + (pdfTitles[jenis] || 'Cetak PDF');
    document.getElementById('modal-pdf').style.display = 'flex';
}

document.getElementById('form-pdf').addEventListener('submit', function(e) {
    e.preventDefault();
    const params = new URLSearchParams(new FormData(this));
    window.open('{{ route("laporan.prestasi.pdf") }}?' + params.toString(), '_blank');
    document.getElementById('modal-pdf').style.display = 'none';
});

document.getElementById('modal-pdf').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endsection
