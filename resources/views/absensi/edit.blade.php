@extends('layouts.app')

@section('page-title', 'Edit Absensi')
@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>✏️ Edit Absensi</h1>
    <p>{{ $absensi->tanggal?->translatedFormat('l, d F Y') }} — {{ $absensi->nama_rombel }}</p>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;">
    @foreach($errors->all() as $e)
        <p style="font-size:13px;color:#dc2626;">• {{ $e }}</p>
    @endforeach
</div>
@endif

<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#d97706;font-weight:600;">
    ⚠️ Mode Edit Admin — Perubahan akan langsung tersimpan
</div>

<form method="POST" action="{{ route('admin.absensi.update', $absensi->id) }}" id="form-edit">
@csrf @method('PUT')

{{-- Info --}}
<div class="card" style="margin-bottom:16px;border-left:4px solid #f59e0b;">
    <div class="card-body">
        <div style="display:flex;gap:20px;flex-wrap:wrap;align-items:center;">
            <div>
                <p style="font-size:11px;color:#94a3b8;font-weight:700;">KELAS</p>
                <p style="font-size:16px;font-weight:800;color:#6366f1;">{{ $absensi->nama_rombel }}</p>
            </div>
            <div>
                <p style="font-size:11px;color:#94a3b8;font-weight:700;">DIABSEN OLEH</p>
                <p style="font-size:14px;font-weight:700;">{{ $absensi->nama_guru ?? '-' }}</p>
            </div>
            <div style="margin-left:auto;display:flex;align-items:center;gap:10px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;">
                    <input type="checkbox" name="is_locked" value="1" {{ $absensi->is_locked ? 'checked' : '' }}
                        style="width:16px;height:16px;accent-color:#dc2626;">
                    🔒 Kunci Absensi
                </label>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Edit --}}
<div class="card">
    <div class="card-header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <h3>👥 Edit Kehadiran Siswa</h3>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                @foreach(['H' => ['Hadir','#16a34a','#dcfce7'], 'S' => ['Sakit','#0284c7','#e0f2fe'], 'I' => ['Izin','#d97706','#fef3c7'], 'A' => ['Alpa','#dc2626','#fee2e2'], 'D' => ['Dispensasi','#7c3aed','#ede9fe']] as $kode => $info)
                <button type="button" onclick="tandaiSemua('{{ $kode }}')"
                    style="padding:6px 12px;border-radius:8px;font-size:12px;font-weight:700;border:none;cursor:pointer;background:{{ $info[2] }};color:{{ $info[1] }};">
                    Semua {{ $info[0] }}
                </button>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table id="tabel-edit">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th style="text-align:center;color:#16a34a;">H</th>
                        <th style="text-align:center;color:#0284c7;">S</th>
                        <th style="text-align:center;color:#d97706;">I</th>
                        <th style="text-align:center;color:#dc2626;">A</th>
                        <th style="text-align:center;color:#7c3aed;">D</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($absensi->absensiSiswa->sortBy('siswa.nama') as $i => $as)
                    <tr id="row-{{ $as->siswa_id }}" data-status="{{ $as->status }}">
                        <td style="text-align:center;">{{ $i + 1 }}</td>
                        <td style="font-size:13px;font-weight:600;">{{ $as->siswa?->nama }}</td>
                        @foreach(['H' => '#16a34a', 'S' => '#0284c7', 'I' => '#d97706', 'A' => '#dc2626', 'D' => '#7c3aed'] as $kode => $warna)
                        <td style="text-align:center;">
                            <input type="radio"
                                name="siswa[{{ $as->siswa_id }}][status]"
                                value="{{ $kode }}"
                                {{ $as->status === $kode ? 'checked' : '' }}
                                onchange="updateRow({{ $as->siswa_id }}, '{{ $kode }}')"
                                style="width:18px;height:18px;accent-color:{{ $warna }};cursor:pointer;">
                        </td>
                        @endforeach
                        <td>
                            <input type="text"
                                name="siswa[{{ $as->siswa_id }}][keterangan]"
                                value="{{ $as->keterangan }}"
                                placeholder="Opsional..."
                                style="width:100%;padding:6px 10px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:12px;outline:none;font-family:inherit;">
                        </td>
                    </tr>
                    @endforeach
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
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">CATATAN</label>
                <input type="text" name="catatan" value="{{ $absensi->catatan }}"
                    placeholder="Catatan tambahan..."
                    style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('admin.absensi.show', $absensi->id) }}"
                    class="btn" style="background:#f1f5f9;color:#374151;">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save-fill"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

</form>

<script>
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
        row.dataset.status  = status;
        const c = statusColors[status] || {};
        row.style.background = c.bg || '';
        row.style.borderLeft = '3px solid ' + (c.border || '#e2e8f0');
    }
}

function tandaiSemua(status) {
    document.querySelectorAll('#tabel-edit tbody tr').forEach(row => {
        const siswaId = row.id.replace('row-', '');
        const radio   = row.querySelector(`input[value="${status}"]`);
        if (radio) { radio.checked = true; updateRow(siswaId, status); }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#tabel-edit tbody tr').forEach(row => {
        const s = row.dataset.status;
        if (s) {
            const c = statusColors[s] || {};
            row.style.background = c.bg || '';
            row.style.borderLeft = '3px solid ' + (c.border || '#e2e8f0');
        }
    });
});
</script>
@endsection
