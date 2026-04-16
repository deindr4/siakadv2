@extends('layouts.app')

@section('page-title', 'Absensi Harian')
@section('page-subtitle', 'Kelola absensi harian siswa per kelas')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>📋 Absensi Harian</h1>
    <p>Hari ini: <strong>{{ now()->translatedFormat('l, d F Y') }}</strong></p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">
    ✅ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#dc2626;font-weight:600;">
    ❌ {{ session('error') }}
</div>
@endif

{{-- Tombol Absen Hari Ini --}}
<div class="card" style="margin-bottom:20px;border-left:4px solid #6366f1;">
    <div class="card-header"><h3>📅 Absensi Hari Ini</h3></div>
    <div class="card-body">
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @foreach($rombels as $r)
            @php $sudahAbsen = in_array($r->id, $absensiHariIni); @endphp
            <a href="{{ route(($isAdmin ? 'admin' : 'guru').'.absensi.create', ['rombel_id' => $r->id, 'tanggal' => date('Y-m-d')]) }}"
                style="padding:10px 16px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:6px;
                {{ $sudahAbsen ? 'background:#dcfce7;color:#16a34a;border:1.5px solid #bbf7d0;' : 'background:#f0f9ff;color:#0369a1;border:1.5px solid #bae6fd;' }}">
                {{ $sudahAbsen ? '✅' : '📝' }} {{ $r->nama_rombel }}
            </a>
            @endforeach
        </div>
        <p style="font-size:11px;color:#94a3b8;margin-top:10px;">
            ✅ = sudah diabsen &nbsp;|&nbsp; 📝 = belum diabsen
        </p>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:180px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">SEMESTER</label>
                <select name="semester_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                            {{ $sem->nama }} {{ $sem->is_aktif ? '✅' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:160px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">KELAS</label>
                <select name="rombel_id" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">Semua Kelas</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:120px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">BULAN</label>
                <select name="bulan" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">Semua</option>
                    @foreach(range(1,12) as $b)
                        <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:140px;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:4px;">TANGGAL</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route(($isAdmin ? 'admin' : 'guru').'.absensi.index') }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header">
        <h3>📊 Riwayat Absensi
            <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $absensi->total() }} data</span>
        </h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Diabsen Oleh</th>
                        <th>Jam</th>
                        <th style="text-align:center;color:#16a34a;">H</th>
                        <th style="text-align:center;color:#0284c7;">S</th>
                        <th style="text-align:center;color:#d97706;">I</th>
                        <th style="text-align:center;color:#dc2626;">A</th>
                        <th style="text-align:center;color:#7c3aed;">D</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $i => $a)
                    <tr>
                        <td>{{ $absensi->firstItem() + $i }}</td>
                        <td>
                            <div style="font-size:13px;font-weight:600;">{{ $a->tanggal?->format('d/m/Y') }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $a->tanggal?->translatedFormat('l') }}</div>
                        </td>
                        <td style="font-size:13px;font-weight:600;">{{ $a->nama_rombel }}</td>
                        <td>
                            <div style="font-size:13px;">{{ $a->nama_guru ?? '-' }}</div>
                            @if($a->ip_address)
                            <div style="font-size:11px;color:#94a3b8;">{{ $a->ip_address }}</div>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#64748b;">
                            {{ $a->diabsen_pada?->format('H:i') ?? '-' }}
                        </td>
                        <td style="text-align:center;font-weight:700;color:#16a34a;">{{ $a->hadir_count }}</td>
                        <td style="text-align:center;font-weight:700;color:#0284c7;">{{ $a->sakit_count }}</td>
                        <td style="text-align:center;font-weight:700;color:#d97706;">{{ $a->izin_count }}</td>
                        <td style="text-align:center;font-weight:700;color:#dc2626;">{{ $a->alpa_count }}</td>
                        <td style="text-align:center;font-weight:700;color:#7c3aed;">{{ $a->dispensasi_count }}</td>
                        <td>
                            @if($a->is_locked)
                                <span style="font-size:11px;padding:3px 10px;border-radius:20px;background:#fee2e2;color:#dc2626;font-weight:600;">🔒 Terkunci</span>
                            @else
                                <span style="font-size:11px;padding:3px 10px;border-radius:20px;background:#dcfce7;color:#16a34a;font-weight:600;">🔓 Terbuka</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route(($isAdmin ? 'admin' : 'guru').'.absensi.show', $a->id) }}"
                                    class="btn btn-sm btn-primary" title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                @if($isAdmin)
                                <a href="{{ route('admin.absensi.edit', $a->id) }}"
                                    class="btn btn-sm" style="background:#fef3c7;color:#d97706;" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.absensi.lock', $a->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm"
                                        style="background:{{ $a->is_locked ? '#dcfce7' : '#fee2e2' }};color:{{ $a->is_locked ? '#16a34a' : '#dc2626' }};"
                                        title="{{ $a->is_locked ? 'Buka Kunci' : 'Kunci' }}">
                                        <i class="bi bi-{{ $a->is_locked ? 'unlock-fill' : 'lock-fill' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.absensi.destroy', $a->id) }}" style="display:inline;"
                                    onsubmit="return confirm('Hapus absensi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" style="text-align:center;padding:50px;color:#94a3b8;">
                            <i class="bi bi-clipboard-x" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                            Belum ada data absensi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($absensi->hasPages())
        <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
            {{ $absensi->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
