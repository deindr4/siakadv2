{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')
@section('page-title', 'Profil Saya')

@section('content')
<style>
/* ===== PROFILE ===== */
.pf-card { background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:16px; }
.pf-head  { padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px; }
.pf-head h3 { font-size:14px;font-weight:700;color:#0f172a;margin:0; }
.pf-head i  { font-size:17px;color:#6366f1; }
.pf-body  { padding:18px 20px; }
.pf-sep   { border:none;border-top:1px solid #f1f5f9;margin:14px 0; }

/* Grid fields */
.pf-grid-3 { display:grid;grid-template-columns:repeat(3,1fr);gap:14px 20px; }
.pf-grid-2 { display:grid;grid-template-columns:repeat(2,1fr);gap:14px 20px; }
.pf-grid-1 { display:grid;grid-template-columns:1fr;gap:14px; }

/* Field */
.pf-field label { font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;display:block;margin-bottom:3px; }
.pf-field .val  { font-size:13px;font-weight:600;color:#1e293b;line-height:1.4; }
.pf-field .val.empty { color:#cbd5e1;font-style:italic;font-weight:400; }

/* Avatar */
.pf-avatar {
    width:72px;height:72px;border-radius:50%;
    background:linear-gradient(135deg,#6366f1,#f59e0b);
    display:flex;align-items:center;justify-content:center;
    font-size:28px;font-weight:800;color:#fff;flex-shrink:0;
}

/* Badge */
.pf-badge {
    display:inline-flex;align-items:center;gap:4px;
    padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
}

/* Ortu card */
.pf-ortu-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:14px; }
.pf-ortu-box  { background:#f8fafc;border-radius:10px;padding:14px; }
.pf-ortu-title { font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;display:flex;align-items:center;gap:5px; }

/* Responsive */
@media (max-width: 900px) {
    .pf-grid-3 { grid-template-columns:repeat(2,1fr); }
    .pf-ortu-grid { grid-template-columns:repeat(2,1fr); }
}
@media (max-width: 640px) {
    .pf-grid-3,.pf-grid-2 { grid-template-columns:1fr; }
    .pf-ortu-grid { grid-template-columns:1fr; }
    .pf-avatar { width:56px;height:56px;font-size:22px; }
    .pf-hero-name { font-size:17px !important; }
}
</style>

@php
$roleLabel = [
    'admin'                => ['Admin Sistem',       '#6366f1','#eef2ff'],
    'kepala_sekolah'       => ['Kepala Sekolah',     '#0284c7','#e0f2fe'],
    'wakil_kepala_sekolah' => ['Wakil Kepala',       '#7c3aed','#f3e8ff'],
    'guru'                 => ['Guru / GTK',         '#10b981','#d1fae5'],
    'bk'                   => ['Bimbingan Konseling','#f59e0b','#fef3c7'],
    'tata_usaha'           => ['Tata Usaha',         '#64748b','#f1f5f9'],
    'siswa'                => ['Siswa',              '#6366f1','#eef2ff'],
][$role] ?? [$role,'#64748b','#f1f5f9'];
@endphp

{{-- Hero Card --}}
<div class="pf-card">
    <div class="pf-body">
        <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
            <div class="pf-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
            <div style="flex:1;min-width:0;">
                <h2 class="pf-hero-name" style="font-size:19px;font-weight:800;color:#0f172a;margin-bottom:6px;">
                    {{ $siswa?->nama ?? $guru?->nama ?? $user->name }}
                </h2>
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
                    <span class="pf-badge" style="color:{{ $roleLabel[1] }};background:{{ $roleLabel[2] }};">
                        <i class="bi bi-person-badge-fill"></i> {{ $roleLabel[0] }}
                    </span>
                    @if($siswa)
                    <span class="pf-badge" style="color:#0284c7;background:#e0f2fe;">
                        <i class="bi bi-diagram-3-fill"></i> {{ $siswa->nama_rombel ?? '-' }}
                    </span>
                    @endif
                    @if($guru?->jenis_ptk)
                    <span class="pf-badge" style="color:#64748b;background:#f1f5f9;">
                        <i class="bi bi-briefcase-fill"></i> {{ $guru->jenis_ptk }}
                    </span>
                    @endif
                </div>
                <div style="display:flex;gap:14px;flex-wrap:wrap;">
                    <span style="font-size:12px;color:#64748b;">
                        <i class="bi bi-envelope-fill" style="color:#6366f1;margin-right:4px;"></i>{{ $user->email }}
                    </span>
                    @if($siswa?->no_hp ?? $guru?->no_hp)
                    <span style="font-size:12px;color:#64748b;">
                        <i class="bi bi-telephone-fill" style="color:#10b981;margin-right:4px;"></i>{{ $siswa?->no_hp ?? $guru?->no_hp }}
                    </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('password.change') }}" class="btn btn-primary btn-sm" style="flex-shrink:0;align-self:flex-start;">
                <i class="bi bi-key-fill"></i> Ganti Password
            </a>
        </div>
    </div>
</div>

@if($siswa)
{{-- ===== SISWA ===== --}}

{{-- Data Pribadi --}}
<div class="pf-card">
    <div class="pf-head">
        <i class="bi bi-person-badge-fill"></i>
        <h3>Data Pribadi Siswa</h3>
    </div>
    <div class="pf-body">
        <div class="pf-grid-3">
            @foreach([
                ['NISN',         $siswa->nisn],
                ['NIPD / NIS',   $siswa->nipd],
                ['NIK',          $siswa->nik],
                ['Nama Lengkap', $siswa->nama],
                ['Jenis Kelamin',$siswa->jk_label],
                ['Agama',        $siswa->agama],
                ['Tempat Lahir', $siswa->tempat_lahir],
                ['Tanggal Lahir',$siswa->tanggal_lahir?->translatedFormat('d F Y')],
                ['No. HP',       $siswa->no_hp],
            ] as [$lbl,$val])
            <div class="pf-field">
                <label>{{ $lbl }}</label>
                <div class="val {{ !$val ? 'empty' : '' }}">{{ $val ?: 'Belum diisi' }}</div>
            </div>
            @endforeach
        </div>
        <hr class="pf-sep">
        <div class="pf-grid-3">
            @foreach([
                ['Kelas / Rombel',    $siswa->nama_rombel],
                ['Kurikulum',         $siswa->kurikulum],
                ['Sekolah Asal',      $siswa->sekolah_asal],
                ['Tanggal Masuk',     $siswa->tanggal_masuk_sekolah?->translatedFormat('d F Y')],
                ['Jenis Pendaftaran', $siswa->jenis_pendaftaran],
                ['Status',            $siswa->status],
            ] as [$lbl,$val])
            <div class="pf-field">
                <label>{{ $lbl }}</label>
                <div class="val {{ !$val ? 'empty' : '' }}">{{ $val ?: 'Belum diisi' }}</div>
            </div>
            @endforeach
        </div>
        @if($siswa->tinggi_badan || $siswa->berat_badan || $siswa->kebutuhan_khusus)
        <hr class="pf-sep">
        <div class="pf-grid-3">
            <div class="pf-field">
                <label>Tinggi Badan</label>
                <div class="val {{ !$siswa->tinggi_badan?'empty':'' }}">{{ $siswa->tinggi_badan ? $siswa->tinggi_badan.' cm' : 'Belum diisi' }}</div>
            </div>
            <div class="pf-field">
                <label>Berat Badan</label>
                <div class="val {{ !$siswa->berat_badan?'empty':'' }}">{{ $siswa->berat_badan ? $siswa->berat_badan.' kg' : 'Belum diisi' }}</div>
            </div>
            <div class="pf-field">
                <label>Kebutuhan Khusus</label>
                <div class="val {{ !$siswa->kebutuhan_khusus?'empty':'' }}">{{ $siswa->kebutuhan_khusus ?: 'Tidak ada' }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Orang Tua --}}
<div class="pf-card">
    <div class="pf-head">
        <i class="bi bi-people-fill"></i>
        <h3>Data Orang Tua / Wali</h3>
    </div>
    <div class="pf-body">
        <div class="pf-ortu-grid">
            <div class="pf-ortu-box">
                <div class="pf-ortu-title" style="color:#6366f1;">
                    <i class="bi bi-person-fill"></i> Ayah
                </div>
                <div class="pf-field" style="margin-bottom:10px;">
                    <label>Nama</label>
                    <div class="val {{ !$siswa->nama_ayah?'empty':'' }}">{{ $siswa->nama_ayah ?: 'Belum diisi' }}</div>
                </div>
                <div class="pf-field">
                    <label>Pekerjaan</label>
                    <div class="val {{ !$siswa->pekerjaan_ayah?'empty':'' }}">{{ $siswa->pekerjaan_ayah ?: 'Belum diisi' }}</div>
                </div>
            </div>
            <div class="pf-ortu-box">
                <div class="pf-ortu-title" style="color:#ec4899;">
                    <i class="bi bi-person-fill"></i> Ibu
                </div>
                <div class="pf-field" style="margin-bottom:10px;">
                    <label>Nama</label>
                    <div class="val {{ !$siswa->nama_ibu?'empty':'' }}">{{ $siswa->nama_ibu ?: 'Belum diisi' }}</div>
                </div>
                <div class="pf-field">
                    <label>Pekerjaan</label>
                    <div class="val {{ !$siswa->pekerjaan_ibu?'empty':'' }}">{{ $siswa->pekerjaan_ibu ?: 'Belum diisi' }}</div>
                </div>
            </div>
            <div class="pf-ortu-box">
                <div class="pf-ortu-title" style="color:#64748b;">
                    <i class="bi bi-person-fill"></i> Wali
                </div>
                <div class="pf-field" style="margin-bottom:10px;">
                    <label>Nama</label>
                    <div class="val {{ !$siswa->nama_wali?'empty':'' }}">{{ $siswa->nama_wali ?: 'Belum diisi' }}</div>
                </div>
                <div class="pf-field">
                    <label>Pekerjaan</label>
                    <div class="val {{ !$siswa->pekerjaan_wali?'empty':'' }}">{{ $siswa->pekerjaan_wali ?: 'Belum diisi' }}</div>
                </div>
            </div>
        </div>
        <hr class="pf-sep">
        <div class="pf-grid-2">
            <div class="pf-field">
                <label>No. HP Orang Tua</label>
                <div class="val {{ !$siswa->no_hp_ortu?'empty':'' }}">{{ $siswa->no_hp_ortu ?: 'Belum diisi' }}</div>
            </div>
            <div class="pf-field">
                <label>Anak Ke-</label>
                <div class="val {{ !$siswa->anak_keberapa?'empty':'' }}">{{ $siswa->anak_keberapa ?: 'Belum diisi' }}</div>
            </div>
        </div>
    </div>
</div>

@elseif($guru)
{{-- ===== GURU ===== --}}

<div class="pf-card">
    <div class="pf-head">
        <i class="bi bi-person-workspace"></i>
        <h3>Data Identitas GTK</h3>
    </div>
    <div class="pf-body">
        <div class="pf-grid-3">
            @foreach([
                ['NUPTK',       $guru->nuptk],
                ['NIP',         $guru->nip],
                ['NIK',         $guru->nik],
                ['Nama Lengkap',$guru->nama],
                ['Jenis Kelamin',$guru->jk_label],
                ['Agama',       $guru->agama],
                ['Tempat Lahir',$guru->tempat_lahir],
                ['Tanggal Lahir',$guru->tanggal_lahir?->translatedFormat('d F Y')],
                ['No. HP',      $guru->no_hp],
            ] as [$lbl,$val])
            <div class="pf-field">
                <label>{{ $lbl }}</label>
                <div class="val {{ !$val?'empty':'' }}">{{ $val ?: 'Belum diisi' }}</div>
            </div>
            @endforeach
        </div>
        <hr class="pf-sep">
        <div class="pf-grid-3">
            @foreach([
                ['Jenis PTK',          $guru->jenis_ptk],
                ['Jabatan',            $guru->jabatan],
                ['Status Kepegawaian', $guru->status_kepegawaian],
                ['Pangkat / Golongan', $guru->pangkat_golongan],
                ['Pendidikan Terakhir',$guru->pendidikan_terakhir],
                ['Bidang Studi',       $guru->bidang_studi],
            ] as [$lbl,$val])
            <div class="pf-field">
                <label>{{ $lbl }}</label>
                <div class="val {{ !$val?'empty':'' }}">{{ $val ?: 'Belum diisi' }}</div>
            </div>
            @endforeach
        </div>
        @if($guru->tanggal_surat_tugas || $guru->tahun_ajaran)
        <hr class="pf-sep">
        <div class="pf-grid-2">
            <div class="pf-field">
                <label>Tahun Ajaran</label>
                <div class="val {{ !$guru->tahun_ajaran?'empty':'' }}">{{ $guru->tahun_ajaran ?: 'Belum diisi' }}</div>
            </div>
            <div class="pf-field">
                <label>Tanggal Surat Tugas</label>
                <div class="val {{ !$guru->tanggal_surat_tugas?'empty':'' }}">{{ $guru->tanggal_surat_tugas?->translatedFormat('d F Y') ?: 'Belum diisi' }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

@else
{{-- ===== ADMIN / USER TANPA RELASI ===== --}}
<div class="pf-card">
    <div class="pf-head">
        <i class="bi bi-person-circle"></i>
        <h3>Informasi Akun</h3>
    </div>
    <div class="pf-body">
        <div class="pf-grid-3">
            <div class="pf-field"><label>Nama Akun</label><div class="val">{{ $user->name }}</div></div>
            <div class="pf-field"><label>Email</label><div class="val">{{ $user->email }}</div></div>
            <div class="pf-field"><label>Role</label><div class="val">{{ $role }}</div></div>
            <div class="pf-field"><label>Akun Dibuat</label><div class="val">{{ $user->created_at->translatedFormat('d F Y') }}</div></div>
            <div class="pf-field">
                <label>Email Terverifikasi</label>
                <div class="val">{{ $user->email_verified_at ? $user->email_verified_at->translatedFormat('d F Y') : 'Belum' }}</div>
            </div>
        </div>
        <div style="margin-top:14px;padding:12px 14px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;font-size:12px;color:#92400e;">
            <i class="bi bi-info-circle-fill me-1"></i>
            Data profil lengkap dikelola oleh Admin melalui menu Data GTK.
        </div>
    </div>
</div>
@endif

{{-- Info Akun Sistem --}}
<div class="pf-card">
    <div class="pf-head">
        <i class="bi bi-shield-lock-fill"></i>
        <h3>Informasi Akun Sistem</h3>
    </div>
    <div class="pf-body">
        <div class="pf-grid-3">
            <div class="pf-field">
                <label>Username / Email Login</label>
                <div class="val">{{ $user->email }}</div>
            </div>
            <div class="pf-field">
                <label>Akun Dibuat</label>
                <div class="val">{{ $user->created_at->translatedFormat('d F Y, H:i') }}</div>
            </div>
            <div class="pf-field">
                <label>Terakhir Diperbarui</label>
                <div class="val">{{ $user->updated_at->translatedFormat('d F Y, H:i') }}</div>
            </div>
        </div>
        <hr class="pf-sep">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <p style="font-size:13px;color:#64748b;margin:0;">
                <i class="bi bi-key-fill" style="color:#6366f1;margin-right:5px;"></i>
                Untuk mengubah password, gunakan tombol di samping.
            </p>
            <a href="{{ route('password.change') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-key-fill"></i> Ganti Password
            </a>
        </div>
    </div>
</div>

@endsection
