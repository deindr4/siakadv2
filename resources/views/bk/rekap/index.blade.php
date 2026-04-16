@extends('layouts.app')

@section('page-title', 'Rekap Poin Pelanggaran')
@section('page-subtitle', 'Akumulasi poin pelanggaran per siswa')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>📊 Rekap Poin Pelanggaran</h1>
    <p>Akumulasi poin pelanggaran per siswa</p>
</div>

{{-- Top 5 --}}
@if($topPelanggaran->count())
<div class="card" style="margin-bottom:24px;border-left:4px solid #ef4444;">
    <div class="card-header"><h3>🏆 Top 5 Siswa Poin Tertinggi</h3></div>
    <div class="card-body">
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            @foreach($topPelanggaran as $i => $siswa)
            @php
                $colors = ['#ef4444','#f97316','#f59e0b','#84cc16','#22c55e'];
                $color  = $colors[$i] ?? '#94a3b8';
            @endphp
            <div style="flex:1;min-width:140px;background:#f8fafc;border-radius:10px;padding:14px;text-align:center;border-top:3px solid {{ $color }};">
                <div style="font-size:20px;font-weight:800;color:{{ $color }};">{{ $i+1 }}</div>
                <div style="font-size:13px;font-weight:700;color:#374151;margin:4px 0;">{{ $siswa->nama }}</div>
                <div style="font-size:11px;color:#94a3b8;">{{ $siswa->nama_rombel }}</div>
                <div style="font-size:22px;font-weight:800;color:{{ $color }};margin-top:8px;">{{ $siswa->total_poin ?? 0 }}</div>
                <div style="font-size:11px;color:#94a3b8;">poin</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('bk.rekap.index') }}">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                <div style="flex:1;min-width:180px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">SEMESTER</label>
                    <select name="semester_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                                {{ $sem->nama }} {{ $sem->is_aktif ? '✅' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="flex:1;min-width:160px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">KELAS</label>
                    <select name="rombel" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->rombongan_belajar_id }}" {{ request('rombel') == $r->rombongan_belajar_id ? 'selected' : '' }}>
                                {{ $r->nama_rombel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="flex:2;min-width:160px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">CARI</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NISN..."
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('bk.rekap.index') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i> Reset</a>
                    <a href="{{ route('bk.rekap.index', array_merge(request()->all(), ['print'=>1])) }}"
                        class="btn" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;" target="_blank">
                        <i class="bi bi-printer-fill"></i> Cetak
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Rekap --}}
<div class="card">
    <div class="card-header">
        <h3>📊 Rekap Per Siswa <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $siswas->total() }} siswa</span></h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Total Pelanggaran</th>
                        <th>Total Poin</th>
                        <th>Level</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $i => $siswa)
                    @php
                        $poin = $siswa->total_poin ?? 0;
                        if ($poin >= 100)      { $level = '🔴 Kritis';   $levelStyle = 'background:#fee2e2;color:#dc2626;'; }
                        elseif ($poin >= 50)   { $level = '🟠 Tinggi';   $levelStyle = 'background:#fed7aa;color:#ea580c;'; }
                        elseif ($poin >= 25)   { $level = '🟡 Sedang';   $levelStyle = 'background:#fef3c7;color:#d97706;'; }
                        else                   { $level = '🟢 Rendah';   $levelStyle = 'background:#dcfce7;color:#16a34a;'; }
                    @endphp
                    <tr>
                        <td>{{ $siswas->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:600;font-size:13px;">{{ $siswa->nama }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $siswa->nisn ?? '-' }}</div>
                        </td>
                        <td style="font-size:13px;">{{ $siswa->nama_rombel ?? '-' }}</td>
                        <td style="text-align:center;">
                            <span style="font-size:18px;font-weight:700;color:#6366f1;">{{ $siswa->total_pelanggaran }}</span>
                            <span style="font-size:11px;color:#94a3b8;display:block;">pelanggaran</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-size:22px;font-weight:800;color:#ef4444;">{{ $poin }}</span>
                            <span style="font-size:11px;color:#94a3b8;display:block;">poin</span>
                        </td>
                        <td>
                            <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:600;{{ $levelStyle }}">
                                {{ $level }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.siswa.show', $siswa) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="bi bi-bar-chart" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                            Belum ada data pelanggaran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswas->hasPages())
        <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
            {{ $siswas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
