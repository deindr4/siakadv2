@extends('layouts.app')

@section('page-title', 'Jurnal Mengajar')
@section('page-subtitle', 'Rekap jurnal mengajar')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>📖 Jurnal Mengajar</h1>
    <p>{{ $isAdmin ? 'Rekap semua jurnal mengajar guru' : 'Jurnal mengajar saya' }}</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">📅</div>
        <div class="stat-info"><h3>{{ $totalBulanIni }}</h3><p>Bulan Ini</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#10b981,#059669);">📊</div>
        <div class="stat-info"><h3>{{ $totalSemester }}</h3><p>Total Semester</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">📝</div>
        <div class="stat-info"><h3>{{ $jurnals->total() }}</h3><p>Hasil Filter</p></div>
    </div>
</div>

{{-- Tombol Tambah --}}
<div style="margin-bottom:20px;">
    <a href="{{ $isAdmin ? route('admin.jurnal.create') : route('guru.jurnal.create') }}"
        class="btn btn-primary" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
        <i class="bi bi-plus-lg"></i> Isi Jurnal Hari Ini
    </a>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET">
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

                <div style="flex:1;min-width:120px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">BULAN</label>
                    <select name="bulan" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua</option>
                        @foreach(range(1,12) as $b)
                            <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($isAdmin)
                <div style="flex:1;min-width:180px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">GURU</label>
                    <select name="guru_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Guru</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div style="flex:2;min-width:160px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">CARI</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Materi, kelas..."
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ $isAdmin ? route('admin.jurnal.index') : route('guru.jurnal.index') }}"
                        class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header">
        <h3>📋 Daftar Jurnal <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $jurnals->total() }} jurnal</span></h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        @if($isAdmin)<th>Guru</th>@endif
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Pertemuan</th>
                        <th>Materi</th>
                        <th>TTD</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurnals as $i => $j)
                    <tr>
                        <td>{{ $jurnals->firstItem() + $i }}</td>
                        @if($isAdmin)
                        <td>
                            <div style="font-size:13px;font-weight:600;">{{ $j->guru?->nama }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $j->guru?->jabatan }}</div>
                        </td>
                        @endif
                        <td>
                            <div style="font-size:13px;font-weight:600;">{{ $j->tanggal?->format('d/m/Y') }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $j->tanggal?->translatedFormat('l') }}</div>
                        </td>
                        <td>
                            <div style="font-size:13px;font-weight:600;">{{ $j->mataPelajaran?->nama }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $j->jam_ke ? 'Jam ke-'.$j->jam_ke : '' }}</div>
                        </td>
                        <td style="font-size:13px;">{{ $j->nama_rombel }}</td>
                        <td style="text-align:center;">
                            <span style="font-size:16px;font-weight:800;color:#6366f1;">{{ $j->pertemuan_ke ?? '-' }}</span>
                        </td>
                        <td style="max-width:200px;">
                            <div style="font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;">
                                {{ $j->materi }}
                            </div>
                        </td>
                        <td style="text-align:center;">
                            @if($j->tanda_tangan)
                                <img src="{{ $j->tanda_tangan }}" style="height:30px;max-width:80px;object-fit:contain;">
                            @else
                                <span style="color:#94a3b8;font-size:12px;">-</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ $isAdmin ? route('admin.jurnal.show', $j) : route('guru.jurnal.show', $j) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                @if($isAdmin)
                                <form method="POST" action="{{ route('admin.jurnal.destroy', $j) }}"
                                    onsubmit="return confirm('Hapus jurnal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 9 : 8 }}" style="text-align:center;padding:50px;color:#94a3b8;">
                            <i class="bi bi-journal-x" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                            Belum ada jurnal mengajar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jurnals->hasPages())
        <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
            {{ $jurnals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
