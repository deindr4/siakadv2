@extends('layouts.app')

@section('page-title', 'Input Absensi')
@section('page-subtitle', 'Input absensi harian siswa')

@section('content')
<div class="page-title">
    <h1>📝 Input Absensi Harian</h1>
    <p>Tanggal: <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</strong></p>
</div>

@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#dc2626;font-weight:600;">
    ❌ {{ session('error') }}
</div>
@endif

{{-- Pilih Kelas --}}
@if(!$rombelId)

{{-- Pilih Tanggal (admin & BK bisa mundur, guru hanya hari ini) --}}
@if($canBackdate)
<div class="card" style="margin-bottom:16px;">
    <div class="card-header"><h3>📅 Pilih Tanggal Absensi</h3></div>
    <div class="card-body">
        <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            <div>
                <label style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:4px;">TANGGAL</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}"
                    max="{{ date('Y-m-d') }}"
                    style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
            </div>
            @if(request('rombel_id'))
            <input type="hidden" name="rombel_id" value="{{ request('rombel_id') }}">
            @endif
            <button type="submit" class="btn btn-primary"><i class="bi bi-calendar-check"></i> Terapkan</button>
        </form>
        @if($tanggal !== date('Y-m-d'))
        <div style="margin-top:10px;padding:8px 12px;background:#fef3c7;border-radius:8px;font-size:12px;color:#d97706;font-weight:600;">
            ⚠️ Anda sedang menginput absensi untuk tanggal <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>
        </div>
        @endif
    </div>
</div>
@endif

<div class="card">
    <div class="card-header"><h3>📚 Pilih Kelas</h3></div>
    <div class="card-body">
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            @foreach($rombels as $r)
            <a href="{{ route($routePrefix.'.absensi.create', ['rombel_id' => $r->id, 'tanggal' => $tanggal]) }}"
                style="padding:14px 20px;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;background:#f0f9ff;color:#0369a1;border:2px solid #bae6fd;transition:all .2s;"
                onmouseover="this.style.background='#0369a1';this.style.color='#fff';"
                onmouseout="this.style.background='#f0f9ff';this.style.color='#0369a1';">
                {{ $r->nama_rombel }}
            </a>
            @endforeach
        </div>
    </div>
</div>
@else

{{-- Cek jika terkunci --}}
@if($isLocked)
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:16px 20px;margin-bottom:20px;">
    <p style="font-weight:700;color:#dc2626;font-size:14px;">🔒 Absensi Terkunci</p>
    <p style="font-size:13px;color:#dc2626;margin-top:4px;">
        Absensi kelas <strong>{{ $rombel?->nama_rombel }}</strong> tanggal ini sudah dikunci oleh
        <strong>{{ $existingAbsensi?->nama_guru }}</strong>
        pada pukul <strong>{{ $existingAbsensi?->diabsen_pada?->format('H:i') }}</strong>.
    </p>
    <p style="font-size:12px;color:#94a3b8;margin-top:6px;">Hubungi admin untuk membuka kunci.</p>
</div>
<a href="{{ route($routePrefix.'.absensi.show', $existingAbsensi->id) }}" class="btn btn-primary">
    <i class="bi bi-eye-fill"></i> Lihat Detail Absensi
</a>

@else

{{-- Info kelas --}}
<div class="card" style="margin-bottom:20px;border-left:4px solid #6366f1;">
    <div class="card-body">
        <div style="display:flex;gap:24px;flex-wrap:wrap;align-items:center;">
            <div>
                <p style="font-size:11px;color:#94a3b8;font-weight:700;">KELAS</p>
                <p style="font-size:18px;font-weight:800;color:#6366f1;">{{ $rombel?->nama_rombel }}</p>
            </div>
            <div>
                <p style="font-size:11px;color:#94a3b8;font-weight:700;">JUMLAH SISWA</p>
                <p style="font-size:18px;font-weight:800;">{{ $siswas->count() }}</p>
            </div>
            <div>
                <p style="font-size:11px;color:#94a3b8;font-weight:700;">TANGGAL</p>
                <p style="font-size:14px;font-weight:700;">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p>
            </div>
            @if($existingAbsensi)
            <div style="background:#fef3c7;border-radius:8px;padding:8px 14px;">
                <p style="font-size:11px;color:#d97706;font-weight:700;">⚠️ SUDAH PERNAH DIABSEN</p>
                <p style="font-size:12px;color:#d97706;">Oleh: {{ $existingAbsensi->nama_guru }} pukul {{ $existingAbsensi->diabsen_pada?->format('H:i') }}</p>
                @if($isAdmin)<p style="font-size:11px;color:#d97706;">Admin dapat menimpa data ini.</p>@endif
            </div>
            @endif
        </div>
    </div>
</div>

<form method="POST" action="{{ route($routePrefix.'.absensi.store') }}" id="form-absensi">
@csrf
<input type="hidden" name="semester_id" value="{{ $semesterAktif?->id }}">
<input type="hidden" name="rombongan_belajar_id" value="{{ $rombelId }}">
<input type="hidden" name="tanggal" value="{{ $tanggal }}">

