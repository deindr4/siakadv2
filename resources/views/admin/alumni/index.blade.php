@extends('layouts.app')

@section('page-title', 'Alumni')
@section('page-subtitle', 'Data siswa yang telah lulus')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>🎓 Alumni</h1>
    <p>Data siswa yang telah lulus</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#6366f1);">🎓</div>
        <div class="stat-info"><h3>{{ $total }}</h3><p>Total Alumni</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">👦</div>
        <div class="stat-info"><h3>{{ $totalL }}</h3><p>Laki-laki</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#ec4899,#f43f5e);">👧</div>
        <div class="stat-info"><h3>{{ $totalP }}</h3><p>Perempuan</p></div>
    </div>
</div>

{{-- Aksi Utama --}}
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <button onclick="document.getElementById('modal-luluskan').style.display='flex'"
        class="btn btn-primary" style="background:linear-gradient(135deg,#8b5cf6,#6366f1);">
        <i class="bi bi-mortarboard-fill"></i> Kelulusan Massal
    </button>
    <button onclick="document.getElementById('modal-import').style.display='flex'"
        class="btn btn-primary" style="background:linear-gradient(135deg,#10b981,#059669);">
        <i class="bi bi-file-earmark-excel-fill"></i> Import Excel
    </button>
    <a href="{{ route('admin.alumni.create') }}" class="btn btn-primary" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
        <i class="bi bi-plus-lg"></i> Tambah Manual
    </a>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.alumni.index') }}">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">

                <div style="flex:1;min-width:160px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">TAHUN LULUS</label>
                    <select name="tahun_lulus" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunLulus as $t)
                            <option value="{{ $t }}" {{ request('tahun_lulus') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="flex:1;min-width:180px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">SEMESTER</label>
                    <select name="semester_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Semester</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>{{ $sem->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="flex:1;min-width:130px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">JENIS KELAMIN</label>
                    <select name="jenis_kelamin" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua</option>
                        <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div style="flex:2;min-width:160px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">CARI</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, NISN, No. Ijazah..."
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('admin.alumni.index') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header">
        <h3>🎓 Daftar Alumni <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $siswas->total() }} alumni</span></h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Alumni</th>
                        <th>NISN</th>
                        <th>Kelas Terakhir</th>
                        <th>Jenis Kelamin</th>
                        <th>Tahun Lulus</th>
                        <th>No. Ijazah</th>
                        <th>Sumber</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $i => $alumni)
                    <tr>
                        <td>{{ $siswas->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#6366f1);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">
                                    {{ strtoupper(substr($alumni->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;">{{ $alumni->nama }}</div>
                                    <div style="font-size:11px;color:#94a3b8;">NIPD: {{ $alumni->nipd ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;font-family:monospace;">{{ $alumni->nisn ?? '-' }}</td>
                        <td>
                            <div style="font-size:13px;font-weight:600;">{{ $alumni->nama_rombel ?? '-' }}</div>
                            <div style="font-size:11px;color:#94a3b8;">Kelas {{ $alumni->tingkat_pendidikan_id ?? '-' }}</div>
                        </td>
                        <td>
                            @if($alumni->jenis_kelamin === 'L')
                                <span class="badge badge-primary">👦 L</span>
                            @else
                                <span class="badge" style="background:#fce7f3;color:#be185d;">👧 P</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background:#ede9fe;color:#7c3aed;">🎓 {{ $alumni->tahun_lulus }}</span>
                        </td>
                        <td style="font-size:12px;font-family:monospace;">{{ $alumni->no_ijazah ?? '-' }}</td>
                        <td>
                            @if($alumni->sumber_data === 'dapodik')
                                <span class="badge badge-success">Dapodik</span>
                            @elseif($alumni->sumber_data === 'excel')
                                <span class="badge badge-warning">Excel</span>
                            @else
                                <span class="badge badge-primary">Manual</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.alumni.show', $alumni) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.alumni.edit', $alumni) }}" class="btn btn-sm" style="background:#fef3c7;color:#d97706;">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:50px;color:#94a3b8;">
                            <i class="bi bi-mortarboard" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                            Belum ada data alumni
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswas->hasPages())
        <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
            {{ $siswas->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL: Kelulusan Massal --}}
<div id="modal-luluskan" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;">
        <div style="padding:24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;">🎓 Kelulusan Massal</h3>
            <button onclick="document.getElementById('modal-luluskan').style.display='none'"
                style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('admin.alumni.luluskan.massal') }}">
                @csrf

                <div style="display:flex;flex-direction:column;gap:16px;">

                    <div style="background:#fef3c7;border-radius:8px;padding:12px;font-size:13px;color:#d97706;">
                        ⚠️ Siswa yang diluluskan akan dipindahkan ke data Alumni dan status berubah menjadi <strong>Lulus</strong>.
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">SEMESTER <span style="color:red">*</span></label>
                        <select name="semester_id" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">Pilih semester...</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ $semesterAktif?->id == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->nama }} {{ $sem->is_aktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">PILIH KELAS <span style="color:red">*</span></label>
                        <div style="border:1.5px solid #e2e8f0;border-radius:8px;padding:10px;max-height:180px;overflow-y:auto;">
                            @forelse($rombels as $r)
                            <label style="display:flex;align-items:center;gap:8px;padding:6px 4px;cursor:pointer;font-size:13px;">
                                <input type="checkbox" name="rombel_ids[]" value="{{ $r->id }}"
                                    style="width:15px;height:15px;accent-color:#6366f1;">
                                <span>{{ $r->nama_rombel }} <span style="color:#94a3b8;">(Kelas {{ $r->tingkat }})</span></span>
                            </label>
                            @empty
                            <p style="font-size:13px;color:#94a3b8;text-align:center;">Tidak ada rombel aktif</p>
                            @endforelse
                        </div>
                        <div style="margin-top:6px;display:flex;gap:8px;">
                            <button type="button" onclick="checkAll(true)" style="font-size:11px;color:#6366f1;background:none;border:none;cursor:pointer;padding:0;">Pilih Semua</button>
                            <span style="color:#e2e8f0;">|</span>
                            <button type="button" onclick="checkAll(false)" style="font-size:11px;color:#94a3b8;background:none;border:none;cursor:pointer;padding:0;">Batal Semua</button>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TAHUN LULUS <span style="color:red">*</span></label>
                            <input type="text" name="tahun_lulus" required placeholder="2025/2026"
                                value="{{ $semesterAktif ? explode('/', $semesterAktif->tahun_ajaran ?? '')[0].'/'.(explode('/', $semesterAktif->tahun_ajaran ?? '')[1] ?? '') : '' }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TANGGAL LULUS <span style="color:red">*</span></label>
                            <input type="date" name="tanggal_lulus" required value="{{ date('Y-m-d') }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;">
                        <button type="button" onclick="document.getElementById('modal-luluskan').style.display='none'"
                            class="btn" style="background:#f1f5f9;color:#374151;">Batal</button>
                        <button type="submit" class="btn btn-primary"
                            style="background:linear-gradient(135deg,#8b5cf6,#6366f1);"
                            onclick="return confirm('Yakin luluskan semua siswa di kelas yang dipilih?')">
                            <i class="bi bi-mortarboard-fill"></i> Proses Kelulusan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: Import Excel --}}
<div id="modal-import" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;">
        <div style="padding:24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;">📥 Import Alumni dari Excel</h3>
            <button onclick="document.getElementById('modal-import').style.display='none'"
                style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('admin.alumni.import') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:flex;flex-direction:column;gap:16px;">

                    <div style="background:#dbeafe;border-radius:8px;padding:12px;font-size:13px;color:#1d4ed8;">
                        📋 Download template Excel terlebih dahulu, isi data alumni, lalu upload kembali.
                    </div>

                    <div>
                        <a href="#" style="font-size:13px;color:#6366f1;font-weight:600;text-decoration:none;">
                            <i class="bi bi-download"></i> Download Template Excel
                        </a>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">UPLOAD FILE <span style="color:red">*</span></label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Format: .xlsx, .xls, .csv (maks 5MB)</p>
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" onclick="document.getElementById('modal-import').style.display='none'"
                            class="btn" style="background:#f1f5f9;color:#374151;">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#10b981,#059669);">
                            <i class="bi bi-upload"></i> Upload & Import
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function checkAll(val) {
    document.querySelectorAll('input[name="rombel_ids[]"]').forEach(cb => cb.checked = val);
}
// Tutup modal klik luar
['modal-luluskan','modal-import'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});
</script>
@endsection
