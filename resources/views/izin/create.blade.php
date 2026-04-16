@extends('layouts.app')
@section('title', 'Ajukan Izin Berencana')
@section('sidebar-menu') @include('partials.sidebar_siswa') @endsection
@section('content')
@include('partials._dashboard_responsive')

<style>
.izin-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.izin-date-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.izin-action {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    justify-content: flex-end;
}
.izin-field label {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    display: block;
    margin-bottom: 4px;
}
.izin-field input,
.izin-field select,
.izin-field textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    font-family: inherit;
    box-sizing: border-box;
    background: #fff;
}
.izin-field textarea { resize: vertical; }
.izin-field input:focus,
.izin-field select:focus,
.izin-field textarea:focus {
    border-color: #6366f1;
}
.izin-right-col {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

@media (max-width: 768px) {
    .izin-grid {
        grid-template-columns: 1fr;
    }
    .izin-date-grid {
        grid-template-columns: 1fr;
    }
    .izin-action {
        flex-direction: column-reverse;
    }
    .izin-action a,
    .izin-action button {
        width: 100%;
        text-align: center;
        justify-content: center;
    }
}
</style>

<div class="page-title">
    <h1>📋 Ajukan Izin Berencana</h1>
    <p>Isi formulir dengan lengkap dan benar</p>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
    <i class="bi bi-exclamation-circle-fill me-2"></i>
    <ul style="margin:6px 0 0 16px;padding:0;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- Info batas hari --}}
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#92400e;">
    <i class="bi bi-info-circle-fill me-2"></i>
    <strong>Perhatian:</strong> Pengajuan mandiri maksimal <strong>2 hari</strong>.
    Lebih dari 2 hari memerlukan persetujuan khusus dari Kepala Sekolah.
</div>

<form method="POST" action="{{ route('izin.store') }}" id="izinForm">
    @csrf

    <div class="izin-grid">

        {{-- Kolom Kiri: Detail Izin --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-file-earmark-text me-2" style="color:#6366f1"></i>Detail Izin</h3>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div class="izin-field">
                        <label>JENIS IZIN <span style="color:#ef4444">*</span></label>
                        <select name="jenis" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach(App\Models\IzinBerencana::jenisList() as $k => $label)
                            <option value="{{ $k }}" {{ old('jenis') === $k ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="izin-field">
                        <label>ALASAN <span style="color:#ef4444">*</span></label>
                        <textarea name="alasan" rows="4" required minlength="10" maxlength="500"
                            placeholder="Jelaskan alasan izin secara singkat dan jelas...">{{ old('alasan') }}</textarea>
                        <p style="font-size:11px;color:#94a3b8;margin-top:3px;">Minimal 10 karakter</p>
                    </div>

                    <div class="izin-date-grid">
                        <div class="izin-field">
                            <label>TANGGAL MULAI <span style="color:#ef4444">*</span></label>
                            <input type="date" name="tanggal_mulai" id="tglMulai"
                                value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}"
                                required onchange="hitungHari()">
                        </div>
                        <div class="izin-field">
                            <label>TANGGAL SELESAI <span style="color:#ef4444">*</span></label>
                            <input type="date" name="tanggal_selesai" id="tglSelesai"
                                value="{{ old('tanggal_selesai', date('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}"
                                required onchange="hitungHari()">
                        </div>
                    </div>

                    {{-- Preview jumlah hari --}}
                    <div id="hariPreview" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;text-align:center;display:none;">
                        <span style="font-size:11px;color:#16a34a;font-weight:600;">JUMLAH HARI</span>
                        <div id="hariValue" style="font-size:28px;font-weight:800;color:#15803d;"></div>
                        <div id="hariWarning" style="font-size:11px;display:none;color:#d97706;margin-top:4px;">
                            ⚠️ Lebih dari 2 hari — memerlukan persetujuan Kepala Sekolah
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Ortu + TTD --}}
        <div class="izin-right-col">

            {{-- Verifikasi Ortu --}}
            <div class="card">
                <div class="card-header">
                    <h3><i class="bi bi-people-fill me-2" style="color:#6366f1"></i>Verifikasi Orang Tua</h3>
                </div>
                <div class="card-body">
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        <div class="izin-field">
                            <label>NAMA ORANG TUA / WALI <span style="color:#ef4444">*</span></label>
                            <input type="text" name="nama_ortu" value="{{ old('nama_ortu') }}"
                                placeholder="Nama lengkap orang tua / wali" required>
                        </div>
                        <div class="izin-field">
                            <label>NOMOR HP ORANG TUA <span style="color:#ef4444">*</span></label>
                            <input type="tel" name="no_hp_ortu" value="{{ old('no_hp_ortu') }}"
                                placeholder="Contoh: 08123456789" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Signature Pad --}}
            <div class="card">
                <div class="card-header">
                    <h3><i class="bi bi-pen-fill me-2" style="color:#6366f1"></i>Tanda Tangan Orang Tua</h3>
                </div>
                <div class="card-body">
                    <p style="font-size:12px;color:#64748b;margin-bottom:10px;">
                        Minta orang tua untuk menandatangani di kotak di bawah ini.
                    </p>
                    <div style="position:relative;border:2px dashed #e2e8f0;border-radius:10px;background:#fafafa;">
                        <canvas id="signaturePad"
                            style="width:100%;height:160px;border-radius:8px;cursor:crosshair;display:block;touch-action:none;">
                        </canvas>
                        <div id="signPlaceholder" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;color:#94a3b8;font-size:13px;">
                            <i class="bi bi-pen me-2"></i>Tandatangan di sini
                        </div>
                    </div>
                    <input type="hidden" name="ttd_ortu" id="ttdOrtu">
                    <div id="ttdError" style="color:#ef4444;font-size:11px;margin-top:4px;display:none;">
                        Tanda tangan orang tua wajib diisi.
                    </div>
                    <div style="display:flex;gap:8px;margin-top:10px;align-items:center;">
                        <button type="button" onclick="clearSignature()"
                            style="padding:6px 14px;background:#f1f5f9;color:#374151;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                            <i class="bi bi-eraser me-1"></i>Hapus TTD
                        </button>
                        <span id="ttdStatus" style="font-size:11px;color:#94a3b8;"></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Action --}}
    <div class="izin-action">
        <a href="{{ route('izin.index') }}"
            style="padding:10px 24px;background:#f1f5f9;color:#374151;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;">
            <i class="bi bi-arrow-left me-2"></i>Batal
        </a>
        <button type="submit" id="btnSubmit" class="btn btn-primary"
            style="padding:10px 28px;font-size:13px;display:inline-flex;align-items:center;">
            <i class="bi bi-send-fill me-2"></i>Ajukan Izin
        </button>
    </div>

