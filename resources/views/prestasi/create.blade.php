@extends('layouts.app')
@section('page-title', isset($prestasi) ? 'Edit Prestasi' : 'Tambah Prestasi')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
@php
    $isEdit  = isset($prestasi);
    $action  = $isEdit ? route('prestasi.update', $prestasi) : route('prestasi.store');
    $isSiswa = auth()->user()->hasRole('siswa');
@endphp

<style>
.form-label  { font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px; }
.form-input  { width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;box-sizing:border-box; }
.form-input:focus  { border-color:#6366f1; }
.form-select { width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;box-sizing:border-box; }
.form-select:focus { border-color:#6366f1; }

/* Layout */
.prestasi-grid { display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start; }
.field-row-2   { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
.field-row-3   { display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px; }

/* Tipe radio */
.tipe-label {
    display:flex;align-items:center;gap:8px;cursor:pointer;
    padding:10px 16px;border:2px solid #e2e8f0;border-radius:10px;
    flex:1;transition:border-color .15s,background .15s;
    font-weight:700;font-size:13px;
}
.tipe-label:has(input:checked) { border-color:#6366f1;background:#eef2ff; }

/* Siswa row */
.siswa-row {
    display:flex;align-items:center;gap:10px;
    padding:10px 14px;background:#f8fafc;
    border-radius:8px;margin-bottom:8px;
    border:1px solid #e2e8f0;
}

/* Drop zone */
.drop-zone {
    border:2px dashed #c7d2fe;border-radius:10px;
    padding:24px;text-align:center;cursor:pointer;
    transition:border-color .15s,background .15s;
}
.drop-zone:hover { border-color:#6366f1;background:#f5f3ff; }

/* Responsive */
@media (max-width: 900px) {
    .prestasi-grid { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .field-row-2 { grid-template-columns: 1fr; }
    .field-row-3 { grid-template-columns: 1fr; }
    .tipe-wrap   { flex-direction: column; }
}
</style>

<div class="page-title" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div>
        <h1>
            <i class="bi bi-{{ $isEdit ? 'pencil-square' : 'trophy-fill' }} me-2" style="color:#6366f1;"></i>
            {{ $isEdit ? 'Edit Prestasi' : 'Tambah Prestasi' }}
        </h1>
        <p>{{ $isEdit ? 'Perbarui data prestasi' : 'Input data prestasi baru' }}</p>
    </div>
    <a href="{{ route('prestasi.index') }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;align-self:center;">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#dc2626;font-size:13px;">
    <strong>Ada kesalahan:</strong>
    <ul style="margin:6px 0 0 16px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="prestasiForm">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="prestasi-grid">

        {{-- KIRI --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Info Lomba --}}
            <div class="card">
                <div class="card-header">
                    <h3><i class="bi bi-journals me-2" style="color:#6366f1;"></i>Informasi Lomba / Kegiatan</h3>
                </div>
                <div class="card-body">
                    <div style="display:flex;flex-direction:column;gap:14px;">

                        <div class="field-row-2">
                            <div>
                                <label class="form-label">SEMESTER <span style="color:red">*</span></label>
                                <select name="semester_id" required class="form-select">
                                    @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ old('semester_id', $isEdit ? $prestasi->semester_id : $semesterAktif?->id) == $sem->id ? 'selected' : '' }}>
                                        {{ $sem->nama }}{{ $sem->is_aktif ? ' (Aktif)' : '' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">KATEGORI</label>
                                <select name="kategori_id" class="form-select">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategoris->groupBy('jenis') as $jenis => $items)
                                    <optgroup label="{{ $jenis === 'akademik' ? 'Akademik' : 'Non-Akademik' }}">
                                        @foreach($items as $k)
                                        <option value="{{ $k->id }}" {{ old('kategori_id', $isEdit ? $prestasi->kategori_id : '') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama }}
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">NAMA LOMBA / KEGIATAN <span style="color:red">*</span></label>
                            <input type="text" name="nama_lomba" required class="form-input"
                                value="{{ old('nama_lomba', $isEdit ? $prestasi->nama_lomba : '') }}"
                                placeholder="cth: Olimpiade Matematika Tingkat Provinsi">
                        </div>

                        <div>
                            <label class="form-label">PENYELENGGARA</label>
                            <input type="text" name="penyelenggara" class="form-input"
                                value="{{ old('penyelenggara', $isEdit ? $prestasi->penyelenggara : '') }}"
                                placeholder="cth: Dinas Pendidikan Provinsi Bali">
                        </div>

                        <div class="field-row-3">
                            <div>
                                <label class="form-label">TINGKAT <span style="color:red">*</span></label>
                                <select name="tingkat" required class="form-select">
                                    @foreach(['sekolah'=>'Sekolah','kecamatan'=>'Kecamatan','kabupaten'=>'Kabupaten/Kota','provinsi'=>'Provinsi','nasional'=>'Nasional','internasional'=>'Internasional'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('tingkat', $isEdit ? $prestasi->tingkat : '') == $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">TANGGAL <span style="color:red">*</span></label>
                                <input type="date" name="tanggal" required class="form-input"
                                    value="{{ old('tanggal', $isEdit ? $prestasi->tanggal->format('Y-m-d') : '') }}">
                            </div>
                            <div>
                                <label class="form-label">TEMPAT</label>
                                <input type="text" name="tempat" class="form-input"
                                    value="{{ old('tempat', $isEdit ? $prestasi->tempat : '') }}"
                                    placeholder="cth: Denpasar">
                            </div>
                        </div>

                        <div class="field-row-2">
                            <div>
                                <label class="form-label">JUARA / PERINGKAT <span style="color:red">*</span></label>
                                <input type="text" name="juara" required class="form-input"
                                    value="{{ old('juara', $isEdit ? $prestasi->juara : '') }}"
                                    placeholder="cth: Juara 1, Medali Emas">
                            </div>
                            <div>
                                <label class="form-label">URUT JUARA <span style="font-size:10px;color:#94a3b8;font-weight:400;">(untuk sorting)</span></label>
                                <input type="number" name="juara_urut" class="form-input" min="1"
                                    value="{{ old('juara_urut', $isEdit ? $prestasi->juara_urut : 1) }}">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">KETERANGAN</label>
                            <textarea name="keterangan" rows="2" class="form-input"
                                placeholder="Deskripsi tambahan...">{{ old('keterangan', $isEdit ? $prestasi->keterangan : '') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Siswa & Tipe --}}
            <div class="card">
                <div class="card-header">
                    <h3><i class="bi bi-people-fill me-2" style="color:#6366f1;"></i>Siswa &amp; Tipe Prestasi</h3>
                </div>
                <div class="card-body">

                    {{-- Tipe --}}
                    <div class="tipe-wrap" style="display:flex;gap:12px;margin-bottom:16px;">
                        <label class="tipe-label">
                            <input type="radio" name="tipe" value="individu" onchange="updateTipe()"
                                {{ old('tipe', $isEdit ? $prestasi->tipe : 'individu') === 'individu' ? 'checked' : '' }}>
                            <i class="bi bi-person-fill" style="color:#6366f1;"></i> Individu
                        </label>
                        <label class="tipe-label">
                            <input type="radio" name="tipe" value="tim" onchange="updateTipe()"
                                {{ old('tipe', $isEdit ? $prestasi->tipe : '') === 'tim' ? 'checked' : '' }}>
                            <i class="bi bi-people-fill" style="color:#6366f1;"></i> Tim / Kelompok
                        </label>
                    </div>

                    {{-- Nama Tim --}}
                    <div id="wrap-nama-tim" style="margin-bottom:14px;{{ old('tipe', $isEdit ? $prestasi->tipe : 'individu') !== 'tim' ? 'display:none;' : '' }}">
                        <label class="form-label">NAMA TIM</label>
                        <input type="text" name="nama_tim" class="form-input"
                            value="{{ old('nama_tim', $isEdit ? $prestasi->nama_tim : '') }}"
                            placeholder="cth: Tim Robotik SMA N 1 Kuta Selatan">
                    </div>

                    {{-- Pilih Siswa (non-siswa role) --}}
                    @if(!$isSiswa)
                    <div class="field-row-2" style="margin-bottom:14px;">
                        <div>
                            <label class="form-label">PILIH KELAS</label>
                            <select id="select-rombel" class="form-select" onchange="loadSiswa()">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($rombels as $r)
                                <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display:flex;align-items:flex-end;">
                            <button type="button" onclick="tambahSiswa()" class="btn btn-primary" style="width:100%;">
                                <i class="bi bi-plus-circle-fill"></i> Tambah
                            </button>
                        </div>
                    </div>

                    <div id="wrap-dropdown-siswa" style="margin-bottom:14px;display:none;">
                        <label class="form-label">SISWA</label>
                        <select id="select-siswa" class="form-select">
                            <option value="">-- Pilih Siswa --</option>
                        </select>
                    </div>
                    @endif

                    {{-- Daftar Siswa --}}
                    <div id="daftar-siswa">
                        @if($isSiswa && isset($siswaSelf))
                        <div class="siswa-row" data-id="{{ $siswaSelf->id }}">
                            <input type="hidden" name="siswa_ids[]" value="{{ $siswaSelf->id }}">
                            <i class="bi bi-person-circle" style="color:#6366f1;font-size:18px;"></i>
                            <div style="flex:1;font-weight:600;">{{ $siswaSelf->nama }}</div>
                        </div>
                        @elseif($isEdit)
                            @foreach($prestasi->prestasiSiswa as $ps)
                            <div class="siswa-row" data-id="{{ $ps->siswa_id }}">
                                <input type="hidden" name="siswa_ids[]" value="{{ $ps->siswa_id }}">
                                <i class="bi bi-person-circle" style="color:#6366f1;font-size:18px;"></i>
                                <div style="flex:1;">
                                    <div style="font-weight:600;font-size:13px;">{{ $ps->siswa?->nama }}</div>
                                    <div style="font-size:11px;color:#94a3b8;">{{ $ps->siswa?->nama_rombel }}</div>
                                </div>
                                <div class="wrap-peran" style="{{ $prestasi->tipe !== 'tim' ? 'display:none;' : '' }}">
                                    <input type="text" name="peran[]" value="{{ $ps->peran }}" placeholder="Peran"
                                        style="padding:5px 10px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:12px;width:100px;outline:none;">
                                </div>
                                <button type="button" onclick="hapusSiswa(this)"
                                    style="background:#fee2e2;color:#dc2626;border:none;border-radius:6px;padding:5px 8px;cursor:pointer;flex-shrink:0;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            @endforeach
                        @else
                        <div id="empty-siswa" style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;">
                            <i class="bi bi-person-plus" style="font-size:28px;display:block;margin-bottom:6px;"></i>
                            Belum ada siswa ditambahkan
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- KANAN --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Submit --}}
            <div class="card">
                <div class="card-body">
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:14px;">
                            <i class="bi bi-{{ $isEdit ? 'check-circle' : 'plus-circle' }}-fill me-1"></i>
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Prestasi' }}
                        </button>
                        <a href="{{ route('prestasi.index') }}" class="btn"
                            style="width:100%;text-align:center;background:#f1f5f9;color:#374151;padding:12px;">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                    </div>
                    @if(!auth()->user()->hasAnyRole(['admin','bk','tata_usaha']))
                    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;margin-top:12px;font-size:12px;color:#92400e;">
                        <i class="bi bi-clock-history me-1"></i>
                        Prestasi akan menunggu verifikasi Admin Kesiswaan
                    </div>
                    @endif
                </div>
            </div>

            {{-- Upload Sertifikat --}}
            <div class="card">
                <div class="card-header">
                    <h3><i class="bi bi-paperclip me-2" style="color:#6366f1;"></i>Bukti / Sertifikat</h3>
                </div>
                <div class="card-body">

                    @if($isEdit && $prestasi->hasSertifikat())
                    <div style="margin-bottom:14px;padding:12px;background:#f0f9ff;border-radius:8px;border:1px solid #bae6fd;">
                        <p style="font-size:11px;font-weight:700;color:#0284c7;margin-bottom:6px;">File Saat Ini:</p>
                        <a href="{{ $prestasi->sertifikatUrl() }}" target="_blank"
                            style="color:#0284c7;font-size:12px;word-break:break-all;display:flex;align-items:center;gap:6px;">
                            <i class="bi bi-file-earmark-check-fill"></i>
                            {{ $prestasi->file_sertifikat_original }}
                        </a>
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;color:#dc2626;margin-top:8px;">
                            <input type="checkbox" name="hapus_sertifikat" value="1"> Hapus file ini
                        </label>
                    </div>
                    @endif

                    <div class="drop-zone" id="drop-zone"
                        onclick="document.getElementById('input-sertifikat').click()"
                        ondragover="event.preventDefault();this.style.borderColor='#6366f1'"
                        ondragleave="this.style.borderColor='#c7d2fe'"
                        ondrop="handleDrop(event)">
                        <i class="bi bi-cloud-upload-fill" style="font-size:32px;color:#c7d2fe;display:block;margin-bottom:8px;"></i>
                        <p style="font-size:13px;font-weight:600;color:#6366f1;margin-bottom:4px;">Klik atau drag file</p>
                        <p style="font-size:11px;color:#94a3b8;">JPG, PNG, PDF — Maks 5MB</p>
                    </div>
                    <input type="file" id="input-sertifikat" name="file_sertifikat"
                        accept=".jpg,.jpeg,.png,.pdf" style="display:none;" onchange="previewFile(this)">

                    <div id="preview-sertifikat" style="display:none;margin-top:12px;">
                        <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;">
                            <i class="bi bi-file-earmark-check-fill" style="color:#16a34a;font-size:20px;flex-shrink:0;"></i>
                            <div style="flex:1;min-width:0;">
                                <div id="preview-nama" style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></div>
                                <div id="preview-size" style="font-size:11px;color:#94a3b8;"></div>
                            </div>
                            <button type="button" onclick="hapusPreview()"
                                style="background:none;border:none;cursor:pointer;color:#dc2626;flex-shrink:0;">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div id="preview-img-wrap" style="margin-top:8px;display:none;">
                            <img id="preview-img" style="width:100%;border-radius:8px;max-height:200px;object-fit:cover;">
                        </div>
                    </div>

                    <p style="font-size:11px;color:#94a3b8;margin-top:10px;">
                        <i class="bi bi-info-circle me-1"></i>
                        Gambar dikompresi otomatis ke maks 1200px untuk menghemat penyimpanan.
                    </p>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
// Tipe Individu/Tim
function updateTipe() {
    var tipe = document.querySelector('input[name="tipe"]:checked');
    if (!tipe) return;
    var isT = tipe.value === 'tim';
    document.getElementById('wrap-nama-tim').style.display = isT ? 'block' : 'none';
    document.querySelectorAll('.wrap-peran').forEach(function(el) {
        el.style.display = isT ? 'block' : 'none';
    });
}
updateTipe();

// AJAX Load Siswa
function loadSiswa() {
    var rombelId = document.getElementById('select-rombel').value;
    var wrap = document.getElementById('wrap-dropdown-siswa');
    var sel  = document.getElementById('select-siswa');
    if (!rombelId) { wrap.style.display = 'none'; return; }
    sel.innerHTML = '<option>Memuat...</option>';
    wrap.style.display = 'block';
    fetch('/prestasi/siswa-by-rombel?rombel_id=' + rombelId)
        .then(function(r){ return r.json(); })
        .then(function(data){
            sel.innerHTML = '<option value="">-- Pilih Siswa --</option>';
            data.forEach(function(s){
                var opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.nama + ' (' + (s.nisn || '-') + ')';
                sel.appendChild(opt);
            });
        });
}

function tambahSiswa() {
    var sel  = document.getElementById('select-siswa');
    var id   = sel.value;
    var nama = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : '';
    if (!id) { alert('Pilih siswa terlebih dahulu'); return; }
    if (document.querySelector('.siswa-row[data-id="' + id + '"]')) {
        alert('Siswa sudah ada dalam daftar'); return;
    }
    var tipe  = document.querySelector('input[name="tipe"]:checked');
    var isT   = tipe && tipe.value === 'tim';
    var empty = document.getElementById('empty-siswa');
    if (empty) empty.remove();
    var rombel = document.getElementById('select-rombel');
    var rombelNama = rombel.options[rombel.selectedIndex] ? rombel.options[rombel.selectedIndex].text : '';

    var div = document.createElement('div');
    div.className = 'siswa-row';
    div.dataset.id = id;
    div.innerHTML =
        '<input type="hidden" name="siswa_ids[]" value="' + id + '">' +
        '<i class="bi bi-person-circle" style="color:#6366f1;font-size:18px;flex-shrink:0;"></i>' +
        '<div style="flex:1;min-width:0;">' +
            '<div style="font-weight:600;font-size:13px;">' + nama.split(' (')[0] + '</div>' +
            '<div style="font-size:11px;color:#94a3b8;">' + rombelNama + '</div>' +
        '</div>' +
        '<div class="wrap-peran" style="' + (isT ? '' : 'display:none;') + '">' +
            '<input type="text" name="peran[]" placeholder="Peran" ' +
            'style="padding:5px 10px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:12px;width:100px;outline:none;">' +
        '</div>' +
        '<button type="button" onclick="hapusSiswa(this)" ' +
        'style="background:#fee2e2;color:#dc2626;border:none;border-radius:6px;padding:5px 8px;cursor:pointer;flex-shrink:0;">' +
            '<i class="bi bi-x-lg"></i>' +
        '</button>';
    document.getElementById('daftar-siswa').appendChild(div);
    sel.value = '';
}

function hapusSiswa(btn) {
    btn.closest('.siswa-row').remove();
    if (!document.querySelector('.siswa-row')) {
        document.getElementById('daftar-siswa').innerHTML =
            '<div id="empty-siswa" style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;">' +
            '<i class="bi bi-person-plus" style="font-size:28px;display:block;margin-bottom:6px;"></i>' +
            'Belum ada siswa ditambahkan</div>';
    }
}

// File Preview
function previewFile(input) {
    var file = input.files[0];
    if (!file) return;
    document.getElementById('preview-nama').textContent = file.name;
    document.getElementById('preview-size').textContent = (file.size / 1024).toFixed(0) + ' KB';
    document.getElementById('preview-sertifikat').style.display = 'block';
    document.getElementById('drop-zone').style.display = 'none';
    if (file.type.startsWith('image/')) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-img-wrap').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function hapusPreview() {
    document.getElementById('input-sertifikat').value = '';
    document.getElementById('preview-sertifikat').style.display = 'none';
    document.getElementById('drop-zone').style.display = 'block';
    document.getElementById('preview-img-wrap').style.display = 'none';
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.style.borderColor = '#c7d2fe';
    var file = e.dataTransfer.files[0];
    if (file) {
        var input = document.getElementById('input-sertifikat');
        var dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        previewFile(input);
    }
}
</script>
@endsection
