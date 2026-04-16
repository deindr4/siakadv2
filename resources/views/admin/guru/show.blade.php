@extends('layouts.app')

@section('page-title', 'Detail Guru')
@section('page-subtitle', $guru->nama)

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>👨‍🏫 Detail Guru</h1>
    <p>Informasi lengkap {{ $guru->nama }}</p>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;align-items:start;">

    {{-- Kartu Profil --}}
    <div class="card">
        <div class="card-body" style="text-align:center;padding:28px 20px;">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,{{ $guru->jenis_kelamin === 'L' ? '#6366f1,#8b5cf6' : '#ec4899,#f43f5e' }});display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:32px;margin:0 auto 16px;">
                {{ strtoupper(substr($guru->nama, 0, 1)) }}
            </div>
            <h2 style="font-size:16px;font-weight:700;color:#0f172a;">{{ $guru->nama }}</h2>
            <p style="font-size:13px;color:#64748b;margin-top:4px;">{{ $guru->jabatan ?? 'Guru' }}</p>

            <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px;">
                @if($guru->status_kepegawaian)
                <span class="badge badge-success" style="font-size:12px;padding:6px 12px;">
                    {{ $guru->status_kepegawaian }}
                </span>
                @endif
                @if($guru->sumber_data === 'dapodik')
                <span class="badge badge-primary" style="font-size:12px;padding:6px 12px;">
                    <i class="bi bi-cloud-check-fill"></i> Data Dapodik
                </span>
                @elseif($guru->sumber_data === 'manual')
                <span class="badge badge-warning" style="font-size:12px;padding:6px 12px;">
                    <i class="bi bi-pencil-fill"></i> Input Manual
                </span>
                @endif
            </div>

            <div style="margin-top:20px;display:flex;flex-direction:column;gap:8px;">
                <a href="{{ route('admin.guru.edit', $guru) }}" class="btn btn-primary" style="width:100%;">
                    <i class="bi bi-pencil-fill"></i> Edit Data
                </a>
                <a href="{{ route('admin.guru.index') }}" class="btn" style="background:#f1f5f9;color:#374151;width:100%;">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Detail Info --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Identitas --}}
        <div class="card">
            <div class="card-header"><h3>📋 Identitas</h3></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    @foreach([
                        ['NIP', $guru->nip],
                        ['NUPTK', $guru->nuptk],
                        ['NIK', $guru->nik],
                        ['Jenis Kelamin', $guru->jk_label],
                        ['Tempat Lahir', $guru->tempat_lahir],
                        ['Tanggal Lahir', $guru->tanggal_lahir?->format('d F Y')],
                        ['Agama', $guru->agama],
                    ] as [$label, $value])
                    <div style="background:#f8fafc;padding:12px;border-radius:8px;">
                        <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.6px;">{{ $label }}</p>
                        <p style="font-size:13px;font-weight:600;color:#374151;margin-top:3px;">{{ $value ?? '-' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Kepegawaian --}}
        <div class="card">
            <div class="card-header"><h3>🏢 Kepegawaian</h3></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    @foreach([
                        ['Jenis PTK', $guru->jenis_ptk],
                        ['Jabatan', $guru->jabatan],
                        ['Status Kepegawaian', $guru->status_kepegawaian],
                        ['Pangkat/Golongan', $guru->pangkat_golongan],
                        ['Pendidikan Terakhir', $guru->pendidikan_terakhir],
                        ['Bidang Studi', $guru->bidang_studi],
                        ['Tahun Ajaran', $guru->tahun_ajaran],
                        ['Tgl. Surat Tugas', $guru->tanggal_surat_tugas?->format('d F Y')],
                    ] as [$label, $value])
                    <div style="background:#f8fafc;padding:12px;border-radius:8px;">
                        <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.6px;">{{ $label }}</p>
                        <p style="font-size:13px;font-weight:600;color:#374151;margin-top:3px;">{{ $value ?? '-' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Kontak --}}
        <div class="card">
            <div class="card-header"><h3>📱 Kontak</h3></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    @foreach([
                        ['No. HP', $guru->no_hp],
                        ['Email', $guru->email],
                    ] as [$label, $value])
                    <div style="background:#f8fafc;padding:12px;border-radius:8px;">
                        <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.6px;">{{ $label }}</p>
                        <p style="font-size:13px;font-weight:600;color:#374151;margin-top:3px;">{{ $value ?? '-' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sync Info --}}
        @if($guru->sumber_data === 'dapodik')
        <div class="card">
            <div class="card-header"><h3>☁️ Info Sinkronisasi Dapodik</h3></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    @foreach([
                        ['PTK ID', $guru->ptk_id],
                        ['PTK Terdaftar ID', $guru->ptk_terdaftar_id],
                        ['Terakhir Sync', $guru->last_sync_dapodik?->format('d F Y H:i')],
                    ] as [$label, $value])
                    <div style="background:#f8fafc;padding:12px;border-radius:8px;">
                        <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.6px;">{{ $label }}</p>
                        <p style="font-size:12px;font-weight:600;color:#374151;margin-top:3px;word-break:break-all;">{{ $value ?? '-' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