</form>

<script>
// ── Hitung Hari ──────────────────────────────────────────────────
function hitungHari() {
    const mulai   = new Date(document.getElementById('tglMulai').value);
    const selesai = new Date(document.getElementById('tglSelesai').value);
    if (!mulai || !selesai || selesai < mulai) {
        document.getElementById('hariPreview').style.display = 'none';
        return;
    }
    const hari = Math.round((selesai - mulai) / 86400000) + 1;
    document.getElementById('hariValue').textContent = hari + ' hari';
    document.getElementById('hariPreview').style.display = 'block';
    document.getElementById('hariWarning').style.display = hari > 2 ? 'block' : 'none';
    document.getElementById('hariPreview').style.borderColor = hari > 2 ? '#fde68a' : '#bbf7d0';
    document.getElementById('hariPreview').style.background  = hari > 2 ? '#fffbeb' : '#f0fdf4';
}
hitungHari();

// ── Signature Pad ────────────────────────────────────────────────
const canvas = document.getElementById('signaturePad');
const ctx    = canvas.getContext('2d');
let drawing  = false;
let hasSigned = false;

// Set canvas internal resolution sesuai display size (fix blur di mobile)
function resizeCanvas() {
    const rect  = canvas.getBoundingClientRect();
    const ratio = window.devicePixelRatio || 1;
    canvas.width  = rect.width  * ratio;
    canvas.height = rect.height * ratio;
    ctx.scale(ratio, ratio);
    ctx.strokeStyle = '#1e293b';
    ctx.lineWidth   = 2.5;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';
}
resizeCanvas();
window.addEventListener('resize', () => { if (!hasSigned) resizeCanvas(); });

function getPos(e) {
    const rect   = canvas.getBoundingClientRect();
    const ratio  = window.devicePixelRatio || 1;
    const src    = e.touches ? e.touches[0] : e;
    return {
        x: (src.clientX - rect.left),
        y: (src.clientY - rect.top),
    };
}

canvas.addEventListener('mousedown',  e => { drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
canvas.addEventListener('mousemove',  e => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); setHasSigned(); });
canvas.addEventListener('mouseup',    () => drawing = false);
canvas.addEventListener('mouseleave', () => drawing = false);
canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }, {passive:false});
canvas.addEventListener('touchmove',  e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); setHasSigned(); }, {passive:false});
canvas.addEventListener('touchend',   () => drawing = false);

function setHasSigned() {
    if (!hasSigned) {
        hasSigned = true;
        document.getElementById('signPlaceholder').style.display = 'none';
        document.getElementById('ttdStatus').textContent = '✅ Tanda tangan diterima';
        document.getElementById('ttdStatus').style.color = '#16a34a';
    }
}

function clearSignature() {
    const rect  = canvas.getBoundingClientRect();
    ctx.clearRect(0, 0, rect.width, rect.height);
    hasSigned = false;
    document.getElementById('signPlaceholder').style.display = 'flex';
    document.getElementById('ttdStatus').textContent = '';
    document.getElementById('ttdOrtu').value = '';
    document.getElementById('ttdError').style.display = 'none';
    canvas.style.borderColor = '';
}

// Submit — inject TTD ke hidden input
document.getElementById('izinForm').addEventListener('submit', function(e) {
    if (!hasSigned) {
        e.preventDefault();
        document.getElementById('ttdError').style.display = 'block';
        canvas.parentElement.style.borderColor = '#ef4444';
        canvas.scrollIntoView({behavior:'smooth', block:'center'});
        return;
    }
    document.getElementById('ttdOrtu').value = canvas.toDataURL('image/png');
});
</script>
@endsection
