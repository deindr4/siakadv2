@extends('layouts.app')

@section('page-title', 'Detail Alumni')
@section('page-subtitle', $alumni->nama)

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>🎓 Detail Alumni</h1>
    <p>Informasi lengkap data alumni</p>
</div>

<div class="grid grid-2" style="margin-bottom:24px;align-items:start;">

    {{-- Kartu Profil --}}
    <div class="card">
        <div class="card-body" style="text-align:center;padding:32px 24px;">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#6366f1);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:28px;margin:0 auto 16px;">
                {{ strtoupper(substr($alumni->nama, 0, 1)) }}
            </div>
            <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:4px;">{{ $alumni->nama }}</h2>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:16px;">{{ $alumni->nisn ? 'NISN: '.$alumni->nisn : 'Tidak ada NISN' }}</p>

            <div style="display:flex;justify-content:center;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
                @if($alumni->jenis_kelamin === 'L')
                    <span class="badge badge-primary">👦 Laki-laki</span>
                @else
                    <span class="badge" style="background:#fce7f3;color:#be185d;">👧 Perempuan</span>
                @endif
                <span class="badge" style="background:#ede9fe;color:#7c3aed;">🎓 Lulus {{ $alumni->tahun_lulus }}</span>
                @if($alumni->sumber_data === 'dapodik')
                    <span class="badge badge-success">Dapodik</span>
                @elseif($alumni->sumber_data === 'excel')
                    <span class="badge badge-warning">Excel</span>
                @else
                    <span class="badge badge-primary">Manual</span>
                @endif
            </div>

            <div style="display:flex;gap:8px;justify-content:center;">
                <a href="{{ route('admin.alumni.edit', $alumni) }}" class="btn btn-primary">
                    <i class="bi bi-pencil-fill"></i> Edit
                </a>
                <a href="{{ route('admin.alumni.index') }}" class="btn" style="background:#f1f5f9;color:#374151;">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Data Kelulusan --}}
    <div class="card">
        <div class="card-header"><h3>🎓 Data Kelulusan</h3></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                @foreach([
                    ['Tahun Lulus', $alumni->tahun_lulus],
                    ['Tanggal Lulus', $alumni->tanggal_lulus?->format('d/m/Y')],
                    ['No. Ijazah', $alumni->no_ijazah],
                    ['No. SKHUN', $alumni->no_skhun],
                    ['Nilai Rata-rata', $alumni->nilai_rata],
                    ['Kelas Terakhir', $alumni->nama_rombel],
                    ['Tingkat', $alumni->tingkat_pendidikan_id ? 'Kelas '.$alumni->tingkat_pendidikan_id : null],
                    ['Kurikulum', $alumni->kurikulum],
                    ['Semester', $alumni->semester?->nama],
                    ['Keterangan', $alumni->keterangan],
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
                    ['NIK', $alumni->nik],
                    ['Tempat Lahir', $alumni->tempat_lahir],
                    ['Tanggal Lahir', $alumni->tanggal_lahir?->format('d/m/Y')],
                    ['Agama', $alumni->agama],
                    ['Sekolah Asal', $alumni->sekolah_asal],
                    ['No. HP', $alumni->no_hp],
                    ['Email', $alumni->email],
                    ['NIPD', $alumni->nipd],
                ] as [$label, $value])
                <div style="background:#f8fafc;padding:10px 12px;border-radius:8px;">
                    <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ $label }}</p>
                    <p style="font-size:13px;font-weight:600;color:#374151;margin-top:2px;">{{ $value ?? '-' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Orang Tua --}}
    <div class="card">
        <div class="card-header"><h3>👨‍👩‍👧 Orang Tua / Wali</h3></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                @foreach([
                    ['Nama Ayah', $alumni->nama_ayah],
                    ['Nama Ibu', $alumni->nama_ibu],
                    ['Nama Wali', $alumni->nama_wali],
                    ['No. HP Ortu', $alumni->no_hp_ortu],
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
@endsection
