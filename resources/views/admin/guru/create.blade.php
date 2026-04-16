@extends('layouts.app')

@section('page-title', 'Tambah Guru')
@section('page-subtitle', 'Input data guru secara manual')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>➕ Tambah Guru Manual</h1>
    <p>Isi data guru yang tidak tersedia di Dapodik</p>
</div>

<form action="{{ route('admin.guru.store') }}" method="POST">
@csrf
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

    {{-- Identitas --}}
    <div class="card">
        <div class="card-header"><h3>📋 Identitas</h3></div>
        <div class="card-body">

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Nama Lengkap <span style="color:red;">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap guru"
                    style="width:100%;padding:10px 14px;border:1.5px solid {{ $errors->has('nama') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                @error('nama')<p style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Jenis Kelamin <span style="color:red;">*</span></label>
                <select name="jenis_kelamin" style="width:100%;padding:10px 14px;border:1.5px solid {{ $errors->has('jenis_kelamin') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">-- Pilih --</option>
                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')<p style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" placeholder="NIP"
                        style="width:100%;padding:10px 14px;border:1.5px solid {{ $errors->has('nip') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    @error('nip')<p style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">NUPTK</label>
                    <input type="text" name="nuptk" value="{{ old('nuptk') }}" placeholder="NUPTK"
                        style="width:100%;padding:10px 14px;border:1.5px solid {{ $errors->has('nuptk') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    @error('nuptk')<p style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">NIK</label>
                <input type="text" name="nik" value="{{ old('nik') }}" placeholder="Nomor Induk Kependudukan"
                    style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Agama</label>
                <select name="agama" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">-- Pilih --</option>
                    @foreach(['Islam','Kristen','Katholik','Hindu','Buddha','Konghucu'] as $agama)
                        <option value="{{ $agama }}" {{ old('agama') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>

    {{-- Kepegawaian & Kontak --}}
    <div style="display:flex;flex-direction:column;gap:20px;">
        <div class="card">
            <div class="card-header"><h3>🏢 Kepegawaian</h3></div>
            <div class="card-body">

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Jenis PTK</label>
                    <select name="jenis_ptk" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">-- Pilih --</option>
                        @foreach(['Guru','Tenaga Kependidikan','Kepala Sekolah'] as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_ptk') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Jabatan</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: Guru Matematika"
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status Kepegawaian</label>
                    <select name="status_kepegawaian" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">-- Pilih --</option>
                        @foreach(['PNS','PPPK','GTT','PTT','Honor'] as $sk)
                            <option value="{{ $sk }}" {{ old('status_kepegawaian') == $sk ? 'selected' : '' }}>{{ $sk }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">-- Pilih --</option>
                            @foreach(['SMA/sederajat','D3','S1','S2','S3'] as $pend)
                                <option value="{{ $pend }}" {{ old('pendidikan_terakhir') == $pend ? 'selected' : '' }}>{{ $pend }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Bidang Studi</label>
                        <input type="text" name="bidang_studi" value="{{ old('bidang_studi') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status</label>
                    <select name="status" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ old('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="pensiun" {{ old('status') == 'pensiun' ? 'selected' : '' }}>Pensiun</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>📱 Kontak</h3></div>
            <div class="card-body">
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
            </div>
        </div>
    </div>

</div>

<div style="margin-top:20px;display:flex;gap:10px;">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> Simpan Data Guru
    </button>
    <a href="{{ route('admin.guru.index') }}" class="btn" style="background:#f1f5f9;color:#374151;">
        Batal
    </a>
</div>

</form>
@endsection
