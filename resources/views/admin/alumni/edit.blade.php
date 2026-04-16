@extends('layouts.app')

@section('page-title', 'Edit Alumni')
@section('page-subtitle', $alumni->nama)

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>✏️ Edit Alumni</h1>
    <p>Update data alumni: <strong>{{ $alumni->nama }}</strong></p>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;">
    <p style="font-weight:700;color:#dc2626;margin-bottom:6px;">⚠️ Ada kesalahan input:</p>
    @foreach($errors->all() as $e)
        <p style="font-size:13px;color:#dc2626;">• {{ $e }}</p>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('admin.alumni.update', $alumni) }}">
@csrf
@method('PUT')
<div class="grid grid-2" style="align-items:start;gap:20px;">

    {{-- Kiri: Identitas --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card">
            <div class="card-header"><h3>🪪 Identitas Alumni</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA LENGKAP <span style="color:red">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $alumni->nama) }}" required
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">JENIS KELAMIN <span style="color:red">*</span></label>
                            <select name="jenis_kelamin" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                                <option value="L" {{ old('jenis_kelamin', $alumni->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $alumni->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">AGAMA</label>
                            <select name="agama" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                                <option value="">Pilih...</option>
                                @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag)
                                    <option value="{{ $ag }}" {{ old('agama', $alumni->agama) == $ag ? 'selected' : '' }}>{{ $ag }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NISN</label>
                            <input type="text" name="nisn" value="{{ old('nisn', $alumni->nisn) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NIPD</label>
                            <input type="text" name="nipd" value="{{ old('nipd', $alumni->nipd) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TEMPAT LAHIR</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $alumni->tempat_lahir) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TANGGAL LAHIR</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $alumni->tanggal_lahir?->format('Y-m-d')) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA AYAH</label>
                            <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $alumni->nama_ayah) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA IBU</label>
                            <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $alumni->nama_ibu) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA WALI</label>
                            <input type="text" name="nama_wali" value="{{ old('nama_wali', $alumni->nama_wali) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NO. HP ORTU</label>
                            <input type="text" name="no_hp_ortu" value="{{ old('no_hp_ortu', $alumni->no_hp_ortu) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Kanan: Kelulusan --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card">
            <div class="card-header"><h3>🎓 Data Kelulusan</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TAHUN LULUS <span style="color:red">*</span></label>
                            <input type="text" name="tahun_lulus" value="{{ old('tahun_lulus', $alumni->tahun_lulus) }}" required
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TANGGAL LULUS</label>
                            <input type="date" name="tanggal_lulus" value="{{ old('tanggal_lulus', $alumni->tanggal_lulus?->format('Y-m-d')) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">SEMESTER</label>
                        <select name="semester_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">Pilih semester...</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ old('semester_id', $alumni->semester_id) == $sem->id ? 'selected' : '' }}>{{ $sem->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NO. IJAZAH</label>
                            <input type="text" name="no_ijazah" value="{{ old('no_ijazah', $alumni->no_ijazah) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NO. SKHUN</label>
                            <input type="text" name="no_skhun" value="{{ old('no_skhun', $alumni->no_skhun) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NILAI RATA-RATA</label>
                            <input type="number" step="0.01" name="nilai_rata" value="{{ old('nilai_rata', $alumni->nilai_rata) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KELAS TERAKHIR</label>
                            <input type="text" name="nama_rombel" value="{{ old('nama_rombel', $alumni->nama_rombel) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KETERANGAN</label>
                        <input type="text" name="keterangan" value="{{ old('keterangan', $alumni->keterangan) }}"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NO. HP</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $alumni->no_hp) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">EMAIL</label>
                            <input type="email" name="email" value="{{ old('email', $alumni->email) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('admin.alumni.show', $alumni) }}" class="btn" style="background:#f1f5f9;color:#374151;">
                <i class="bi bi-x-lg"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Update Alumni
            </button>
        </div>
    </div>

</div>
</form>
@endsection
