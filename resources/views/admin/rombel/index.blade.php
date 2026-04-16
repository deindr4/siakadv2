@extends('layouts.app')

@section('page-title', 'Data Rombel')
@section('page-subtitle', 'Daftar rombongan belajar')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>🏠 Data Rombel</h1>
    <p>Kelas reguler, mapel pilihan, dan ekstrakurikuler</p>
</div>

{{-- Stat Cards --}}
<div class="grid grid-4" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">🏫</div>
        <div class="stat-info"><h3>{{ $countKelas }}</h3><p>Rombel Kelas</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">📚</div>
        <div class="stat-info"><h3>{{ $countMapel }}</h3><p>Mapel Pilihan</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#6366f1);">⚽</div>
        <div class="stat-info"><h3>{{ $countEkskul }}</h3><p>Ekstrakurikuler</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#10b981,#059669);">👨‍🎓</div>
        <div class="stat-info"><h3>{{ $totalSiswa }}</h3><p>Total Siswa Aktif</p></div>
    </div>
</div>

{{-- Tab Filter --}}
<div style="display:flex;gap:4px;margin-bottom:20px;background:#f1f5f9;padding:5px;border-radius:12px;width:fit-content;">
    @foreach([
        ['kelas',  '🏫 Kelas',        $countKelas],
        ['mapel',  '📚 Mapel Pilihan', $countMapel],
        ['ekskul', '⚽ Ekskul',        $countEkskul],
        ['semua',  '📋 Semua',         $countSemua],
    ] as [$key, $label, $count])
    <a href="{{ route('admin.rombel.index', array_merge(request()->except('tab','page'), ['tab'=>$key])) }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .2s;
              {{ $tab === $key ? 'background:#fff;color:#6366f1;box-shadow:0 1px 4px rgba(0,0,0,.1);' : 'color:#64748b;' }}">
        {{ $label }}
        <span style="font-size:11px;padding:2px 7px;border-radius:20px;font-weight:700;
                     {{ $tab === $key ? 'background:#ede9fe;color:#6366f1;' : 'background:#e2e8f0;color:#94a3b8;' }}">
            {{ $count }}
        </span>
    </a>
    @endforeach
</div>

{{-- Filter Form --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.rombel.index') }}">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">

                <div style="flex:1;min-width:200px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">SEMESTER</label>
                    <select name="semester_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Semester</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                                {{ $sem->nama }} {{ $sem->is_aktif ? '✅' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(in_array($tab, ['kelas', 'mapel', 'semua']))
                <div style="flex:1;min-width:140px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">TINGKAT</label>
                    <select name="tingkat" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Tingkat</option>
                        @foreach($tingkats as $t)
                            <option value="{{ $t }}" {{ request('tingkat') == $t ? 'selected' : '' }}>Kelas {{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div style="flex:2;min-width:160px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">CARI</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ $tab === 'ekskul' ? 'Nama ekstrakurikuler...' : ($tab === 'mapel' ? 'Nama mapel pilihan...' : 'Nama kelas...') }}"
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('admin.rombel.index', ['tab' => $tab]) }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header">
        <h3>
            @if($tab === 'kelas') 🏫 Rombel Kelas
            @elseif($tab === 'mapel') 📚 Mapel Pilihan
            @elseif($tab === 'ekskul') ⚽ Ekstrakurikuler
            @else 📋 Semua Rombel
            @endif
            <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $rombels->total() }} rombel</span>
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Rombel</th>
                        @if($tab === 'ekskul')
                            <th>Jenis Ekskul</th>
                            <th>Pembina</th>
                        @else
                            <th>Tingkat</th>
                            <th>Kurikulum</th>
                        @endif
                        <th>Semester</th>
                        <th>Jenis</th>
                        <th>Jumlah Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rombels as $i => $rombel)
                    <tr>
                        <td>{{ $rombels->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;flex-shrink:0;
                                    background:linear-gradient(135deg,
                                    {{ $rombel->isEkskul() ? '#8b5cf6,#6366f1' : ($rombel->isMapel() ? '#3b82f6,#1d4ed8' : '#f59e0b,#d97706') }});">
                                    {{ strtoupper(substr($rombel->nama_rombel, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13.5px;">{{ $rombel->nama_rombel }}</div>
                                    @if($rombel->rombongan_belajar_id)
                                    <div style="font-size:11px;color:#94a3b8;">ID: {{ substr($rombel->rombongan_belajar_id, 0, 8) }}...</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        @if($tab === 'ekskul')
                            <td style="font-size:12px;color:#374151;">{{ $rombel->jenis_rombel_str ?? '-' }}</td>
                            <td style="font-size:12px;color:#374151;">{{ $rombel->wali_kelas ?? '-' }}</td>
                        @else
                            <td>
                                @if($rombel->tingkat)
                                    <span class="badge badge-primary">Kelas {{ $rombel->tingkat }}</span>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">-</span>
                                @endif
                            </td>
                            <td style="font-size:13px;">{{ $rombel->kurikulum ?? '-' }}</td>
                        @endif
                        <td style="font-size:12px;color:#64748b;">{{ $rombel->semester?->nama ?? '-' }}</td>
                        <td>
                            <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:600;{{ $rombel->jenisBadgeStyle() }}">
                                {{ $rombel->jenisLabel() }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <span style="font-size:18px;font-weight:800;color:#0f172a;">{{ $rombel->jumlah_siswa_aktif ?? 0 }}</span>
                                <span style="font-size:11px;color:#94a3b8;">siswa</span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.rombel.show', $rombel) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye-fill"></i> Lihat Siswa
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:50px;color:#94a3b8;">
                            <i class="bi bi-diagram-3" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                            Belum ada data
                            @if($tab === 'ekskul') ekstrakurikuler
                            @elseif($tab === 'mapel') mapel pilihan
                            @else rombel kelas
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rombels->hasPages())
        <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
            {{ $rombels->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
