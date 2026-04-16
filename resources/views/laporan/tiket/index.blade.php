@extends('layouts.app')
@section('page-title', 'Laporan Tiket')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
<div class="page-title">
    <h1>📊 Laporan Tiket Kritik, Saran & Pengaduan</h1>
    <p>Rekap tiket tahun {{ $tahun }}</p>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div style="min-width:120px;">
                <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">TAHUN</label>
                <select name="tahun" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:130px;">
                <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">BULAN</label>
                <select name="bulan" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:180px;flex:1;">
                <label style="font-size:11px;font-weight:700;display:block;margin-bottom:4px;">KATEGORI</label>
                <select name="kategori" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $v => $l)
                        <option value="{{ $v }}" {{ $kategori == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
                <a href="{{ route('laporan.tiket') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
    @foreach([
        ['Total Tiket', $stats->total, '#6366f1', 'ticket-perforated-fill'],
        ['Aktif', $stats->open, '#16a34a', 'envelope-open-fill'],
        ['Selesai', $stats->selesai, '#64748b', 'check-circle-fill'],
        ['Dari Siswa', $stats->dari_siswa, '#0284c7', 'person-fill'],
    ] as [$label, $val, $color, $icon])
    <div style="background:{{ $color }}11;border:1.5px solid {{ $color }}33;border-radius:14px;padding:16px 18px;display:flex;align-items:center;gap:14px;">
        <i class="bi bi-{{ $icon }}" style="font-size:26px;color:{{ $color }};"></i>
        <div>
            <p style="font-size:11px;color:{{ $color }};font-weight:700;text-transform:uppercase;">{{ $label }}</p>
            <p style="font-size:26px;font-weight:800;color:{{ $color }};">{{ $val }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Export --}}
@if($stats->total > 0)
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <span style="font-size:13px;font-weight:700;color:#374151;">Export:</span>
            <a href="{{ route('laporan.tiket.excel', request()->all()) }}"
                class="btn" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;">
                <i class="bi bi-file-earmark-excel-fill"></i> Excel (3 Sheet)
            </a>
            <button onclick="showPdfModal('rekap')" class="btn" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF Rekap
            </button>
            <button onclick="showPdfModal('detail')" class="btn" style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF Detail
            </button>
        </div>
    </div>
</div>
@endif

{{-- TAB --}}
<div style="display:flex;gap:4px;margin-bottom:0;border-bottom:2px solid #e2e8f0;">
    @foreach(['kategori'=>'🏷️ Per Kategori','bulan'=>'📅 Per Bulan','detail'=>'📋 Detail Tiket'] as $tab=>$label)
    <button onclick="switchTab('{{ $tab }}')" id="tab-btn-{{ $tab }}"
        style="padding:10px 18px;border:none;border-radius:8px 8px 0 0;font-size:13px;font-weight:600;cursor:pointer;transition:.15s;
        background:{{ $loop->first ? '#6366f1' : '#f8fafc' }};color:{{ $loop->first ? '#fff' : '#64748b' }};">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- TAB: Per Kategori --}}
<div id="tab-kategori" class="tab-content">
    <div class="card" style="border-radius:0 8px 8px 8px;">
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;">Terbuka</th>
                            <th style="text-align:center;">Diproses</th>
                            <th style="text-align:center;">Selesai</th>
                            <th style="text-align:center;">Terkunci</th>
                            <th style="text-align:center;">Dari Siswa</th>
                            <th style="text-align:center;">Dari Guru</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapKategori as $i => $r)
                        @php
                            $dummy = new \App\Models\Tiket(['kategori'=>$r->kategori,'kategori_lainnya'=>$r->kategori_lainnya]);
                            $warna = $dummy->kategoriColor();
                            $label = $dummy->kategoriLabel();
                        @endphp
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>
                                <span style="padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $warna }}22;color:{{ $warna }};">
                                    {{ $label }}
                                </span>
                            </td>
                            <td style="text-align:center;font-weight:800;font-size:18px;color:#6366f1;">{{ $r->total }}</td>
                            <td style="text-align:center;font-weight:600;color:#16a34a;">{{ $r->open }}</td>
                            <td style="text-align:center;font-weight:600;color:#0284c7;">{{ $r->diproses }}</td>
                            <td style="text-align:center;font-weight:600;color:#64748b;">{{ $r->selesai }}</td>
                            <td style="text-align:center;font-weight:600;color:#dc2626;">{{ $r->terkunci }}</td>
                            <td style="text-align:center;">{{ $r->dari_siswa }}</td>
                            <td style="text-align:center;">{{ $r->dari_guru }}</td>
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

