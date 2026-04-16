@extends('layouts.app')

@section('page-title', 'Detail Jurnal')
@section('page-subtitle', $jurnal->mataPelajaran?->nama)

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
@php $isAdmin = auth()->user()->hasRole('admin'); @endphp

<div class="page-title">
    <h1>📖 Detail Jurnal Mengajar</h1>
    <p>{{ $jurnal->tanggal?->translatedFormat('l, d F Y') }}</p>
</div>

<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ $isAdmin ? route('admin.jurnal.index') : route('guru.jurnal.index') }}"
        class="btn" style="background:#f1f5f9;color:#374151;">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <button onclick="window.print()" class="btn btn-primary" style="background:linear-gradient(135deg,#10b981,#059669);">
        <i class="bi bi-printer-fill"></i> Cetak
    </button>
</div>

<div class="grid grid-2" style="align-items:start;gap:20px;">

    <div style="display:flex;flex-direction:column;gap:16px;">

        <div class="card">
            <div class="card-header"><h3>📋 Informasi Mengajar</h3></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    @foreach([
                        ['Guru', $jurnal->guru?->nama],
                        ['Mata Pelajaran', $jurnal->mataPelajaran?->nama],
                        ['Kelas', $jurnal->nama_rombel],
                        ['Tanggal', $jurnal->tanggal?->format('d/m/Y')],
                        ['Pertemuan Ke', $jurnal->pertemuan_ke],
                        ['Jam Ke', $jurnal->jam_ke],
                        ['Jam Mulai', $jurnal->jam_mulai?->format('H:i')],
                        ['Jam Selesai', $jurnal->jam_selesai?->format('H:i')],
                        ['Jumlah Hadir', $jurnal->jumlah_hadir],
                        ['Tidak Hadir', $jurnal->jumlah_tidak_hadir],
                    ] as [$label, $value])
                    <div style="background:#f8fafc;padding:10px 12px;border-radius:8px;">
                        <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ $label }}</p>
                        <p style="font-size:13px;font-weight:600;color:#374151;margin-top:2px;">{{ $value ?? '-' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($jurnal->foto_pendukung)
        <div class="card">
            <div class="card-header"><h3>📷 Foto Pendukung</h3></div>
            <div class="card-body" style="text-align:center;">
                <img src="{{ Storage::url($jurnal->foto_pendukung) }}"
                    style="max-width:100%;border-radius:8px;border:1px solid #e2e8f0;">
            </div>
        </div>
        @endif

    </div>

    <div style="display:flex;flex-direction:column;gap:16px;">

        <div class="card">
            <div class="card-header"><h3>📖 Isi Jurnal</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <p style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">MATERI / KD</p>
                        <div style="background:#f8fafc;padding:12px;border-radius:8px;font-size:13px;line-height:1.6;">{{ $jurnal->materi }}</div>
                    </div>
                    <div>
                        <p style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">KEGIATAN PEMBELAJARAN</p>
                        <div style="background:#f8fafc;padding:12px;border-radius:8px;font-size:13px;line-height:1.6;">{{ $jurnal->kegiatan }}</div>
                    </div>
                    @if($jurnal->catatan)
                    <div>
                        <p style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">CATATAN</p>
                        <div style="background:#fef3c7;padding:12px;border-radius:8px;font-size:13px;line-height:1.6;">{{ $jurnal->catatan }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>✍️ Tanda Tangan Guru</h3></div>
            <div class="card-body" style="text-align:center;">
                @if($jurnal->tanda_tangan)
                    <div style="border:1px solid #e2e8f0;border-radius:8px;padding:16px;background:#fafafa;display:inline-block;">
                        <img src="{{ $jurnal->tanda_tangan }}" style="max-height:120px;max-width:300px;">
                    </div>
                    <p style="font-size:13px;font-weight:600;color:#374151;margin-top:8px;">{{ $jurnal->guru?->nama }}</p>
                    <p style="font-size:11px;color:#94a3b8;">{{ $jurnal->guru?->nip ? 'NIP: '.$jurnal->guru?->nip : '' }}</p>
                @else
                    <p style="color:#94a3b8;font-size:13px;">Belum ada tanda tangan</p>
                @endif
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    .sidebar, .page-title, a.btn, button { display: none !important; }
    .card { break-inside: avoid; }
}
</style>
@endsection
