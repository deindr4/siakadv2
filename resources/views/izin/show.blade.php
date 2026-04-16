@extends('layouts.app')
@section('title', 'Detail Izin ' . $izin->nomor_izin)

@section('content')
<style>
    /* Container Utama Responsif */
    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr; /* Desktop: 2 kolom */
        gap: 20px;
        align-items: start;
    }

    /* Grid Informasi (2 kolom di dalam card) */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* Penyesuaian Mobile */
    @media (max-width: 991px) {
        .detail-grid {
            grid-template-columns: 1fr; /* Mobile: Tumpuk ke bawah */
        }

        /* Sidebar aksi pindah ke urutan pertama di mobile jika perlu,
           atau tetap di bawah. Di sini kita biarkan di bawah agar user baca detail dulu. */
        .sidebar-aksi {
            position: static !important;
        }

        .info-grid {
            grid-template-columns: 1fr; /* Info jadi 1 kolom di HP agar tidak sempit */
        }

        .page-title h1 { font-size: 1.4rem; }
    }

    .label-keterangan {
        font-size: 10px;
        color: #94a3b8;
        font-weight: 700;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .isi-keterangan {
        font-weight: 600;
        font-size: 14px;
        color: #1e293b;
        margin: 0;
    }
</style>

<div class="page-title">
    <h1>📋 Detail Izin Berencana</h1>
    <p style="margin:0; font-family:monospace; color:#6366f1;">{{ $izin->nomor_izin }}</p>
</div>

{{-- Breadcrumb --}}
<div style="display:flex; align-items:center; gap:8px; font-size:12px; margin-bottom:20px;">
    <a href="{{ route('izin.index') }}" style="color:#6366f1; text-decoration:none; font-weight:600;">Izin Berencana</a>
    <i class="bi bi-chevron-right" style="font-size:10px; color:#94a3b8;"></i>
    <span style="color:#94a3b8;">Detail</span>
</div>

{{-- Alerts --}}
@if(session('success') || session('error'))
<div style="background:{{ session('success') ? '#dcfce7' : '#fef2f2' }}; border:1px solid {{ session('success') ? '#bbf7d0' : '#fecaca' }}; color:{{ session('success') ? '#15803d' : '#dc2626' }}; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13px;">
    <i class="bi {{ session('success') ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }} me-2"></i>
    {{ session('success') ?? session('error') }}
</div>
@endif

<div class="detail-grid">

    {{-- Kolom Kiri: Detail Konten --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- Info Umum --}}
        <div class="card" style="border:none; box-shadow:0 1px 3px rgba(0,0,0,0.1); border-radius:12px;">
            <div class="card-header" style="background:#fff; display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #f1f5f9;">
                <h3 style="font-size:15px; margin:0; font-weight:700;"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Data Pengajuan</h3>
                <span style="background:{{ $izin->statusBg() }}; color:{{ $izin->statusColor() }}; font-weight:700; padding:4px 12px; border-radius:20px; font-size:11px;">
                    {{ $izin->statusLabel() }}
                </span>
            </div>
            <div class="card-body" style="padding:20px;">
                <div class="info-grid">
                    <div>
                        <p class="label-keterangan">Siswa</p>
                        <p class="isi-keterangan">{{ $izin->siswa?->nama }}</p>
                        <p style="font-size:12px; color:#94a3b8; margin:0;">{{ $izin->siswa?->nama_rombel ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="label-keterangan">Jenis Izin</p>
                        <p class="isi-keterangan">{{ App\Models\IzinBerencana::jenisList()[$izin->jenis] ?? $izin->jenis }}</p>
                    </div>
                    <div>
                        <p class="label-keterangan">Periode Izin</p>
                        <p class="isi-keterangan" style="font-size:13px;">
                            {{ $izin->tanggal_mulai->translatedFormat('d M Y') }}
                            <span style="color:#94a3b8; font-weight:400;">s/d</span>
                            {{ $izin->tanggal_selesai->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="label-keterangan">Durasi</p>
                        <p class="isi-keterangan" style="color:{{ $izin->jumlah_hari > 2 ? '#d97706' : '#16a34a' }}">
                            {{ $izin->jumlah_hari }} Hari
                            @if($izin->jumlah_hari > 2) <small style="font-weight:400;">(Perlu Atensi)</small> @endif
                        </p>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <p class="label-keterangan">Alasan / Keperluan</p>
                        <div style="background:#f8fafc; padding:12px; border-radius:8px; font-size:13px; color:#475569; line-height:1.5; border:1px solid #f1f5f9;">
                            {{ $izin->alasan }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Verifikasi Ortu --}}
        <div class="card" style="border:none; box-shadow:0 1px 3px rgba(0,0,0,0.1); border-radius:12px;">
            <div class="card-header" style="background:#fff; padding:16px 20px; border-bottom:1px solid #f1f5f9;">
                <h3 style="font-size:15px; margin:0; font-weight:700;"><i class="bi bi-person-check me-2 text-primary"></i>Persetujuan Orang Tua</h3>
            </div>
            <div class="card-body" style="padding:20px;">
                <div class="info-grid" style="margin-bottom:15px;">
                    <div>
                        <p class="label-keterangan">Nama Orang Tua</p>
                        <p class="isi-keterangan">{{ $izin->nama_ortu }}</p>
                    </div>
                    <div>
                        <p class="label-keterangan">Kontak (WhatsApp)</p>
                        <p class="isi-keterangan">{{ $izin->no_hp_ortu }}</p>
                    </div>
                </div>
                @if($izin->ttd_ortu)
                <p class="label-keterangan">Tanda Tangan</p>
                <div style="border:1px dashed #e2e8f0; border-radius:8px; padding:10px; background:#fff; display:inline-block;">
                    <img src="{{ $izin->ttd_ortu }}" style="max-width:100%; height:80px; object-fit:contain;">
                </div>
                @endif
            </div>
        </div>

        {{-- Info Review (Jika sudah diproses) --}}
        @if($izin->status !== 'pending')
        <div class="card" style="border:none; border-left:4px solid {{ $izin->statusColor() }}; box-shadow:0 1px 3px rgba(0,0,0,0.1); border-radius:12px;">
            <div class="card-body" style="padding:20px;">
                <h3 style="font-size:14px; margin-bottom:12px; font-weight:700;"><i class="bi bi-info-circle me-2"></i>Riwayat Pemeriksaan</h3>
                <div class="info-grid">
                    <div>
                        <p class="label-keterangan">Pemeriksa</p>
                        <p class="isi-keterangan">{{ $izin->disetujuiOleh?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="label-keterangan">Waktu Proses</p>
                        <p class="isi-keterangan" style="font-size:12px;">{{ $izin->disetujui_pada?->translatedFormat('d M Y, H:i') ?? '-' }}</p>
                    </div>
                    @if($izin->catatan_approver)
                    <div style="grid-column: 1 / -1;">
                        <p class="label-keterangan">Catatan Sekolah</p>
                        <p style="font-size:13px; color:#475569; margin:0; font-style:italic;">"{{ $izin->catatan_approver }}"</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Kolom Kanan: Sidebar Aksi --}}
    <div class="sidebar-aksi" style="display:flex; flex-direction:column; gap:16px; position:sticky; top:20px;">

        @if($izin->status === 'pending' && auth()->user()->hasAnyRole(['admin','kepala_sekolah']))
        <div class="card" style="border:none; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); border-radius:12px; overflow:hidden;">
            <div style="background:#16a34a; padding:15px; color:#fff; text-align:center;">
                <h3 style="margin:0; font-size:14px; font-weight:700; color:#fff;">Tindakan Admin</h3>
            </div>
            <div class="card-body" style="padding:16px;">
                <form method="POST" action="{{ route('izin.approve', $izin) }}">
                    @csrf
                    <div style="margin-bottom:12px;">
                        <label class="label-keterangan" style="color:#374151;">Hari yang Disetujui</label>
                        <input type="number" name="jumlah_hari_disetujui" value="{{ $izin->jumlah_hari }}" min="1"
                            style="width:100%; padding:10px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; outline:none; font-weight:700;">
                    </div>
                    <div style="margin-bottom:15px;">
                        <label class="label-keterangan" style="color:#374151;">Catatan (Opsional)</label>
                        <textarea name="catatan_approver" rows="2" style="width:100%; padding:10px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none; resize:none;"></textarea>
                    </div>
                    <button type="submit" style="width:100%; background:#16a34a; color:#fff; border:none; padding:12px; border-radius:8px; font-weight:700; cursor:pointer; margin-bottom:10px;">
                        Setujui Izin
                    </button>
                </form>

                <hr style="border:0; border-top:1px solid #f1f5f9; margin:15px 0;">

                <form method="POST" action="{{ route('izin.tolak', $izin) }}" id="tolakForm">
                    @csrf
                    <div style="margin-bottom:10px;">
                        <label class="label-keterangan" style="color:#374151;">Alasan Tolak <span style="color:red;">*</span></label>
                        <textarea name="catatan_approver" id="alasanTolak" rows="2" style="width:100%; padding:10px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none;"></textarea>
                    </div>
                    <button type="button" class="btn-danger" style="width:100%; background:#ef4444; color:#fff; border:none; padding:10px; border-radius:8px; font-weight:700; cursor:pointer;"
                        onclick="if(!document.getElementById('alasanTolak').value.trim()){ alert('Alasan penolakan wajib diisi!'); return; } if(confirm('Tolak pengajuan ini?')) document.getElementById('tolakForm').submit();">
                        Tolak Izin
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Status Badge Card --}}
        <div style="background:#fff; border:1.5px solid #f1f5f9; border-radius:12px; padding:20px; text-align:center;">
            <div style="font-size:40px; margin-bottom:10px;">
                @if($izin->status === 'pending') <i class="bi bi-hourglass-split" style="color:#d97706;"></i>
                @elseif($izin->status === 'disetujui') <i class="bi bi-check-circle-fill" style="color:#16a34a;"></i>
                @else <i class="bi bi-x-circle-fill" style="color:#ef4444;"></i> @endif
            </div>
            <div style="font-weight:800; color:#1e293b; font-size:16px; text-transform:uppercase;">{{ $izin->statusLabel() }}</div>
            <p style="font-size:12px; color:#94a3b8; margin:5px 0 0;">Status Pengajuan Saat Ini</p>
        </div>

        @if($izin->status === 'disetujui')
        <a href="{{ route('izin.cetak', $izin) }}" target="_blank"
            class="btn" style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;">
            <i class="bi bi-file-earmark-pdf-fill"></i> Cetak Surat Izin
        </a>
        @endif

        <a href="{{ route('izin.index') }}" style="display:block; text-align:center; padding:12px; background:#f1f5f9; color:#475569; border-radius:8px; font-size:13px; font-weight:700; text-decoration:none; transition:0.2s;">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
        </a>

    </div>
</div>
@endsection