{{-- TAB: Per Bulan --}}
<div id="tab-bulan" class="tab-content" style="display:none;">
    <div class="card" style="border-radius:0 8px 8px 8px;">
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;">Aktif</th>
                            <th style="text-align:center;">Selesai</th>
                            <th style="text-align:center;">Dari Siswa</th>
                            <th style="text-align:center;">Dari Guru</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapBulan as $r)
                        @php $pct = $r->total > 0 ? round(($r->selesai / $r->total) * 100) : 0; @endphp
                        <tr>
                            <td style="font-weight:700;">{{ $r->bulan_nama }}</td>
                            <td style="text-align:center;font-weight:800;font-size:18px;color:#6366f1;">{{ $r->total }}</td>
                            <td style="text-align:center;font-weight:600;color:#16a34a;">{{ $r->aktif }}</td>
                            <td style="text-align:center;font-weight:600;color:#64748b;">{{ $r->selesai }}</td>
                            <td style="text-align:center;">{{ $r->dari_siswa }}</td>
                            <td style="text-align:center;">{{ $r->dari_guru }}</td>
                            <td style="min-width:120px;">
                                <div style="background:#f1f5f9;border-radius:20px;height:8px;overflow:hidden;">
                                    <div style="background:#16a34a;width:{{ $pct }}%;height:100%;border-radius:20px;transition:.3s;"></div>
                                </div>
                                <p style="font-size:10px;color:#64748b;margin-top:2px;">{{ $pct }}% selesai</p>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- TAB: Detail Tiket --}}
<div id="tab-detail" class="tab-content" style="display:none;">
    <div class="card" style="border-radius:0 8px 8px 8px;">
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Dari</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th style="text-align:center;">Respon</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailTiket as $i => $t)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>
                                <a href="{{ route('tiket.show', $t) }}" style="color:#6366f1;font-weight:700;text-decoration:none;font-size:13px;">
                                    {{ \Str::limit($t->judul, 45) }}
                                </a>
                            </td>
                            <td>
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $t->kategoriColor() }}22;color:{{ $t->kategoriColor() }};">
                                    {{ $t->kategoriLabel() }}
                                </span>
                            </td>
                            <td style="font-size:12px;">
                                @if($t->is_anonim)
                                    <span style="color:#64748b;">🎭 Anonim</span>
                                    <div style="font-size:10px;color:#6366f1;font-style:italic;">{{ $t->user?->name }}</div>
                                @else
                                    {{ $t->user?->name }}
                                @endif
                                <div style="font-size:10px;color:#94a3b8;">{{ ucfirst($t->role_pembuat) }}</div>
                            </td>
                            <td>
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $t->prioritasColor() }}22;color:{{ $t->prioritasColor() }};">
                                    {{ ucfirst($t->prioritas) }}
                                </span>
                            </td>
                            <td>
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $t->statusBg() }};color:{{ $t->statusColor() }};">
                                    {{ $t->statusLabel() }}
                                </span>
                            </td>
                            <td style="text-align:center;font-weight:700;color:#6366f1;">{{ $t->respon->count() }}</td>
                            <td style="font-size:12px;white-space:nowrap;">{{ $t->created_at->translatedFormat('d M Y') }}</td>
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
                <input type="hidden" name="tahun"    value="{{ $tahun }}">
                <input type="hidden" name="bulan"    value="{{ $bulan }}">
                <input type="hidden" name="kategori" value="{{ $kategori }}">
                <input type="hidden" name="jenis" id="input-jenis-pdf" value="rekap">
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
const tabColors = { kategori:'#6366f1', bulan:'#0284c7', detail:'#374151' };
function switchTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
        btn.style.background = '#f8fafc'; btn.style.color = '#64748b';
    });
    const btn = document.getElementById('tab-btn-' + name);
    btn.style.background = tabColors[name]; btn.style.color = '#fff';
}
function showPdfModal(jenis) {
    document.getElementById('input-jenis-pdf').value = jenis;
    document.getElementById('modal-pdf-title').textContent = '🖨️ PDF ' + (jenis === 'rekap' ? 'Rekap' : 'Detail') + ' Tiket';
    document.getElementById('modal-pdf').style.display = 'flex';
}
document.getElementById('form-pdf').addEventListener('submit', function(e) {
    e.preventDefault();
    const params = new URLSearchParams(new FormData(this));
    window.open('{{ route("laporan.tiket.pdf") }}?' + params.toString(), '_blank');
    document.getElementById('modal-pdf').style.display = 'none';
});
document.getElementById('modal-pdf').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endsection
