{{-- resources/views/prestasi/show.blade.php --}}
@extends('layouts.app')
@section('page-title', 'Detail Prestasi')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')

<style>
* { box-sizing:border-box; }

.form-label { font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px; }
.prestasi-layout { display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start; }
.info-grid-3 { display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px; }
.info-grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
@media (max-width:768px) {
    .prestasi-layout { grid-template-columns:1fr !important; }
    .info-grid-3 { grid-template-columns:1fr 1fr 1fr !important;gap:6px !important; }
    .info-grid-2 { grid-template-columns:1fr !important; }
    .prestasi-header { flex-direction:column !important; align-items:flex-start !important; }
    .prestasi-actions { flex-wrap:wrap; }
    /* Prevent overflow */
    .card { overflow:hidden; }
    .card-body { overflow-x:hidden; }
    h2 { word-break:break-word; }
}
</style>

{{-- Header --}}
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;" class="prestasi-header">
    <div class="page-title" style="margin:0;">
        <h1>🏆 Detail Prestasi</h1>
    </div>
    <div style="display:flex;gap:10px;" class="prestasi-actions">
        <a href="{{ route('prestasi.index') }}" class="btn" style="background:#f1f5f9;color:#374151;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        @if(auth()->user()->hasAnyRole(['admin','bk','tata_usaha']))
        <a href="{{ route('prestasi.edit', $prestasi) }}" class="btn" style="background:#fef9c3;color:#d97706;">
            <i class="bi bi-pencil-fill"></i> Edit
        </a>
        @endif
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">
    ✅ {{ session('success') }}
</div>
@endif

