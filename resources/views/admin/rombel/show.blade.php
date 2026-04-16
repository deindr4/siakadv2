@extends('layouts.app')

@section('page-title', 'Detail Rombel')
@section('page-subtitle', $rombel->nama_rombel)

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>🏠 {{ $rombel->nama_rombel }}</h1>
    <p>Daftar siswa dalam rombel ini</p>
</div>

{{-- Info Rombel + Stat --}}
<div class="grid grid-2" style="margin-bottom:24px;">

    <div class="card">
        <div class="card-header"><h3>📋 Info Rombel</h3></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                @foreach([
                    ['Nama Rombel', $rombel->nama_rombel],
                    ['Tingkat', $rombel->tingkat ? 'Kelas '.$rombel->tingkat : '-'],
                    ['Kurikulum', $rombel->kurikulum],
                    ['Semester', $rombel->semester?->nama],
                ] as [$label, $value])
                <div style="background:#f8fafc;padding:12px;border-radius:8px;">
                    <p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.6px;">{{ $label }}</p>
                    <p style="font-size:13px;font-weight:600;color:#374151;margin-top:3px;">{{ $value ?? '-' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:12px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">👦</div>
            <div class="stat-info">
                <h3>{{ $totalL }}</h3>
                <p>Siswa Laki-laki</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#ec4899,#f43f5e);">👧</div>
            <div class="stat-info">
                <h3>{{ $totalP }}</h3>
                <p>Siswa Perempuan</p>
            </div>
        </div>
    </div>

</div>

{{-- Filter Siswa --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                <div style="flex:2;min-width:200px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">CARI SISWA</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama atau NISN..."
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
                <div style="flex:1;min-width:140px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">JENIS KELAMIN</label>
                    <select name="jenis_kelamin" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua</option>
                        <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('admin.rombel.show', $rombel) }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i> Reset</a>
                    <a href="{{ route('admin.rombel.index') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Siswa --}}
<div class="card">
    <div class="card-header">
        <h3>👨‍🎓 Daftar Siswa <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $siswas->total() }} siswa</span></h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Jenis Kelamin</th>
                        <th>Tempat, Tgl Lahir</th>
                        <th>Agama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $i => $siswa)
                    <tr>
                        <td>{{ $siswas->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,{{ $siswa->jenis_kelamin === 'L' ? '#6366f1,#8b5cf6' : '#ec4899,#f43f5e' }});display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;flex-shrink:0;">
                                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                </div>
                                <div style="font-weight:600;font-size:13px;">{{ $siswa->nama }}</div>
                            </div>
                        </td>
                        <td style="font-size:13px;font-family:monospace;">{{ $siswa->nisn ?? '-' }}</td>
                        <td>
                            @if($siswa->jenis_kelamin === 'L')
                                <span class="badge badge-primary">👦 L</span>
                            @else
                                <span class="badge" style="background:#fce7f3;color:#be185d;">👧 P</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#64748b;">
                            {{ $siswa->tempat_lahir ?? '-' }},
                            {{ $siswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td style="font-size:13px;">{{ $siswa->agama ?? '-' }}</td>
                        <td>
                            <a href="#" class="btn btn-sm" style="background:#f1f5f9;color:#374151;">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:8px;"></i>
                            Belum ada siswa di rombel ini
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