<div class="card">
    <div class="card-header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <h3>👥 Daftar Siswa — {{ $rombel?->nama_rombel }}</h3>
            {{-- Tombol tandai semua --}}
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                @foreach(['H' => ['Hadir','#16a34a','#dcfce7'], 'S' => ['Sakit','#0284c7','#e0f2fe'], 'I' => ['Izin','#d97706','#fef3c7'], 'A' => ['Alpa','#dc2626','#fee2e2'], 'D' => ['Dispensasi','#7c3aed','#ede9fe']] as $kode => $info)
                <button type="button"
                    onclick="tandaiSemua('{{ $kode }}')"
                    style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;border:none;cursor:pointer;background:{{ $info[2] }};color:{{ $info[1] }};">
                    Semua {{ $info[0] }}
                </button>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table id="tabel-absensi">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Siswa</th>
                        <th style="width:80px;">NISN</th>
                        <th style="text-align:center;width:80px;color:#16a34a;">H</th>
                        <th style="text-align:center;width:80px;color:#0284c7;">S</th>
                        <th style="text-align:center;width:80px;color:#d97706;">I</th>
                        <th style="text-align:center;width:80px;color:#dc2626;">A</th>
                        <th style="text-align:center;width:80px;color:#7c3aed;">D</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $i => $siswa)
                    @php
                        $existing = $existingAbsensi?->absensiSiswa->firstWhere('siswa_id', $siswa->id);
                        $currentStatus = $existing?->status ?? 'H';
                    @endphp
                    <tr id="row-{{ $siswa->id }}" data-status="{{ $currentStatus }}">
                        <td style="text-align:center;">{{ $i + 1 }}</td>
                        <td>
                            <div style="font-size:13px;font-weight:600;">{{ $siswa->nama }}</div>
                        </td>
                        <td style="font-size:12px;color:#64748b;">{{ $siswa->nisn ?? '-' }}</td>

                        @foreach(['H' => '#16a34a', 'S' => '#0284c7', 'I' => '#d97706', 'A' => '#dc2626', 'D' => '#7c3aed'] as $kode => $warna)
                        <td style="text-align:center;">
                            <label style="cursor:pointer;display:flex;align-items:center;justify-content:center;">
                                <input type="radio"
                                    name="siswa[{{ $siswa->id }}][status]"
                                    value="{{ $kode }}"
                                    {{ $currentStatus === $kode ? 'checked' : '' }}
                                    onchange="updateRow({{ $siswa->id }}, '{{ $kode }}')"
                                    style="width:18px;height:18px;accent-color:{{ $warna }};cursor:pointer;">
                            </label>
                        </td>
                        @endforeach

                        <td>
                            <input type="text"
                                name="siswa[{{ $siswa->id }}][keterangan]"
                                value="{{ $existing?->keterangan }}"
                                placeholder="Opsional..."
                                style="width:100%;padding:6px 10px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:12px;outline:none;font-family:inherit;">
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">
                            Tidak ada siswa di kelas ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Catatan & Submit --}}
<div class="card" style="margin-top:16px;">
    <div class="card-body">
        <div style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">CATATAN (Opsional)</label>
                <input type="text" name="catatan" placeholder="Catatan tambahan absensi..."
                    style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route($routePrefix.'.absensi.index') }}"
                    class="btn" style="background:#f1f5f9;color:#374151;">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary" id="btn-submit" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                    <i class="bi bi-check-circle-fill"></i> Simpan & Kunci Absensi
                </button>
            </div>
        </div>

        {{-- Summary --}}
        <div style="margin-top:14px;display:flex;gap:12px;flex-wrap:wrap;" id="summary-box">
            @foreach(['H' => ['Hadir','#16a34a','#dcfce7'], 'S' => ['Sakit','#0284c7','#e0f2fe'], 'I' => ['Izin','#d97706','#fef3c7'], 'A' => ['Alpa','#dc2626','#fee2e2'], 'D' => ['Dispensasi','#7c3aed','#ede9fe']] as $kode => $info)
            <div style="padding:8px 16px;border-radius:8px;background:{{ $info[2] }};text-align:center;min-width:80px;">
                <p style="font-size:11px;color:{{ $info[1] }};font-weight:700;">{{ $info[0] }}</p>
                <p id="count-{{ $kode }}" style="font-size:20px;font-weight:800;color:{{ $info[1] }};">0</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

</form>
@endif
@endif

<script>
// Warna row per status
const statusColors = {
    'H': { bg: '#f0fdf4', border: '#bbf7d0' },
    'S': { bg: '#f0f9ff', border: '#bae6fd' },
    'I': { bg: '#fffbeb', border: '#fde68a' },
    'A': { bg: '#fff1f2', border: '#fecdd3' },
    'D': { bg: '#f5f3ff', border: '#ddd6fe' },
};

function updateRow(siswaId, status) {
    const row = document.getElementById('row-' + siswaId);
    if (row) {
        row.dataset.status = status;
        const c = statusColors[status] || {};
        row.style.background    = c.bg || '';
        row.style.borderLeft    = '3px solid ' + (c.border || '#e2e8f0');
    }
    updateSummary();
}

function updateSummary() {
    const counts = { H: 0, S: 0, I: 0, A: 0, D: 0 };
    document.querySelectorAll('#tabel-absensi tbody tr').forEach(row => {
        const s = row.dataset.status;
        if (s && counts[s] !== undefined) counts[s]++;
    });
    Object.keys(counts).forEach(k => {
        const el = document.getElementById('count-' + k);
        if (el) el.textContent = counts[k];
    });
}

function tandaiSemua(status) {
    document.querySelectorAll('#tabel-absensi tbody tr').forEach(row => {
        const siswaId = row.id.replace('row-', '');
        const radio   = row.querySelector(`input[value="${status}"]`);
        if (radio) {
            radio.checked = true;
            updateRow(siswaId, status);
        }
    });
}

// Init warna & summary saat load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#tabel-absensi tbody tr').forEach(row => {
        const s = row.dataset.status;
        if (s) {
            const c = statusColors[s] || {};
            row.style.background = c.bg || '';
            row.style.borderLeft = '3px solid ' + (c.border || '#e2e8f0');
        }
    });
    updateSummary();
});

// Loading state submit
document.getElementById('form-absensi')?.addEventListener('submit', function() {
    const btn = document.getElementById('btn-submit');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
    }
});
</script>
@endsection
