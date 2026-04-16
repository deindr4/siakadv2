@extends('layouts.app')

@section('page-title', 'Edit Data Guru & GTK')
@section('page-subtitle', 'Ubah data guru dan tenaga kependidikan')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>✏️ Edit Data GTK</h1>
    <p>{{ $guru->nama }}</p>
</div>

{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#94a3b8;margin-bottom:20px;">
    <a href="{{ route('admin.guru.index') }}" style="color:#6366f1;text-decoration:none;">Data Guru & GTK</a>
    <i class="bi bi-chevron-right" style="font-size:11px;"></i>
    <span>Edit — {{ $guru->nama }}</span>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
    <i class="bi bi-exclamation-circle-fill me-2"></i>
    <ul style="margin:6px 0 0 16px;padding:0;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.guru.update', $guru) }}">
    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

        {{-- Identitas --}}
        <div class="card" style="grid-column:1/-1;">
            <div class="card-header">
                <h3><i class="bi bi-person-badge-fill" style="color:#6366f1;margin-right:8px;"></i>Identitas</h3>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">

                    <div style="grid-column:1/3;">
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">NAMA LENGKAP <span style="color:#ef4444">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $guru->nama) }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid {{ $errors->has('nama') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13px;outline:none;font-family:inherit;"
                            required>
                        @error('nama')<p style="color:#ef4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">JENIS KELAMIN <span style="color:#ef4444">*</span></label>
                        <select name="jenis_kelamin" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<p style="color:#ef4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">NIP</label>
                        <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        @error('nip')<p style="color:#ef4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">NUPTK</label>
                        <input type="text" name="nuptk" value="{{ old('nuptk', $guru->nuptk) }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        @error('nuptk')<p style="color:#ef4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik', $guru->nik) }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">TEMPAT LAHIR</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $guru->tempat_lahir) }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">TANGGAL LAHIR</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $guru->tanggal_lahir) }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">AGAMA</label>
                        <select name="agama" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">-- Pilih --</option>
                            @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag)
                            <option value="{{ $ag }}" {{ old('agama', $guru->agama) === $ag ? 'selected' : '' }}>{{ $ag }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- Kepegawaian --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-briefcase-fill" style="color:#6366f1;margin-right:8px;"></i>Kepegawaian</h3>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">JENIS PTK</label>
                        <select name="jenis_ptk" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">-- Pilih --</option>
                            <option value="Guru" {{ old('jenis_ptk', $guru->jenis_ptk) === 'Guru' ? 'selected' : '' }}>👨‍🏫 Guru</option>
                            <option value="Tendik" {{ old('jenis_ptk', $guru->jenis_ptk) === 'Tendik' ? 'selected' : '' }}>🗂️ Tenaga Kependidikan</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">JABATAN</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $guru->jabatan) }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">STATUS KEPEGAWAIAN</label>
                        <select name="status_kepegawaian" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">-- Pilih --</option>
                            @foreach(['PNS','PPPK','GTT','PTT','Honor','Kontrak'] as $sk)
                            <option value="{{ $sk }}" {{ old('status_kepegawaian', $guru->status_kepegawaian) === $sk ? 'selected' : '' }}>{{ $sk }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">PENDIDIKAN TERAKHIR</label>
                        <select name="pendidikan_terakhir" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">-- Pilih --</option>
                            @foreach(['SMA/SMK','D2','D3','D4','S1','S2','S3'] as $pd)
                            <option value="{{ $pd }}" {{ old('pendidikan_terakhir', $guru->pendidikan_terakhir) === $pd ? 'selected' : '' }}>{{ $pd }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">BIDANG STUDI</label>
                        <input type="text" name="bidang_studi" value="{{ old('bidang_studi', $guru->bidang_studi) }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                </div>
            </div>
        </div>

        {{-- Kontak & Status --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-telephone-fill" style="color:#6366f1;margin-right:8px;"></i>Kontak & Status</h3>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">NO. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">EMAIL</label>
                        <input type="email" name="email" value="{{ old('email', $guru->email) }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">STATUS</label>
                        <select name="status" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="aktif"    {{ old('status', $guru->status) === 'aktif'    ? 'selected' : '' }}>✅ Aktif</option>
                            <option value="nonaktif" {{ old('status', $guru->status) === 'nonaktif' ? 'selected' : '' }}>❌ Nonaktif</option>
                        </select>
                    </div>

                    {{-- Info sumber data --}}
                    <div style="background:#f8fafc;border-radius:8px;padding:12px;font-size:12px;color:#64748b;">
                        <div style="margin-bottom:4px;"><span style="font-weight:600;">Sumber Data:</span>
                            @if($guru->sumber_data === 'dapodik')
                                <span class="badge badge-success">Dapodik</span>
                            @elseif($guru->sumber_data === 'excel')
                                <span class="badge badge-warning">Excel</span>
                            @else
                                <span class="badge badge-primary">Manual</span>
                            @endif
                        </div>
                        <div><span style="font-weight:600;">Terakhir diupdate:</span> {{ $guru->updated_at?->translatedFormat('d F Y, H:i') ?? '-' }}</div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- Action Buttons --}}
    <div style="display:flex;gap:12px;margin-top:20px;justify-content:flex-end;">
        <a href="{{ route('admin.guru.index') }}"
            style="padding:10px 24px;background:#f1f5f9;color:#374151;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
            <i class="bi bi-arrow-left me-2"></i>Batal
        </a>
        <button type="submit" class="btn btn-primary" style="padding:10px 28px;font-size:13px;">
            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
        </button>
    </div>

</form>
@endsection
