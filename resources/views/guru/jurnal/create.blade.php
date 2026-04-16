@extends('layouts.app')

@section('page-title', 'Isi Jurnal Mengajar')
@section('page-subtitle', 'Input jurnal mengajar')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>📝 Isi Jurnal Mengajar</h1>
    <p>Tanggal: <strong>{{ now()->translatedFormat('l, d F Y') }}</strong>
        @if(!$isAdmin)
        <span style="font-size:12px;color:#f59e0b;margin-left:8px;">⚠️ Hanya bisa diisi untuk hari ini</span>
        @endif
    </p>
</div>

@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#dc2626;font-weight:600;">
    ❌ {{ session('error') }}
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

<form method="POST"
    action="{{ $isAdmin ? route('admin.jurnal.store') : route('guru.jurnal.store') }}"
    enctype="multipart/form-data"
    id="form-jurnal">
@csrf

<div class="grid grid-2" style="align-items:start;gap:20px;">

    {{-- KIRI --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Informasi Mengajar --}}
        <div class="card">
            <div class="card-header"><h3>📋 Informasi Mengajar</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">

                    {{-- Semester --}}
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">SEMESTER <span style="color:red">*</span></label>
                        <select name="semester_id" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">Pilih semester...</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ $semesterAktif?->id == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->nama }} {{ $sem->is_aktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Guru (admin saja) --}}
                    @if($isAdmin)
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">GURU <span style="color:red">*</span></label>
                        <select name="guru_id" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">Pilih guru...</option>
                            @foreach($guruList as $g)
                                <option value="{{ $g->id }}">{{ $g->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div style="background:#f8fafc;padding:12px;border-radius:8px;">
                        <p style="font-size:12px;color:#94a3b8;font-weight:700;margin-bottom:2px;">GURU</p>
                        <p style="font-size:14px;font-weight:700;color:#374151;">{{ $guru?->nama }}</p>
                        <p style="font-size:12px;color:#94a3b8;">{{ $guru?->jabatan }} — {{ $guru?->bidang_studi }}</p>
                    </div>
                    @endif

                    {{-- Mata Pelajaran --}}
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">MATA PELAJARAN <span style="color:red">*</span></label>
                        <select name="mata_pelajaran_id" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">Pilih mata pelajaran...</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}" {{ old('mata_pelajaran_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama }} ({{ $m->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kelas --}}
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KELAS <span style="color:red">*</span></label>
                        <select name="nama_rombel" id="select-rombel" required onchange="updateRombelId()"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="">Pilih kelas...</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->nama_rombel }}" data-id="{{ $r->rombongan_belajar_id }}"
                                    {{ old('nama_rombel') == $r->nama_rombel ? 'selected' : '' }}>
                                    {{ $r->nama_rombel }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="rombongan_belajar_id" id="input-rombel-id" value="{{ old('rombongan_belajar_id') }}">
                    </div>

                    {{-- Tanggal & Pertemuan --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TANGGAL <span style="color:red">*</span></label>
                            <input type="date" name="tanggal" required
                                value="{{ old('tanggal', date('Y-m-d')) }}"
                                {{ !$isAdmin ? 'readonly' : '' }}
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;{{ !$isAdmin ? 'background:#f8fafc;color:#94a3b8;' : '' }}">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">PERTEMUAN KE</label>
                            <input type="number" name="pertemuan_ke" min="1"
                                value="{{ old('pertemuan_ke', ($pertemuanTerakhir ?? 0) + 1) }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    {{-- Jam --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">JAM KE</label>
                            <input type="text" name="jam_ke" value="{{ old('jam_ke') }}" placeholder="1-2"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </br>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">MULAI</label>
                            <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        </br>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">SELESAI</label>
                            <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    {{-- Kehadiran --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">JUMLAH HADIR</label>
                            <input type="number" name="jumlah_hadir" min="0" value="{{ old('jumlah_hadir') }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TIDAK HADIR</label>
                            <input type="number" name="jumlah_tidak_hadir" min="0" value="{{ old('jumlah_tidak_hadir') }}"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Foto Pendukung --}}
        <div class="card">
            <div class="card-header">
                <h3>📷 Foto Pendukung
                    <span style="font-size:12px;font-weight:400;color:#94a3b8;">(Opsional — auto compress)</span>
                </h3>
            </div>
            <div class="card-body">
                <div id="drop-zone"
                    style="border:2px dashed #e2e8f0;border-radius:10px;padding:28px;text-align:center;cursor:pointer;transition:all .2s;"
                    onclick="document.getElementById('foto-input').click()"
                    ondragover="event.preventDefault();this.style.borderColor='#6366f1';this.style.background='#f5f3ff';"
                    ondragleave="this.style.borderColor='#e2e8f0';this.style.background='#fff';"
                    ondrop="handleDrop(event)">
                    <i class="bi bi-cloud-upload" style="font-size:36px;color:#94a3b8;display:block;margin-bottom:8px;"></i>
                    <p style="font-size:13px;color:#64748b;font-weight:600;">Klik atau drag foto ke sini</p>
                    <p style="font-size:11px;color:#94a3b8;margin-top:4px;">JPG, PNG — Max 10MB (otomatis dicompress ke JPEG 70%, max 1200px)</p>
                </div>
                <input type="file" id="foto-input" name="foto_pendukung" accept="image/*" capture="environment" style="display:none;" onchange="previewFoto(this)">

                <div id="foto-preview" style="display:none;margin-top:14px;">
                    <div style="text-align:center;">
                        <img id="foto-img" style="max-width:100%;max-height:220px;border-radius:8px;border:1px solid #e2e8f0;object-fit:contain;">
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
                        <p id="foto-info" style="font-size:11px;color:#94a3b8;"></p>
                        <button type="button" onclick="clearFoto()"
                            style="font-size:12px;color:#dc2626;background:none;border:none;cursor:pointer;">
                            <i class="bi bi-x-circle"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- KANAN --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Isi Jurnal --}}
        <div class="card">
            <div class="card-header"><h3>📖 Isi Jurnal</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">
                            MATERI / KOMPETENSI DASAR <span style="color:red">*</span>
                        </label>
                        <textarea name="materi" required rows="4"
                            placeholder="Materi atau Kompetensi Dasar yang diajarkan..."
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;resize:vertical;">{{ old('materi') }}</textarea>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">
                            KEGIATAN PEMBELAJARAN <span style="color:red">*</span>
                        </label>
                        <textarea name="kegiatan" required rows="5"
                            placeholder="Deskripsi kegiatan belajar mengajar (pendahuluan, inti, penutup)..."
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;resize:vertical;">{{ old('kegiatan') }}</textarea>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">CATATAN</label>
                        <textarea name="catatan" rows="3"
                            placeholder="Catatan tambahan, kendala, atau evaluasi pembelajaran..."
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;resize:vertical;">{{ old('catatan') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- Signature Pad --}}
        <div class="card">
            <div class="card-header">
                <h3>✍️ Tanda Tangan Guru <span style="color:red">*</span></h3>
            </div>
            <div class="card-body">
                <div style="border:2px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#f8fafc;position:relative;">
                    <canvas id="signature-pad" width="600" height="200"
                        style="width:100%;height:200px;display:block;touch-action:none;cursor:crosshair;">
                    </canvas>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none;opacity:0.3;" id="ttd-placeholder">
                        <p style="font-size:13px;color:#94a3b8;text-align:center;">Tanda tangan di sini</p>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:10px;">
                    <p style="font-size:11px;color:#94a3b8;">Gunakan mouse atau sentuhan layar</p>
                    <button type="button" onclick="clearSignature()"
                        class="btn btn-sm" style="background:#fee2e2;color:#dc2626;">
                        <i class="bi bi-eraser-fill"></i> Hapus TTD
                    </button>
                </div>

                <input type="hidden" name="tanda_tangan" id="signature-data">
                <div id="ttd-error" style="display:none;background:#fee2e2;border-radius:6px;padding:8px 12px;margin-top:8px;font-size:12px;color:#dc2626;font-weight:600;">
                    ⚠️ Tanda tangan wajib diisi sebelum menyimpan jurnal!
                </div>
            </div>
        </div>

        {{-- Tombol --}}
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ $isAdmin ? route('admin.jurnal.index') : route('guru.jurnal.index') }}"
                class="btn" style="background:#f1f5f9;color:#374151;">
                <i class="bi bi-x-lg"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary" id="btn-submit"
                style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                <i class="bi bi-journal-check"></i> Simpan Jurnal
            </button>
        </div>

    </div>

</div>
</form>

<script>
// ==========================================
// SIGNATURE PAD
// ==========================================
const canvas  = document.getElementById('signature-pad');
const ctx     = canvas.getContext('2d');
let drawing   = false;
let hasDrawn  = false;

// Set canvas resolution
function resizeCanvas() {
    const rect = canvas.getBoundingClientRect();
    canvas.width  = rect.width * window.devicePixelRatio;
    canvas.height = rect.height * window.devicePixelRatio;
    ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
    ctx.strokeStyle = '#1e293b';
    ctx.lineWidth   = 2;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';
}
resizeCanvas();

function getPos(e) {
    const rect   = canvas.getBoundingClientRect();
    const source = e.touches ? e.touches[0] : e;
    return {
        x: source.clientX - rect.left,
        y: source.clientY - rect.top
    };
}

function startDraw(e) {
    drawing = true;
    const p = getPos(e);
    ctx.beginPath();
    ctx.moveTo(p.x, p.y);
    document.getElementById('ttd-placeholder').style.display = 'none';
}

function draw(e) {
    if (!drawing) return;
    const p = getPos(e);
    ctx.lineTo(p.x, p.y);
    ctx.stroke();
    hasDrawn = true;
}

function stopDraw() { drawing = false; }

canvas.addEventListener('mousedown',  startDraw);
canvas.addEventListener('mousemove',  draw);
canvas.addEventListener('mouseup',    stopDraw);
canvas.addEventListener('mouseleave', stopDraw);
canvas.addEventListener('touchstart', e => { e.preventDefault(); startDraw(e); }, { passive: false });
canvas.addEventListener('touchmove',  e => { e.preventDefault(); draw(e); }, { passive: false });
canvas.addEventListener('touchend',   stopDraw);

function clearSignature() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasDrawn = false;
    document.getElementById('signature-data').value = '';
    document.getElementById('ttd-placeholder').style.display = 'block';
    document.getElementById('ttd-error').style.display = 'none';
}

// ==========================================
// FOTO COMPRESS (CLIENT SIDE)
// ==========================================
function previewFoto(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            // Resize max 1200px
            const maxW = 1200;
            let w = img.width, h = img.height;
            if (w > maxW) { h = Math.round(h * maxW / w); w = maxW; }

            const c = document.createElement('canvas');
            c.width  = w;
            c.height = h;
            c.getContext('2d').drawImage(img, 0, 0, w, h);

            // Preview
            const compressed = c.toDataURL('image/jpeg', 0.7);
            document.getElementById('foto-img').src = compressed;
            document.getElementById('foto-preview').style.display = 'block';

            const sizeKB = Math.round(compressed.length * 0.75 / 1024);
            const origKB = Math.round(file.size / 1024);
            document.getElementById('foto-info').textContent =
                `${w}×${h}px • Asli: ${origKB}KB → Setelah compress: ~${sizeKB}KB`;

            // Ganti file input dengan blob hasil compress
            c.toBlob(function(blob) {
                const compressedFile = new File([blob], 'foto_jurnal.jpg', { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(compressedFile);
                document.getElementById('foto-input').files = dt.files;
            }, 'image/jpeg', 0.7);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function handleDrop(e) {
    e.preventDefault();
    const dz = document.getElementById('drop-zone');
    dz.style.borderColor = '#e2e8f0';
    dz.style.background  = '#fff';

    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const dt = new DataTransfer();
        dt.items.add(file);
        const input = document.getElementById('foto-input');
        input.files = dt.files;
        previewFoto(input);
    }
}

function clearFoto() {
    document.getElementById('foto-input').value = '';
    document.getElementById('foto-preview').style.display = 'none';
}

// ==========================================
// ROMBEL ID
// ==========================================
function updateRombelId() {
    const sel = document.getElementById('select-rombel');
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('input-rombel-id').value = opt.dataset.id || '';
}

// Init rombel id saat load jika ada old value
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('select-rombel');
    if (sel.value) updateRombelId();
});

// ==========================================
// FORM SUBMIT - validasi TTD
// ==========================================
document.getElementById('form-jurnal').addEventListener('submit', function(e) {
    if (!hasDrawn) {
        e.preventDefault();
        document.getElementById('ttd-error').style.display = 'block';
        document.getElementById('signature-pad').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    document.getElementById('ttd-error').style.display = 'none';

    // Simpan signature sebagai base64
    const signatureCanvas = document.createElement('canvas');
    const rect = canvas.getBoundingClientRect();
    signatureCanvas.width  = rect.width;
    signatureCanvas.height = rect.height;
    const sCtx = signatureCanvas.getContext('2d');
    sCtx.drawImage(canvas, 0, 0, rect.width, rect.height);
    document.getElementById('signature-data').value = signatureCanvas.toDataURL('image/png');

    // Loading state
    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
});
</script>
@endsection