<div class="prestasi-layout">

    {{-- KIRI --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Info Utama --}}
        <div class="card">
            <div class="card-body">
                {{-- Judul & Status --}}
                <div style="margin-bottom:16px;">
                    <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:8px;">
                        <span style="display:block;width:fit-content;padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $prestasi->statusBg() }};color:{{ $prestasi->statusColor() }};">
                            {{ $prestasi->statusLabel() }}
                        </span>
                        <h2 style="font-size:18px;font-weight:800;color:#1e293b;line-height:1.3;word-break:break-word;white-space:normal;">{{ $prestasi->nama_lomba }}</h2>
                    </div>
                    @if($prestasi->penyelenggara)
                    <p style="font-size:13px;color:#64748b;">{{ $prestasi->penyelenggara }}</p>
                    @endif
                </div>

                {{-- Stats 3 kotak --}}
                <div class="info-grid-3">
                    <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                        <p style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Tingkat</p>
                        <p style="font-size:14px;font-weight:800;color:{{ $prestasi->tingkatColor() }};margin-top:4px;">{{ $prestasi->tingkatLabel() }}</p>
                    </div>
                    <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                        <p style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Juara</p>
                        <p style="font-size:14px;font-weight:800;color:#f59e0b;margin-top:4px;">🥇 {{ $prestasi->juara }}</p>
                    </div>
                    <div style="background:#fafafa;border-radius:10px;padding:12px;text-align:center;">
                        <p style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Tanggal</p>
                        <p style="font-size:12px;font-weight:700;color:#374151;margin-top:4px;">{{ $prestasi->tanggal->translatedFormat('d M Y') }}</p>
                    </div>
                </div>

                {{-- Detail 2 kolom --}}
                <div class="info-grid-2">
                    <div>
                        <span style="font-size:11px;color:#94a3b8;font-weight:700;">KATEGORI</span>
                        <p style="margin-top:4px;">
                            @if($prestasi->kategori)
                            <span style="padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;background:{{ $prestasi->kategori->warna }}22;color:{{ $prestasi->kategori->warna }};">
                                {{ $prestasi->kategori->nama }}
                            </span>
                            @else <span style="color:#94a3b8;">-</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span style="font-size:11px;color:#94a3b8;font-weight:700;">TIPE</span>
                        <p style="margin-top:4px;font-weight:600;font-size:13px;">
                            {{ $prestasi->tipe === 'tim' ? '👥 Tim' : '👤 Individu' }}
                            @if($prestasi->nama_tim) — {{ $prestasi->nama_tim }} @endif
                        </p>
                    </div>
                    @if($prestasi->tempat)
                    <div>
                        <span style="font-size:11px;color:#94a3b8;font-weight:700;">TEMPAT</span>
                        <p style="margin-top:4px;font-size:13px;">{{ $prestasi->tempat }}</p>
                    </div>
                    @endif
                    <div>
                        <span style="font-size:11px;color:#94a3b8;font-weight:700;">SEMESTER</span>
                        <p style="margin-top:4px;font-size:13px;">{{ $prestasi->semester?->nama ?? '-' }}</p>
                    </div>
                </div>

                @if($prestasi->keterangan)
                <div style="margin-top:14px;padding:12px;background:#f8fafc;border-radius:8px;">
                    <p style="font-size:11px;color:#94a3b8;font-weight:700;margin-bottom:4px;">KETERANGAN</p>
                    <p style="font-size:13px;line-height:1.6;">{{ $prestasi->keterangan }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Siswa --}}
        <div class="card">
            <div class="card-header"><h3>👥 Siswa yang Terlibat</h3></div>
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:400px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:10px 16px;text-align:left;font-size:12px;color:#64748b;font-weight:700;border-bottom:1px solid #f1f5f9;">#</th>
                            <th style="padding:10px 16px;text-align:left;font-size:12px;color:#64748b;font-weight:700;border-bottom:1px solid #f1f5f9;">Nama Siswa</th>
                            <th style="padding:10px 16px;text-align:left;font-size:12px;color:#64748b;font-weight:700;border-bottom:1px solid #f1f5f9;">NISN</th>
                            <th style="padding:10px 16px;text-align:left;font-size:12px;color:#64748b;font-weight:700;border-bottom:1px solid #f1f5f9;">Kelas</th>
                            @if($prestasi->tipe === 'tim')
                            <th style="padding:10px 16px;text-align:left;font-size:12px;color:#64748b;font-weight:700;border-bottom:1px solid #f1f5f9;">Peran</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prestasi->prestasiSiswa as $i => $ps)
                        <tr style="border-bottom:1px solid #f8fafc;">
                            <td style="padding:10px 16px;font-size:13px;">{{ $i+1 }}</td>
                            <td style="padding:10px 16px;font-weight:600;font-size:13px;">{{ $ps->siswa?->nama }}</td>
                            <td style="padding:10px 16px;font-size:12px;color:#64748b;">{{ $ps->siswa?->nisn ?? '-' }}</td>
                            <td style="padding:10px 16px;font-size:12px;">{{ $ps->siswa?->nama_rombel }}</td>
                            @if($prestasi->tipe === 'tim')
                            <td style="padding:10px 16px;font-size:12px;color:#6366f1;">{{ $ps->peran ?? '-' }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- KANAN --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Sertifikat --}}
        @if($prestasi->hasSertifikat())
        <div class="card">
            <div class="card-header"><h3>📎 Bukti / Sertifikat</h3></div>
            <div class="card-body" style="text-align:center;">
                @php $isImage = !str_ends_with(strtolower($prestasi->file_sertifikat), '.pdf'); @endphp
                @if($isImage)
                <a href="{{ $prestasi->sertifikatUrl() }}" target="_blank">
                    <img src="{{ $prestasi->sertifikatUrl() }}" style="width:100%;border-radius:8px;max-height:250px;object-fit:cover;">
                </a>
                @else
                <a href="{{ $prestasi->sertifikatUrl() }}" target="_blank" class="btn" style="background:#f0f9ff;color:#0284c7;width:100%;">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Lihat PDF
                </a>
                @endif
                <p style="font-size:11px;color:#94a3b8;margin-top:8px;word-break:break-all;">{{ $prestasi->file_sertifikat_original }}</p>
            </div>
        </div>
        @endif

        {{-- Verifikasi --}}
        @if(auth()->user()->hasAnyRole(['admin','bk','tata_usaha']))
            @if(in_array($prestasi->status, ['pending','ditolak']))
            <div class="card" style="border:2px solid #fde68a;">
                <div class="card-header" style="background:#fef9c3;"><h3>✅ Verifikasi</h3></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('prestasi.verifikasi', $prestasi) }}">
                        @csrf @method('PATCH')
                        <div style="margin-bottom:12px;">
                            <label class="form-label">CATATAN (opsional)</label>
                            <textarea name="catatan" rows="2"
                                style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;resize:vertical;"
                                placeholder="Catatan verifikasi..."></textarea>
                        </div>
                        <div style="display:flex;gap:10px;">
                            <button type="submit" name="status" value="diverifikasi" class="btn"
                                style="flex:1;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:12px;">
                                <i class="bi bi-check-circle-fill"></i> Verifikasi
                            </button>
                            <button type="submit" name="status" value="ditolak" class="btn"
                                style="flex:1;background:#fee2e2;color:#dc2626;font-size:12px;"
                                onclick="return confirm('Tolak prestasi ini?')">
                                <i class="bi bi-x-circle-fill"></i> Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @elseif($prestasi->status === 'diverifikasi')
            <div class="card" style="border:2px solid #bbf7d0;">
                <div class="card-body" style="text-align:center;padding:20px;">
                    <i class="bi bi-patch-check-fill" style="font-size:36px;color:#16a34a;display:block;margin-bottom:8px;"></i>
                    <p style="font-weight:700;color:#16a34a;">Telah Diverifikasi</p>
                    <p style="font-size:12px;color:#64748b;margin-top:4px;">oleh {{ $prestasi->diverifikasiOleh?->name }}</p>
                    <p style="font-size:11px;color:#94a3b8;">{{ $prestasi->diverifikasi_pada?->translatedFormat('d M Y, H:i') }}</p>
                    @if($prestasi->catatan_verifikasi)
                    <p style="font-size:12px;color:#374151;margin-top:8px;font-style:italic;">"{{ $prestasi->catatan_verifikasi }}"</p>
                    @endif
                </div>
            </div>
            @endif
        @endif

        {{-- Meta --}}
        <div class="card">
            <div class="card-header"><h3>ℹ️ Informasi</h3></div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:10px;font-size:12px;">
                    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:4px;">
                        <span style="color:#94a3b8;">Dibuat oleh</span>
                        <span style="font-weight:600;">{{ $prestasi->dibuatOleh?->name ?? '-' }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:4px;">
                        <span style="color:#94a3b8;">Role</span>
                        <span>{{ $prestasi->role_pembuat ?? '-' }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:4px;">
                        <span style="color:#94a3b8;">Dibuat pada</span>
                        <span>{{ $prestasi->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
