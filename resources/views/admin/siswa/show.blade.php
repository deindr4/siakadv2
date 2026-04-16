@extends('layouts.app')

@section('page-title', 'Detail Siswa')
@section('page-subtitle', $siswa->nama)

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>👨‍🎓 Detail Siswa</h1>
    <p>Informasi lengkap data siswa</p>
</div>

<div class="grid grid-2" style="margin-bottom:24px;align-items:start;">

    {{-- Kartu Profil --}}
    <div class="card">
        <div class="card-body" style="text-align:center;padding:32px 24px;">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,{{ $siswa->jenis_kelamin === 'L' ? '#6366f1,#8b5cf6' : '#ec4899,#f43f5e' }});display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:28px;margin:0 auto 16px;">
                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
            </div>
            <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:4px;">{{ $siswa->nama }}</h2>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:16px;">{{ $siswa->nisn ? 'NISN: '.$siswa->nisn : 'Tidak ada NISN' }}</p>

            <div style="display:flex;justify-content:center;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
                @if($siswa->jenis_kelamin === 'L')
                    <span class="badge badge-primary">👦 Laki-laki</span>
                @else
                    <span class="badge" style="background:#fce7f3;color:#be185d;">👧 Perempuan</span>
                @endif

                @php
                    $statusColor = match($siswa->status_mutasi) {
                        'aktif'          => 'background:#dcfce7;color:#16a34a;',
                        'mutasi_masuk'   => 'background:#dbeafe;color:#1d4ed8;',
                        'mutasi_keluar'  => 'background:#fef3c7;color:#d97706;',
                        'putus_sekolah'  => 'background:#fee2e2;color:#dc2626;',
                        'berhenti'       => 'background:#fee2e2;color:#dc2626;',
                        'lulus'          => 'background:#ede9fe;color:#7c3aed;',
                        default          => 'background:#f1f5f9;color:#64748b;',
                    };
                    $statusLabel = match($siswa->status_mutasi) {
                        'aktif'          => '✅ Aktif',
                        'mutasi_masuk'   => '➡️ Mutasi Masuk',
                        'mutasi_keluar'  => '⬅️ Mutasi Keluar',
                        'putus_sekolah'  => '❌ Putus Sekolah',
                        'berhenti'       => '🚫 Berhenti',
                        'lulus'          => '🎓 Lulus',
                        default          => $siswa->status_mutasi,
                    };
                @endphp
                <span class="badge" style="{{ $statusColor }}">{{ $statusLabel }}</span>

                @if($siswa->sumber_data === 'dapodik')
                    <span class="badge badge-success">Dapodik</span>
                @elseif($siswa->sumber_data === 'excel')
                    <span class="badge badge-warning">Excel</span>
                @else
                    <span class="badge badge-primary">Manual</span>
                @endif
            </div>

            <div style="display:flex;gap:8px;justify-content:center;">
                <a href="{{ route('admin.siswa.edit', $siswa) }}" class="btn btn-primary">
                    <i class="bi bi-pencil-fill"></i> Edit
                </a>
                <a href="{{ route('admin.siswa.index') }}" class="btn" style="background:#f1f5f9;color:#374151;">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Info Akademik --}}
    <div class="card">
        <div class="card-header"><h3>🏫 Info Akademik</h3></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                @foreach([
                    ['Rombel', $siswa->nama_rombel],
                    ['Tingkat', $siswa->tingkat_pendidikan_id ? 'Kelas '.$siswa->tingkat_pendidikan_id : null],
                    ['Kurikulum', $siswa->kurikulum],
                    ['NIPD', $siswa->nipd],
                    ['Jenis Pendaftaran', $siswa->jenis_pendaftaran],
                    ['Tgl Masuk Sekolah', $siswa->tanggal_masuk_sekolah?->format('d/m/Y')],
                    ['Sekolah Asal', $siswa->sekolah_asal],
                    ['Semester', $siswa->semester?->nama],
                ] as [$label, $value])
                <div style="background:#f8fafc;padding:10px 12px;border-radius:8px;">
                    <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ $label }}</p>
                    <p style="font-size:13px;font-weight:600;color:#374151;margin-top:2px;">{{ $value ?? '-' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="grid grid-2" style="margin-bottom:24px;align-items:start;">

    {{-- Identitas Diri --}}
    <div class="card">
        <div class="card-header"><h3>🪪 Identitas Diri</h3></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                @foreach([
                    ['NIK', $siswa->nik],
                    ['Tempat Lahir', $siswa->tempat_lahir],
                    ['Tanggal Lahir', $siswa->tanggal_lahir?->format('d/m/Y')],
                    ['Agama', $siswa->agama],
                    ['Anak ke-', $siswa->anak_keberapa],
                    ['Kebutuhan Khusus', $siswa->kebutuhan_khusus],
                    ['Tinggi Badan', $siswa->tinggi_badan ? $siswa->tinggi_badan.' cm' : null],
                    ['Berat Badan', $siswa->berat_badan ? $siswa->berat_badan.' kg' : null],
                ] as [$label, $value])
                <div style="background:#f8fafc;padding:10px 12px;border-radius:8px;">
                    <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ $label }}</p>
                    <p style="font-size:13px;font-weight:600;color:#374151;margin-top:2px;">{{ $value ?? '-' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Kontak & Orang Tua --}}
    <div class="card">
        <div class="card-header"><h3>👨‍👩‍👧 Orang Tua / Wali</h3></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                @foreach([
                    ['Nama Ayah', $siswa->nama_ayah],
                    ['Pekerjaan Ayah', $siswa->pekerjaan_ayah],
                    ['Nama Ibu', $siswa->nama_ibu],
                    ['Pekerjaan Ibu', $siswa->pekerjaan_ibu],
                    ['Nama Wali', $siswa->nama_wali],
                    ['Pekerjaan Wali', $siswa->pekerjaan_wali],
                    ['No. HP Ortu', $siswa->no_hp_ortu],
                    ['Email', $siswa->email],
                ] as [$label, $value])
                <div style="background:#f8fafc;padding:10px 12px;border-radius:8px;">
                    <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ $label }}</p>
                    <p style="font-size:13px;font-weight:600;color:#374151;margin-top:2px;">{{ $value ?? '-' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Form Mutasi / Info Mutasi --}}
