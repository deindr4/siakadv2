@extends('layouts.app')
@section('page-title', 'Data Prestasi Siswa')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
<style>
    /* Desktop vs Mobile Toggle */
    .mobile-cards { display: none; }
    .pagination-wrapper { padding: 16px 20px; border-top: 1px solid #f1f5f9; }

    @media (max-width: 768px) {
        .table-wrapper { display: none; }
        .mobile-cards { display: block; padding: 10px; }

        /* Card Styling */
        .card-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .card-row { display: flex; justify-content: space-between; margin-bottom: 8px; align-items: flex-start; }
        .card-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
        .card-value { font-size: 13px; font-weight: 600; color: #374151; }
        .card-footer {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    }
</style>

<div class="page-title">
    <h1>🏆 Prestasi Siswa</h1>
    <p>Kelola data prestasi akademik dan non-akademik</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">✅ {{ session('success') }}</div>
@endif

{{-- Header Actions --}}
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('prestasi.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill"></i> Tambah Prestasi
        </a>
        @if(auth()->user()->hasRole('admin'))
        <a href="{{ route('prestasi.kategori.index') }}" class="btn" style="background:#f1f5f9;color:#374151;">
            <i class="bi bi-tags-fill"></i> Kategori
        </a>
        <a href="{{ route('laporan.prestasi') }}" class="btn" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;">
            <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
        </a>
        @endif
    </div>
    <span style="font-size:13px;color:#94a3b8;font-weight:600;">{{ $prestasi->total() }} total data</span>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:140px;">
                <label style="font-size:10px;font-weight:800;color:#64748b;display:block;margin-bottom:4px;">SEMESTER</label>
                <select name="semester_id" style="width:100%;padding:8px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>{{ $sem->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:140px;">
                <label style="font-size:10px;font-weight:800;color:#64748b;display:block;margin-bottom:4px;">STATUS</label>
                <select name="status" style="width:100%;padding:8px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="diverifikasi" {{ request('status') === 'diverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                </select>
            </div>
            <div style="flex:2;min-width:200px;">
                <label style="font-size:10px;font-weight:800;color:#64748b;display:block;margin-bottom:4px;">CARI PRESTASI</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama lomba atau siswa..." style="width:100%;padding:8px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;">
            </div>
            <div style="display:flex;gap:5px;">
                <button type="submit" class="btn btn-primary" style="padding:8px 15px;"><i class="bi bi-search"></i></button>
                <a href="{{ route('prestasi.index') }}" class="btn" style="background:#f1f5f9;color:#475569;padding:8px 12px;"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Main Content --}}
<div class="card">
    <div class="card-body" style="padding:0;">
        {{-- Table View (Desktop) --}}
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Lomba</th>
                        <th>Kategori</th>
                        <th>Juara</th>
                        <th>Siswa</th>
                        <th>Status</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prestasi as $i => $p)
                    <tr>
                        <td>{{ $prestasi->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:700;color:#1e293b;">{{ $p->nama_lomba }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $p->tingkatLabel() }}</div>
                        </td>
                        <td>
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $p->kategori->warna ?? '#e2e8f0' }}22;color:{{ $p->kategori->warna ?? '#64748b' }};">
                                {{ $p->kategori->nama ?? '-' }}
                            </span>
                        </td>
                        <td style="font-weight:700;color:#f59e0b;">🏆 {{ $p->juara }}</td>
                        <td style="font-size:12px;">{{ $p->siswas->first()->nama ?? '-' }}</td>
                        <td>
                            <span style="padding:4px 10px;border-radius:20px;font-size:10px;font-weight:800;background:{{ $p->statusBg() }};color:{{ $p->statusColor() }};">
                                {{ strtoupper($p->statusLabel()) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('prestasi.show', $p) }}" class="btn-icon" style="color:#0284c7;background:#f0f9ff;"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('prestasi.edit', $p) }}" class="btn-icon" style="color:#d97706;background:#fef9c3;"><i class="bi bi-pencil"></i></a>
                                @if(auth()->user()->hasRole('admin'))
                                <form method="POST" action="{{ route('prestasi.destroy', $p) }}" onsubmit="return confirm('Hapus data?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon" style="color:#dc2626;background:#fee2e2;border:none;"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:50px;color:#94a3b8;">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Card View (Mobile) --}}
        <div class="mobile-cards">
            @foreach($prestasi as $p)
            <div class="card-item">
                <div class="card-row">
                    <div>
                        <div class="card-label">Lomba</div>
                        <div class="card-value">{{ $p->nama_lomba }}</div>
                    </div>
                    <span style="padding:4px 8px;border-radius:8px;font-size:10px;font-weight:800;background:{{ $p->statusBg() }};color:{{ $p->statusColor() }};">
                        {{ $p->statusLabel() }}
                    </span>
                </div>
                <div class="card-row">
                    <div>
                        <div class="card-label">Juara</div>
                        <div class="card-value" style="color:#f59e0b;">🥇 {{ $p->juara }}</div>
                    </div>
                    <div style="text-align:right">
                        <div class="card-label">Tingkat</div>
                        <div class="card-value">{{ $p->tingkatLabel() }}</div>
                    </div>
                </div>
                <div class="card-row">
                    <div>
                        <div class="card-label">Siswa</div>
                        <div class="card-value">{{ $p->siswas->first()->nama ?? '-' }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <span style="font-size:11px;color:#94a3b8;"><i class="bi bi-calendar3"></i> {{ $p->tanggal->format('d/m/Y') }}</span>
                    <div style="display:flex;gap:10px;">
                        <a href="{{ route('prestasi.show', $p) }}" style="color:#0284c7;font-size:18px;"><i class="bi bi-eye-fill"></i></a>
                        <a href="{{ route('prestasi.edit', $p) }}" style="color:#d97706;font-size:18px;"><i class="bi bi-pencil-square"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($prestasi->hasPages())
        <div class="pagination-wrapper">{{ $prestasi->links() }}</div>
        @endif
    </div>
</div>
@endsection
