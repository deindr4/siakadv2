@extends('layouts.app')

@section('page-title', 'Pengaturan Sekolah')
@section('page-subtitle', 'Data identitas sekolah untuk kop surat laporan')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>⚙️ Pengaturan Sekolah</h1>
    <p>Data identitas sekolah yang akan tampil di kop surat laporan</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">
    ✅ {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;">
    <p style="font-weight:700;color:#dc2626;margin-bottom:6px;">⚠️ Ada kesalahan:</p>
    @foreach($errors->all() as $e)
        <p style="font-size:13px;color:#dc2626;">• {{ $e }}</p>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="grid grid-2" style="align-items:start;gap:20px;">

    {{-- KIRI: Identitas Sekolah --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        <div class="card">
            <div class="card-header"><h3>🏫 Identitas Sekolah</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA SEKOLAH <span style="color:red">*</span></label>
                        <input type="text" name="nama_sekolah" value="{{ $settings['nama_sekolah'] ?? '' }}"
                            placeholder="SMA Negeri 1 ..."
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NPSN</label>
                            <input type="text" name="npsn" value="{{ $settings['npsn'] ?? '' }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NSS</label>
                            <input type="text" name="nss" value="{{ $settings['nss'] ?? '' }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">ALAMAT</label>
                        <textarea name="alamat" rows="2"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;resize:vertical;">{{ $settings['alamat'] ?? '' }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KELURAHAN/DESA</label>
                            <input type="text" name="kelurahan" value="{{ $settings['kelurahan'] ?? '' }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KECAMATAN</label>
                            <input type="text" name="kecamatan" value="{{ $settings['kecamatan'] ?? '' }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KABUPATEN/KOTA</label>
                            <input type="text" name="kabupaten" value="{{ $settings['kabupaten'] ?? '' }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">PROVINSI</label>
                            <input type="text" name="provinsi" value="{{ $settings['provinsi'] ?? '' }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KODE POS</label>
                            <input type="text" name="kode_pos" value="{{ $settings['kode_pos'] ?? '' }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TELEPON</label>
                            <input type="text" name="telepon" value="{{ $settings['telepon'] ?? '' }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">EMAIL</label>
                            <input type="email" name="email" value="{{ $settings['email'] ?? '' }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">WEBSITE</label>
                        <input type="text" name="website" value="{{ $settings['website'] ?? '' }}"
                            placeholder="https://..."
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                </div>
            </div>
        </div>

        {{-- Logo Sekolah --}}
        <div class="card">
            <div class="card-header"><h3>🖼️ Logo Sekolah</h3></div>
            <div class="card-body">
                @if(isset($settings['logo']) && $settings['logo'])
                <div style="text-align:center;margin-bottom:14px;">
                    <img src="{{ Storage::url($settings['logo']) }}"
                        style="max-height:90px;max-width:180px;object-fit:contain;border:1px solid #e2e8f0;border-radius:8px;padding:8px;">
                    <p style="font-size:11px;color:#94a3b8;margin-top:6px;">Logo saat ini</p>
                </div>
                @endif

                <div style="border:2px dashed #e2e8f0;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:all .2s;"
                    onclick="document.getElementById('logo-input').click()"
                    onmouseover="this.style.borderColor='#6366f1'"
                    onmouseout="this.style.borderColor='#e2e8f0'">
                    <i class="bi bi-image" style="font-size:28px;color:#94a3b8;display:block;margin-bottom:6px;"></i>
                    <p style="font-size:13px;color:#64748b;font-weight:600;">Klik untuk upload logo</p>
                    <p style="font-size:11px;color:#94a3b8;margin-top:4px;">PNG, JPG — Rekomendasi 200×200px</p>
                </div>
                <input type="file" id="logo-input" name="logo" accept="image/*" style="display:none;"
                    onchange="previewLogo(this)">

                <div id="logo-preview" style="display:none;text-align:center;margin-top:14px;">
                    <img id="logo-img" style="max-height:90px;max-width:180px;object-fit:contain;border:2px solid #6366f1;border-radius:8px;padding:8px;">
                    <p style="font-size:11px;color:#6366f1;font-weight:600;margin-top:6px;">✅ Logo baru siap diupload</p>
                </div>
            </div>
        </div>

    </div>

    {{-- KANAN: Kop Surat & Preview --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Mode Kop Surat --}}
        <div class="card">
            <div class="card-header"><h3>📄 Kop Surat Laporan</h3></div>
            <div class="card-body">

                {{-- Pilih Mode --}}
                <div style="margin-bottom:16px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:8px;">MODE KOP SURAT</label>
                    <div style="display:flex;gap:10px;">
                        <label id="label-auto"
                            style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px 16px;border:2px solid {{ ($settings['kop_mode'] ?? 'auto') == 'auto' ? '#6366f1' : '#e2e8f0' }};border-radius:10px;flex:1;transition:all .2s;"
                            onclick="setKopMode('auto')">
                            <input type="radio" name="kop_mode" value="auto" id="kop-auto"
                                {{ ($settings['kop_mode'] ?? 'auto') == 'auto' ? 'checked' : '' }}
                                style="accent-color:#6366f1;">
                            <div>
                                <p style="font-size:13px;font-weight:700;color:#374151;">⚙️ Otomatis</p>
                                <p style="font-size:11px;color:#94a3b8;">Generate dari data sekolah</p>
                            </div>
                        </label>
                        <label id="label-image"
                            style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px 16px;border:2px solid {{ ($settings['kop_mode'] ?? '') == 'image' ? '#6366f1' : '#e2e8f0' }};border-radius:10px;flex:1;transition:all .2s;"
                            onclick="setKopMode('image')">
                            <input type="radio" name="kop_mode" value="image" id="kop-image"
                                {{ ($settings['kop_mode'] ?? '') == 'image' ? 'checked' : '' }}
                                style="accent-color:#6366f1;">
                            <div>
                                <p style="font-size:13px;font-weight:700;color:#374151;">🖼️ Gambar Kop</p>
                                <p style="font-size:11px;color:#94a3b8;">Upload gambar kop jadi</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Upload Kop Gambar --}}
                <div id="section-kop-image" style="{{ ($settings['kop_mode'] ?? 'auto') == 'image' ? '' : 'display:none;' }}">
                    <div style="border-top:1px solid #f1f5f9;padding-top:14px;">

                        {{-- Kop Saat Ini --}}
                        @if(isset($settings['kop_surat']) && $settings['kop_surat'])
                        <div style="margin-bottom:12px;">
                            <p style="font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">KOP SAAT INI:</p>
                            <div style="border:1px solid #e2e8f0;border-radius:8px;padding:8px;background:#f8fafc;">
                                <img src="{{ Storage::url($settings['kop_surat']) }}"
                                    style="width:100%;max-height:100px;object-fit:contain;">
                            </div>
                        </div>
                        @endif

                        {{-- Drop Zone Upload --}}
                        <div id="kop-drop-zone"
                            style="border:2px dashed #e2e8f0;border-radius:10px;padding:24px;text-align:center;cursor:pointer;transition:all .2s;"
                            onclick="document.getElementById('kop-input').click()"
                            ondragover="event.preventDefault();this.style.borderColor='#6366f1';this.style.background='#f5f3ff';"
                            ondragleave="this.style.borderColor='#e2e8f0';this.style.background='#fff';"
                            ondrop="handleKopDrop(event)"
                            onmouseover="this.style.borderColor='#6366f1'"
                            onmouseout="this.style.borderColor='#e2e8f0'">
                            <i class="bi bi-file-image" style="font-size:32px;color:#6366f1;display:block;margin-bottom:8px;"></i>
                            <p style="font-size:13px;color:#64748b;font-weight:600;">
                                {{ isset($settings['kop_surat']) && $settings['kop_surat'] ? 'Klik untuk ganti kop surat' : 'Upload gambar kop surat' }}
                            </p>
                            <p style="font-size:11px;color:#94a3b8;margin-top:4px;">PNG, JPG — Rekomendasi landscape lebar penuh (max 5MB)</p>
                        </div>
                        <input type="file" id="kop-input" name="kop_surat" accept="image/*" style="display:none;"
                            onchange="previewKop(this)">

                        {{-- Preview Kop Baru --}}
                        <div id="kop-preview" style="display:none;margin-top:14px;">
                            <p style="font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">PREVIEW KOP BARU:</p>
                            <div style="border:2px solid #6366f1;border-radius:8px;padding:8px;background:#f8fafc;">
                                <img id="kop-img" style="width:100%;max-height:120px;object-fit:contain;">
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
                                <p id="kop-info" style="font-size:11px;color:#6366f1;font-weight:600;">✅ Kop baru siap diupload</p>
                                <button type="button" onclick="clearKop()"
                                    style="font-size:12px;color:#dc2626;background:none;border:none;cursor:pointer;">
                                    <i class="bi bi-x-circle"></i> Batal
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Section Otomatis --}}
                <div id="section-kop-auto" style="{{ ($settings['kop_mode'] ?? 'auto') != 'auto' ? 'display:none;' : '' }}">
                    <div style="border-top:1px solid #f1f5f9;padding-top:14px;">
                        <p style="font-size:12px;color:#64748b;">Kop surat akan digenerate otomatis dari data identitas sekolah di sebelah kiri.</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Preview Kop Surat --}}
        <div class="card">
            <div class="card-header">
                <h3>👁️ Preview Kop Surat di Laporan</h3>
            </div>
            <div class="card-body">

                {{-- Preview Mode Image --}}
                <div id="preview-kop-image" style="{{ ($settings['kop_mode'] ?? 'auto') == 'image' ? '' : 'display:none;' }}">
                    @if(isset($settings['kop_surat']) && $settings['kop_surat'])
                    <div style="border:1px solid #e2e8f0;border-radius:8px;padding:12px;background:#fff;">
                        <img src="{{ Storage::url($settings['kop_surat']) }}"
                            style="width:100%;max-height:120px;object-fit:contain;">
                        <hr style="border:2.5px double #374151;margin:8px 0 0;">
                    </div>
                    <p style="font-size:11px;color:#94a3b8;text-align:center;margin-top:6px;">Tampilan kop di laporan cetak</p>
                    @else
                    <div style="background:#fef3c7;border-radius:8px;padding:16px;text-align:center;">
                        <p style="font-size:13px;color:#d97706;">⚠️ Belum ada gambar kop. Upload terlebih dahulu.</p>
                    </div>
                    @endif
                </div>

                {{-- Preview Mode Auto --}}
                <div id="preview-kop-auto" style="{{ ($settings['kop_mode'] ?? 'auto') != 'auto' ? 'display:none;' : '' }}">
                    <div style="border:1px solid #e2e8f0;border-radius:8px;padding:16px;background:#fff;font-family:serif;">
                        <div style="display:flex;align-items:center;gap:14px;">

                            @if(isset($settings['logo']) && $settings['logo'])
                            <img src="{{ Storage::url($settings['logo']) }}"
                                style="width:65px;height:65px;object-fit:contain;flex-shrink:0;">
                            @else
                            <div style="width:65px;height:65px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">🏫</div>
                            @endif

                            <div style="flex:1;text-align:center;">
                                <p style="font-size:10px;font-weight:400;color:#374151;margin-bottom:1px;text-transform:uppercase;letter-spacing:.5px;">
                                    PEMERINTAH PROVINSI {{ strtoupper($settings['provinsi'] ?? '') }}
                                </p>
                                <p style="font-size:15px;font-weight:800;color:#1e293b;text-transform:uppercase;letter-spacing:1px;line-height:1.2;">
                                    {{ $settings['nama_sekolah'] ?? 'NAMA SEKOLAH' }}
                                </p>
                                <p style="font-size:10px;color:#374151;margin-top:2px;">
                                    {{ $settings['alamat'] ?? 'Alamat Sekolah' }}
                                    @if(!empty($settings['kecamatan'])), Kec. {{ $settings['kecamatan'] }}@endif
                                    @if(!empty($settings['kabupaten'])), {{ $settings['kabupaten'] }}@endif
                                    @if(!empty($settings['kode_pos'])) {{ $settings['kode_pos'] }}@endif
                                </p>
                                <p style="font-size:10px;color:#374151;">
                                    @if(!empty($settings['telepon']))Telp: {{ $settings['telepon'] }}@endif
                                    @if(!empty($settings['email'])) | Email: {{ $settings['email'] }}@endif
                                    @if(!empty($settings['website'])) | {{ $settings['website'] }}@endif
                                </p>
                                @if(!empty($settings['npsn']) || !empty($settings['nss']))
                                <p style="font-size:10px;color:#374151;">
                                    @if(!empty($settings['npsn']))NPSN: {{ $settings['npsn'] }}@endif
                                    @if(!empty($settings['nss'])) | NSS: {{ $settings['nss'] }}@endif
                                </p>
                                @endif
                            </div>

                        </div>
                        <hr style="border:2.5px double #374151;margin:8px 0 0;">
                    </div>
                    <p style="font-size:11px;color:#94a3b8;text-align:center;margin-top:6px;">Preview kop otomatis dari data sekolah</p>
                </div>

            </div>
        </div>

        {{-- Tombol Simpan --}}
        <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;">
            <i class="bi bi-save-fill"></i> Simpan Pengaturan
        </button>

    </div>
</div>
</form>

<script>
// ==========================================
// MODE KOP SURAT
// ==========================================
function setKopMode(mode) {
    // Toggle sections
    document.getElementById('section-kop-image').style.display  = mode === 'image' ? 'block' : 'none';
    document.getElementById('section-kop-auto').style.display   = mode === 'auto'  ? 'block' : 'none';
    document.getElementById('preview-kop-image').style.display  = mode === 'image' ? 'block' : 'none';
    document.getElementById('preview-kop-auto').style.display   = mode === 'auto'  ? 'block' : 'none';

    // Update border label
    document.getElementById('label-auto').style.borderColor  = mode === 'auto'  ? '#6366f1' : '#e2e8f0';
    document.getElementById('label-image').style.borderColor = mode === 'image' ? '#6366f1' : '#e2e8f0';

    // Set radio
    document.getElementById('kop-auto').checked  = mode === 'auto';
    document.getElementById('kop-image').checked = mode === 'image';
}

// ==========================================
// PREVIEW LOGO
// ==========================================
function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('logo-img').src = e.target.result;
        document.getElementById('logo-preview').style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}

// ==========================================
// PREVIEW KOP SURAT
// ==========================================
function previewKop(input) {
    if (!input.files || !input.files[0]) return;
    const file   = input.files[0];
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('kop-img').src = e.target.result;
        document.getElementById('kop-preview').style.display = 'block';

        // Juga update preview di card preview
        const previewImageDiv = document.getElementById('preview-kop-image');
        previewImageDiv.innerHTML = `
            <div style="border:1px solid #e2e8f0;border-radius:8px;padding:12px;background:#fff;">
                <img src="${e.target.result}" style="width:100%;max-height:120px;object-fit:contain;">
                <hr style="border:2.5px double #374151;margin:8px 0 0;">
            </div>
            <p style="font-size:11px;color:#6366f1;font-weight:600;text-align:center;margin-top:6px;">✅ Preview kop baru</p>
        `;

        const sizeKB = Math.round(file.size / 1024);
        document.getElementById('kop-info').textContent = `✅ ${file.name} • ${sizeKB}KB — siap diupload`;
    };
    reader.readAsDataURL(file);
}

function handleKopDrop(e) {
    e.preventDefault();
    const dz = document.getElementById('kop-drop-zone');
    dz.style.borderColor = '#e2e8f0';
    dz.style.background  = '#fff';

    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const dt = new DataTransfer();
        dt.items.add(file);
        const input = document.getElementById('kop-input');
        input.files = dt.files;
        previewKop(input);
    }
}

function clearKop() {
    document.getElementById('kop-input').value = '';
    document.getElementById('kop-preview').style.display = 'none';
}
</script>
@endsection