@if($siswa->status_mutasi === 'aktif')
<div class="card" style="margin-bottom:24px;border-left:4px solid #6366f1;">
    <div class="card-header"><h3>🔄 Proses Mutasi Siswa</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.mutasi.store') }}">
            @csrf
            <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;align-items:end;">

                <div>
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">STATUS MUTASI <span style="color:red">*</span></label>
                    <select name="status_mutasi" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Pilih status...</option>
                        <option value="mutasi_masuk">➡️ Mutasi Masuk</option>
                        <option value="mutasi_keluar">⬅️ Mutasi Keluar</option>
                        <option value="putus_sekolah">❌ Putus Sekolah</option>
                        <option value="berhenti">🚫 Berhenti</option>
                        <option value="lulus">🎓 Lulus</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TANGGAL <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_mutasi" required value="{{ date('Y-m-d') }}"
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>

                <div>
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KETERANGAN</label>
                    <input type="text" name="keterangan_mutasi" placeholder="Alasan / keterangan..."
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>

            </div>
            <div style="margin-top:14px;text-align:right;">
                <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Yakin ubah status siswa ini?')"
                    style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                    <i class="bi bi-arrow-left-right"></i> Proses Mutasi
                </button>
            </div>
        </form>
    </div>
</div>

@else
<div class="card" style="margin-bottom:24px;border-left:4px solid #f59e0b;">
    <div class="card-header"><h3>🔄 Info Mutasi</h3></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:16px;">
            <div style="background:#fef3c7;padding:10px 12px;border-radius:8px;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Status</p>
                <p style="font-size:13px;font-weight:600;color:#d97706;margin-top:2px;">{{ $statusLabel }}</p>
            </div>
            <div style="background:#f8fafc;padding:10px 12px;border-radius:8px;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Tanggal</p>
                <p style="font-size:13px;font-weight:600;color:#374151;margin-top:2px;">{{ $siswa->tanggal_mutasi?->format('d/m/Y') ?? '-' }}</p>
            </div>
            <div style="background:#f8fafc;padding:10px 12px;border-radius:8px;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Keterangan</p>
                <p style="font-size:13px;font-weight:600;color:#374151;margin-top:2px;">{{ $siswa->keterangan_mutasi ?? '-' }}</p>
            </div>
        </div>
        <div style="text-align:right;">
            <form method="POST" action="{{ route('admin.mutasi.restore', $siswa) }}"
                onsubmit="return confirm('Kembalikan siswa ini ke status aktif?')" style="display:inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn" style="background:#dcfce7;color:#16a34a;">
                    <i class="bi bi-arrow-counterclockwise"></i> Kembalikan ke Aktif
                </button>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Info Dapodik (jika dari dapodik) --}}
@if($siswa->sumber_data === 'dapodik')
<div class="card">
    <div class="card-header"><h3>🔄 Info Sinkronisasi Dapodik</h3></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div style="background:#f8fafc;padding:10px 12px;border-radius:8px;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Peserta Didik ID</p>
                <p style="font-size:12px;font-weight:600;color:#374151;margin-top:2px;font-family:monospace;">{{ $siswa->peserta_didik_id ?? '-' }}</p>
            </div>
            <div style="background:#f8fafc;padding:10px 12px;border-radius:8px;">
                <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Terakhir Sync</p>
                <p style="font-size:13px;font-weight:600;color:#374151;margin-top:2px;">{{ $siswa->last_sync_dapodik?->format('d/m/Y H:i') ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
