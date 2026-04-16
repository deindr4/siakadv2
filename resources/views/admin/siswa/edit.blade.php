@extends('layouts.app')

@section('page-title', 'Edit Siswa')
@section('page-subtitle', $siswa->nama)

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>✏️ Edit Siswa</h1>
    <p>Update data siswa: <strong>{{ $siswa->nama }}</strong></p>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;">
    <p style="font-weight:700;color:#dc2626;margin-bottom:6px;">⚠️ Ada kesalahan input:</p>
    @foreach($errors->all() as $e)
        <p style="font-size:13px;color:#dc2626;">• {{ $e }}</p>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('admin.siswa.update', $siswa) }}">
@csrf
@method('PUT')
<div class="grid grid-2" style="align-items:start;gap:20px;">

    {{-- Kolom Kiri --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        <div class="card">
            <div class="card-header"><h3>🪪 Identitas Siswa</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA LENGKAP <span style="color:red">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $siswa->nama) }}" required
                            style="width:100%;padding:9px 14px;border:1.5px solid {{ $errors->has('nama') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">JENIS KELAMIN <span style="color:red">*</span></label>
                            <select name="jenis_kelamin" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                                <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">AGAMA</label>
                            <select name="agama" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                                <option value="">Pilih...</option>
                                @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag)
                                    <option value="{{ $ag }}" {{ old('agama', $siswa->agama) == $ag ? 'selected' : '' }}>{{ $ag }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NISN</label>
                            <input type="text" name="nisn" value="{{ old('nisn', $siswa->nisn) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid {{ $errors->has('nisn') ? '#ef4444' : '#e2e8f0' }};border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NIPD</label>
                            <input type="text" name="nipd" value="{{ old('nipd', $siswa->nipd) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik', $siswa->nik) }}"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TEMPAT LAHIR</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TANGGAL LAHIR</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir?->format('Y-m-d')) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TINGGI BADAN (cm)</label>
                            <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $siswa->tinggi_badan) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">BERAT BADAN (kg)</label>
                            <input type="number" name="berat_badan" value="{{ old('berat_badan', $siswa->berat_badan) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Orang Tua --}}
        <div class="card">
            <div class="card-header"><h3>👨‍👩‍👧 Orang Tua / Wali</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA AYAH</label>
                            <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $siswa->nama_ayah) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">PEKERJAAN AYAH</label>
                            <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $siswa->pekerjaan_ayah) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA IBU</label>
                            <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $siswa->nama_ibu) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">PEKERJAAN IBU</label>
                            <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $siswa->pekerjaan_ibu) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA WALI</label>
                            <input type="text" name="nama_wali" value="{{ old('nama_wali', $siswa->nama_wali) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NO. HP ORTU</label>
                            <input type="text" name="no_hp_ortu" value="{{ old('no_hp_ortu', $siswa->no_hp_ortu) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Kolom Kanan --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        <div class="card">
            <div class="card-header"><h3>🏫 Info Akademik</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">SEMESTER <span style="color:red">*</span></label>
                        <select name="semester_id" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ old('semester_id', $siswa->semester_id) == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->nama }} {{ $sem->is_aktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">ROMBEL / KELAS</label>
                        <select name="rombongan_belajar_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">Pilih rombel...</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->rombongan_belajar_id }}"
                                    data-nama="{{ $r->nama_rombel }}"
                                    data-tingkat="{{ $r->tingkat }}"
                                    {{ old('rombongan_belajar_id', $siswa->rombongan_belajar_id) == $r->rombongan_belajar_id ? 'selected' : '' }}>
                                    {{ $r->nama_rombel }} (Kelas {{ $r->tingkat }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">SEKOLAH ASAL</label>
                            <input type="text" name="sekolah_asal" value="{{ old('sekolah_asal', $siswa->sekolah_asal) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TGL MASUK SEKOLAH</label>
                            <input type="date" name="tanggal_masuk_sekolah" value="{{ old('tanggal_masuk_sekolah', $siswa->tanggal_masuk_sekolah?->format('Y-m-d')) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">ANAK KE-</label>
                        <input type="number" name="anak_keberapa" value="{{ old('anak_keberapa', $siswa->anak_keberapa) }}" min="1"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                </div>
            </div>
        </div>

        {{-- Kontak --}}
        <div class="card">
            <div class="card-header"><h3>📱 Kontak</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NO. HP SISWA</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $siswa->no_hp) }}"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">EMAIL</label>
                        <input type="email" name="email" value="{{ old('email', $siswa->email) }}"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol --}}
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('admin.siswa.show', $siswa) }}" class="btn" style="background:#f1f5f9;color:#374151;">
                <i class="bi bi-x-lg"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Update Siswa
            </button>
        </div>

    </div>
</div>
</form>
@endsection
