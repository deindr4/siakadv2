@extends('layouts.app')

@section('page-title', 'Data Guru & GTK')
@section('page-subtitle', 'Kelola data guru dan tenaga kependidikan')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>👨‍🏫 Data Guru & GTK</h1>
    <p>Guru dan Tenaga Kependidikan dari Dapodik</p>
</div>

{{-- TAB --}}
<div style="display:flex;gap:0;margin-bottom:24px;background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.04);">
    @foreach([
        ['semua', '👥 Semua GTK', $totalSemua],
        ['guru',  '👨‍🏫 Guru',     $totalGuru],
        ['tendik','🗂️ Tendik',    $totalTendik],
    ] as [$key, $label, $count])
    <a href="{{ request()->fullUrlWithQuery(['tab' => $key, 'page' => 1]) }}"
        style="flex:1;padding:14px 20px;text-align:center;font-size:13.5px;font-weight:600;text-decoration:none;border-bottom:3px solid {{ $tab === $key ? '#6366f1' : 'transparent' }};color:{{ $tab === $key ? '#6366f1' : '#64748b' }};background:{{ $tab === $key ? '#f5f3ff' : '#fff' }};transition:all .2s;">
        {{ $label }}
        <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:999px;font-size:11px;font-weight:700;margin-left:6px;background:{{ $tab === $key ? '#6366f1' : '#f1f5f9' }};color:{{ $tab === $key ? '#fff' : '#64748b' }};">
            {{ $count }}
        </span>
    </a>
    @endforeach
</div>

{{-- Filter & Search --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.guru.index') }}">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                <div style="flex:2;min-width:200px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">CARI</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, NIP, NUPTK, Jabatan..."
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
                <div style="flex:1;min-width:160px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">STATUS KEPEGAWAIAN</label>
                    <select name="status_kepegawaian" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua</option>
                        @foreach($statusKepegawaian as $sk)
                            <option value="{{ $sk }}" {{ request('status_kepegawaian') == $sk ? 'selected' : '' }}>{{ $sk }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.guru.index', ['tab' => $tab]) }}" class="btn" style="background:#f1f5f9;color:#374151;">
                        <i class="bi bi-x-lg"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header">
        <h3>
            @if($tab === 'guru') 👨‍🏫 Daftar Guru
            @elseif($tab === 'tendik') 🗂️ Daftar Tenaga Kependidikan
            @else 👥 Semua GTK
            @endif
            <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $gurus->total() }} data</span>
        </h3>
        <a href="{{ route('admin.guru.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah Manual
        </a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>NIP / NUPTK</th>
                        <th>Jabatan</th>
                        <th>Status Kepegawaian</th>
                        <th>Pend. Terakhir</th>
                        <th>Jenis</th>
                        <th>Sumber</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $i => $guru)
                    <tr>
                        <td>{{ $gurus->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,{{ $guru->jenis_kelamin === 'L' ? '#6366f1,#8b5cf6' : '#ec4899,#f43f5e' }});display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">
                                    {{ strtoupper(substr($guru->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:13.5px;">{{ $guru->nama }}</div>
                                    <div style="font-size:11px;color:#94a3b8;">{{ $guru->jk_label }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($guru->nip)
                            <div style="font-size:12px;"><span style="color:#94a3b8;">NIP:</span> {{ $guru->nip }}</div>
                            @endif
                            @if($guru->nuptk)
                            <div style="font-size:12px;"><span style="color:#94a3b8;">NUPTK:</span> {{ $guru->nuptk }}</div>
                            @endif
                            @if(!$guru->nip && !$guru->nuptk)
                            <span style="color:#94a3b8;font-size:12px;">-</span>
                            @endif
                        </td>
                        <td style="font-size:13px;">{{ $guru->jabatan ?? '-' }}</td>
                        <td>
                            @php
                                $skColors = [
                                    'PNS'  => 'badge-primary',
                                    'PPPK' => 'badge-success',
                                    'GTT'  => 'badge-warning',
                                    'PTT'  => 'badge-warning',
                                    'Honor'=> 'badge-warning',
                                ];
                                $skClass = $skColors[$guru->status_kepegawaian] ?? 'badge-primary';
                            @endphp
                            @if($guru->status_kepegawaian)
                            <span class="badge {{ $skClass }}">{{ $guru->status_kepegawaian }}</span>
                            @else
                            <span style="color:#94a3b8;font-size:12px;">-</span>
                            @endif
                        </td>
                        <td style="font-size:13px;">{{ $guru->pendidikan_terakhir ?? '-' }}</td>
                        <td>
                            @if($guru->jenis_ptk === 'Guru')
                                <span class="badge badge-primary">👨‍🏫 Guru</span>
                            @elseif($guru->jenis_ptk)
                                <span class="badge badge-warning">🗂️ Tendik</span>
                            @else
                                <span style="color:#94a3b8;font-size:12px;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($guru->sumber_data === 'dapodik')
                                <span class="badge badge-success">Dapodik</span>
                            @elseif($guru->sumber_data === 'excel')
                                <span class="badge badge-warning">Excel</span>
                            @else
                                <span class="badge badge-primary">Manual</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.guru.show', $guru) }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.guru.edit', $guru) }}" class="btn btn-sm" style="background:#ede9fe;color:#6366f1;">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('admin.guru.destroy', $guru) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete(this.closest('form'))">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:50px;color:#94a3b8;">
                            <i class="bi bi-person-workspace" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                            Belum ada data
                            @if($tab === 'guru') guru
                            @elseif($tab === 'tendik') tenaga kependidikan
                            @else GTK
                            @endif
                            @if(request('search'))
                                <br><small>Tidak ada hasil untuk "{{ request('search') }}"</small>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($gurus->hasPages())
        <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
            {{ $gurus->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
