@extends('layouts.app')

@section('page-title', 'Detail Guru')
@section('page-subtitle', $guru->nama)

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<style>
    .gd-page { width: 100%; padding: 1.5rem 2.5rem; }
    @media (max-width: 768px) { .gd-page { padding: 1rem; } }

    /* Header */
    .gd-header { display: flex; align-items: center; justify-content: space-between;
        gap: 12px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .gd-header-left h1 { font-size: 22px; font-weight: 700; color: #111827; }
    .gd-header-left p  { font-size: 13px; color: #6b7280; margin-top: 3px; }
    @media (max-width: 480px) { .gd-header { flex-direction: column; align-items: flex-start; } }

    /* Layout: sidebar + main */
    .gd-layout { display: grid; grid-template-columns: 260px 1fr; gap: 16px; align-items: start; }
    @media (max-width: 768px) { .gd-layout { grid-template-columns: 1fr; } }

    /* Card */
    .gd-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; margin-bottom: 14px; }
    .gd-card-head { display: flex; align-items: center; gap: 8px; padding: 13px 18px; border-bottom: 1px solid #f3f4f6; }
    .gd-card-head-ico { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .gd-card-title { font-size: 13px; font-weight: 700; color: #111827; }
    .gd-card-body  { padding: 18px; }

    /* Profile card */
    .gd-profile { text-align: center; padding: 24px 20px; }
    .gd-avatar-lg { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; color: #fff; font-weight: 800; font-size: 30px; margin: 0 auto 14px; }
    .gd-avatar-lg.laki   { background: linear-gradient(135deg, #534AB7, #3C3489); }
    .gd-avatar-lg.wanita { background: linear-gradient(135deg, #D4537E, #72243E); }
    .gd-profile-name { font-size: 16px; font-weight: 700; color: #111827; }
    .gd-profile-job  { font-size: 13px; color: #6b7280; margin-top: 4px; }
    .gd-profile-badges { display: flex; flex-direction: column; gap: 7px; margin: 16px 0 20px; align-items: center; }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 10px; }
    @media (max-width: 480px) { .info-grid { grid-template-columns: 1fr; } }
    .info-item { background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 10px; padding: 12px 14px; }
    .info-label { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
    .info-value { font-size: 13px; font-weight: 600; color: #111827; word-break: break-word; }
    .info-item.span-2 { grid-column: span 2; }
    @media (max-width: 480px) { .info-item.span-2 { grid-column: span 1; } }

    /* Buttons */
    .btn-pv { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
        border-radius: 8px; border: none; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all .15s; font-family: inherit; text-decoration: none; }
    .btn-primary-pv { background: #3C3489; color: #fff; }
    .btn-primary-pv:hover { background: #26215C; color: #fff; }
    .btn-ghost { background: #f1f5f9; color: #374151; border: 1px solid #e5e7eb; }
    .btn-ghost:hover { background: #e2e8f0; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 4px 12px; border-radius: 99px; white-space: nowrap; }
    .badge-pns    { background: #EEEDFE; color: #3C3489; }
    .badge-pppk   { background: #E1F5EE; color: #0F6E56; }
    .badge-honor  { background: #FAEEDA; color: #854F0B; }
    .badge-dapodik { background: #E1F5EE; color: #0F6E56; }
    .badge-manual  { background: #FAEEDA; color: #854F0B; }
    .badge-sync   { background: #E6F1FB; color: #0C447C; }
</style>

<div class="gd-page">

    {{-- Header --}}
    <div class="gd-header">
        <div class="gd-header-left">
            <h1><i class="bi bi-person-workspace me-2" style="color:#534AB7;font-size:20px;"></i>Detail Guru</h1>
            <p>Informasi lengkap {{ $guru->nama }}</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.guru.index') }}" class="btn-pv btn-ghost">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('admin.guru.edit', $guru) }}" class="btn-pv btn-primary-pv">
                <i class="bi bi-pencil-fill"></i> Edit Data
            </a>
        </div>
    </div>

    <div class="gd-layout">

        {{-- Sidebar: Profil --}}
        <div>
            <div class="gd-card">
                <div class="gd-profile">
                    <div class="gd-avatar-lg {{ $guru->jenis_kelamin === 'L' ? 'laki' : 'wanita' }}">
                        {{ strtoupper(substr($guru->nama, 0, 1)) }}
                    </div>
                    <div class="gd-profile-name">{{ $guru->nama }}</div>
                    <div class="gd-profile-job">{{ $guru->jabatan ?? 'Guru' }}</div>
                    <div class="gd-profile-badges">
                        @if($guru->status_kepegawaian)
                        @php
                            $skClass = match($guru->status_kepegawaian) { 'PNS' => 'badge-pns', 'PPPK' => 'badge-pppk', default => 'badge-honor' };
                        @endphp
                        <span class="badge {{ $skClass }}">{{ $guru->status_kepegawaian }}</span>
                        @endif
                        @if($guru->sumber_data === 'dapodik')
                        <span class="badge badge-dapodik">
                            <i class="bi bi-cloud-check-fill me-1" style="font-size:10px;"></i>Data Dapodik
                        </span>
                        @elseif($guru->sumber_data === 'manual')
                        <span class="badge badge-manual">
                            <i class="bi bi-pencil-fill me-1" style="font-size:10px;"></i>Input Manual
                        </span>
                        @endif
                        @if($guru->jenis_ptk === 'Guru')
                        <span class="badge badge-pns" style="background:#EEEDFE;color:#3C3489;">Guru</span>
                        @elseif($guru->jenis_ptk)
                        <span class="badge badge-honor">Tendik</span>
                        @endif
                    </div>
                    {{-- Quick info --}}
                    @if($guru->no_hp)
                    <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#f8fafc;border-radius:8px;margin-bottom:6px;text-align:left;">
                        <i class="bi bi-telephone-fill" style="color:#6b7280;font-size:13px;flex-shrink:0;"></i>
                        <span style="font-size:13px;color:#374151;">{{ $guru->no_hp }}</span>
                    </div>
                    @endif
                    @if($guru->email)
                    <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#f8fafc;border-radius:8px;text-align:left;">
                        <i class="bi bi-envelope-fill" style="color:#6b7280;font-size:13px;flex-shrink:0;"></i>
                        <span style="font-size:12px;color:#374151;word-break:break-all;">{{ $guru->email }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main: detail sections --}}
        <div>

            {{-- Identitas --}}
            <div class="gd-card">
                <div class="gd-card-head">
                    <div class="gd-card-head-ico" style="background:#EEEDFE;">
                        <i class="bi bi-person-vcard-fill" style="color:#534AB7;font-size:14px;"></i>
                    </div>
                    <span class="gd-card-title">Identitas</span>
                </div>
                <div class="gd-card-body">
                    <div class="info-grid">
                        @foreach([
                            ['NIP',           $guru->nip],
                            ['NUPTK',         $guru->nuptk],
                            ['NIK',           $guru->nik],
                            ['Jenis Kelamin', $guru->jk_label],
                            ['Tempat Lahir',  $guru->tempat_lahir],
                            ['Tanggal Lahir', $guru->tanggal_lahir?->format('d F Y')],
                            ['Agama',         $guru->agama],
                        ] as [$label, $value])
                        <div class="info-item">
                            <div class="info-label">{{ $label }}</div>
                            <div class="info-value">{{ $value ?? '-' }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kepegawaian --}}
            <div class="gd-card">
                <div class="gd-card-head">
                    <div class="gd-card-head-ico" style="background:#E1F5EE;">
                        <i class="bi bi-briefcase-fill" style="color:#0F6E56;font-size:14px;"></i>
                    </div>
                    <span class="gd-card-title">Kepegawaian</span>
                </div>
                <div class="gd-card-body">
                    <div class="info-grid">
                        @foreach([
                            ['Jenis PTK',         $guru->jenis_ptk],
                            ['Jabatan',           $guru->jabatan],
                            ['Status Kepegawaian',$guru->status_kepegawaian],
                            ['Pangkat/Golongan',  $guru->pangkat_golongan],
                            ['Pendidikan Terakhir',$guru->pendidikan_terakhir],
                            ['Bidang Studi',      $guru->bidang_studi],
                            ['Tahun Ajaran',      $guru->tahun_ajaran],
                            ['Tgl. Surat Tugas',  $guru->tanggal_surat_tugas?->format('d F Y')],
                        ] as [$label, $value])
                        <div class="info-item">
                            <div class="info-label">{{ $label }}</div>
                            <div class="info-value">{{ $value ?? '-' }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sinkronisasi Dapodik --}}
            @if($guru->sumber_data === 'dapodik')
            <div class="gd-card">
                <div class="gd-card-head">
                    <div class="gd-card-head-ico" style="background:#E6F1FB;">
                        <i class="bi bi-cloud-check-fill" style="color:#0C447C;font-size:14px;"></i>
                    </div>
                    <span class="gd-card-title">Info Sinkronisasi Dapodik</span>
                </div>
                <div class="gd-card-body">
                    <div class="info-grid">
                        @foreach([
                            ['PTK ID',           $guru->ptk_id],
                            ['PTK Terdaftar ID',  $guru->ptk_terdaftar_id],
                            ['Terakhir Sync',    $guru->last_sync_dapodik?->format('d F Y H:i')],
                        ] as [$label, $value])
                        <div class="info-item">
                            <div class="info-label">{{ $label }}</div>
                            <div class="info-value" style="font-family:monospace;font-size:12px;">{{ $value ?? '-' }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection