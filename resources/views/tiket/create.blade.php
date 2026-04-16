{{-- ============================================================ --}}
{{-- FILE: resources/views/tiket/create.blade.php               --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('page-title', 'Buat Tiket Baru')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
<div class="page-title">
    <h1>🎫 Buat Tiket Baru</h1>
    <p>Sampaikan kritik, saran, atau pengaduan Anda</p>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#dc2626;">
    <strong>Ada kesalahan:</strong>
    <ul style="margin:6px 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div style="max-width:720px;margin:0 auto;">
    <form method="POST" action="{{ route('tiket.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header"><h3>📝 Detail Tiket</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:16px;">

                    <div>
                        <label class="form-label">JUDUL TIKET <span style="color:red">*</span></label>
                        <input type="text" name="judul" required value="{{ old('judul') }}"
                            placeholder="Ringkas judul tiket Anda..."
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;outline:none;font-family:inherit;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div>
                            <label class="form-label">KATEGORI <span style="color:red">*</span></label>
                            <select name="kategori" required id="select-kategori" onchange="toggleLainnya()"
                                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriList as $v => $l)
                                    <option value="{{ $v }}" {{ old('kategori') == $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">PRIORITAS</label>
                            <select name="prioritas"
                                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                                <option value="rendah" {{ old('prioritas') == 'rendah' ? 'selected' : '' }}>🟢 Rendah</option>
                                <option value="sedang" {{ old('prioritas','sedang') == 'sedang' ? 'selected' : '' }}>🟡 Sedang</option>
                                <option value="tinggi" {{ old('prioritas') == 'tinggi' ? 'selected' : '' }}>🔴 Tinggi</option>
                            </select>
                        </div>
                    </div>

                    <div id="wrap-lainnya" style="{{ old('kategori') == 'lainnya' ? '' : 'display:none;' }}">
                        <label class="form-label">SEBUTKAN KATEGORI LAINNYA</label>
                        <input type="text" name="kategori_lainnya" value="{{ old('kategori_lainnya') }}"
                            placeholder="cth: Kebersihan Toilet, Kantin, dll"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div>
                        <label class="form-label">ISI TIKET <span style="color:red">*</span></label>
                        <textarea name="isi" required rows="6"
                            placeholder="Jelaskan detail tiket Anda dengan jelas dan lengkap..."
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;resize:vertical;">{{ old('isi') }}</textarea>
                    </div>

                    {{-- Upload Foto --}}
                    <div>
                        <label class="form-label">FOTO BUKTI <small style="color:#94a3b8;">(opsional, JPG/PNG max 5MB)</small></label>
                        <div id="drop-zone" style="border:2px dashed #c7d2fe;border-radius:10px;padding:20px;text-align:center;cursor:pointer;"
                            onclick="document.getElementById('input-foto').click()"
                            ondragover="event.preventDefault();this.style.borderColor='#6366f1'"
                            ondragleave="this.style.borderColor='#c7d2fe'"
                            ondrop="handleDrop(event)">
                            <i class="bi bi-image" style="font-size:28px;color:#c7d2fe;display:block;margin-bottom:6px;"></i>
                            <p style="font-size:13px;color:#6366f1;font-weight:600;">Klik atau drag foto</p>
                            <p style="font-size:11px;color:#94a3b8;margin-top:2px;">Auto-compress max 1200px, kualitas 75%</p>
                        </div>
                        <input type="file" id="input-foto" name="foto" accept=".jpg,.jpeg,.png" style="display:none;" onchange="previewFoto(this)">
                        <div id="preview-foto" style="display:none;margin-top:10px;">
                            <div style="display:flex;align-items:center;gap:10px;padding:10px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;margin-bottom:8px;">
                                <i class="bi bi-image-fill" style="color:#16a34a;font-size:18px;"></i>
                                <div style="flex:1;">
                                    <div id="preview-nama" style="font-size:13px;font-weight:600;"></div>
                                    <div id="preview-size" style="font-size:11px;color:#94a3b8;"></div>
                                </div>
                                <button type="button" onclick="hapusFoto()" style="background:none;border:none;cursor:pointer;color:#dc2626;"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <img id="preview-img" style="width:100%;max-height:200px;object-fit:cover;border-radius:8px;">
                        </div>
                    </div>

                    {{-- Anonim --}}
                    @if(auth()->user()->hasRole('siswa'))
                    <div style="padding:14px;background:#fef9c3;border-radius:10px;border:1px solid #fde68a;">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                            <input type="checkbox" name="is_anonim" value="1" {{ old('is_anonim') ? 'checked' : '' }}
                                style="width:18px;height:18px;cursor:pointer;">
                            <div>
                                <p style="font-size:13px;font-weight:700;color:#92400e;">🎭 Kirim sebagai Anonim</p>
                                <p style="font-size:11px;color:#b45309;margin-top:2px;">Identitas anda akan disembunyikan.</p>
                            </div>
                        </label>
                    </div>
                    @endif

                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <a href="{{ route('tiket.index') }}" class="btn" style="background:#f1f5f9;color:#374151;">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary" style="padding:10px 24px;">
                            <i class="bi bi-send-fill"></i> Kirim Tiket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>.form-label { font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px; }</style>
<script>
function toggleLainnya() {
    const val = document.getElementById('select-kategori').value;
    document.getElementById('wrap-lainnya').style.display = val === 'lainnya' ? 'block' : 'none';
}
function previewFoto(input) {
    const file = input.files[0]; if (!file) return;
    document.getElementById('preview-nama').textContent = file.name;
    document.getElementById('preview-size').textContent = (file.size/1024).toFixed(0) + ' KB';
    const reader = new FileReader();
    reader.onload = e => { document.getElementById('preview-img').src = e.target.result; };
    reader.readAsDataURL(file);
    document.getElementById('preview-foto').style.display = 'block';
    document.getElementById('drop-zone').style.display = 'none';
}
function hapusFoto() {
    document.getElementById('input-foto').value = '';
    document.getElementById('preview-foto').style.display = 'none';
    document.getElementById('drop-zone').style.display = 'block';
}
function handleDrop(e) {
    e.preventDefault(); e.currentTarget.style.borderColor = '#c7d2fe';
    const file = e.dataTransfer.files[0]; if (!file) return;
    const input = document.getElementById('input-foto');
    const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files;
    previewFoto(input);
}
</script>
@endsection
