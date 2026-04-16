@extends('layouts.app')

@section('page-title', 'Generate Akun')
@section('page-subtitle', 'Buat akun login untuk guru dan siswa')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>🔑 Generate Akun Login</h1>
    <p>Buat akun login untuk guru dan siswa</p>
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

{{-- Stat Cards --}}
<div class="grid grid-4" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">👨‍🏫</div>
        <div class="stat-info"><h3>{{ $totalGuruAkunAktif }}</h3><p>Akun Guru Aktif</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">⚠️</div>
        <div class="stat-info"><h3>{{ $totalGuruBelumAkun }}</h3><p>Guru Belum Akun</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#10b981,#059669);">👨‍🎓</div>
        <div class="stat-info"><h3>{{ $totalSiswaAkunAktif }}</h3><p>Akun Siswa Aktif</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626);">⚠️</div>
        <div class="stat-info"><h3>{{ $totalSiswaBelumAkun }}</h3><p>Siswa Belum Akun</p></div>
    </div>
</div>

{{-- Tab --}}
<div style="display:flex;gap:4px;margin-bottom:20px;background:#f1f5f9;padding:5px;border-radius:12px;width:fit-content;">
    @foreach([['siswa','👨‍🎓 Siswa'],['guru','👨‍🏫 Guru & GTK']] as [$key,$label])
    <a href="{{ route('admin.akun.index', array_merge(request()->except('tab','page'), ['tab'=>$key])) }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 20px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .2s;
              {{ $tab === $key ? 'background:#fff;color:#6366f1;box-shadow:0 1px 4px rgba(0,0,0,.1);' : 'color:#64748b;' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

{{-- TAB SISWA --}}
@if($tab === 'siswa')

{{-- Info default --}}
<div style="background:#dbeafe;border:1px solid #bfdbfe;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:13px;color:#1d4ed8;">
    ℹ️ <strong>Username:</strong> NISN siswa &nbsp;|&nbsp; <strong>Password default:</strong> 12345678
</div>

{{-- Generate Massal --}}
<div class="card" style="margin-bottom:20px;border-left:4px solid #6366f1;">
    <div class="card-header"><h3>⚡ Generate Massal per Kelas</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.akun.siswa.massal') }}">
            @csrf
            <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-start;">

                <div style="min-width:200px;flex:1;">
                    <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">SEMESTER</label>
                    <select name="semester_id" required style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $semesterAktif?->id == $sem->id ? 'selected' : '' }}>
                                {{ $sem->nama }} {{ $sem->is_aktif ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="flex:2;min-width:280px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <label style="font-size:12px;font-weight:700;color:#374151;">PILIH KELAS</label>
                        <div style="display:flex;gap:8px;">
                            <button type="button" onclick="toggleAllKelas(true)"
                                style="font-size:11px;color:#6366f1;background:#eef2ff;border:none;padding:3px 10px;border-radius:6px;cursor:pointer;font-weight:600;">
                                ✅ Pilih Semua
                            </button>
                            <button type="button" onclick="toggleAllKelas(false)"
                                style="font-size:11px;color:#64748b;background:#f1f5f9;border:none;padding:3px 10px;border-radius:6px;cursor:pointer;font-weight:600;">
                                ✕ Batal
                            </button>
                        </div>
                    </div>
                    <div id="kelasCounter" style="font-size:12px;color:#6366f1;font-weight:600;margin-bottom:6px;">0 kelas dipilih</div>
                    <div style="border:1.5px solid #e2e8f0;border-radius:10px;padding:12px;max-height:200px;overflow-y:auto;background:#fafafa;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                            @forelse($rombels as $r)
                            <label style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:500;border:1.5px solid #e2e8f0;background:#fff;transition:all .15s;">
                                <input type="checkbox" name="rombel_ids[]" value="{{ $r->id }}"
                                    class="kelas-checkbox"
                                    style="accent-color:#6366f1;width:15px;height:15px;flex-shrink:0;"
                                    onchange="updateKelasStyle(this)">
                                <div style="min-width:0;">
                                    <div style="font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $r->nama_rombel }}</div>
                                    <div style="font-size:10px;color:#94a3b8;">Kelas {{ $r->tingkat }}</div>
                                </div>
                            </label>
                            @empty
                            <p style="font-size:13px;color:#94a3b8;grid-column:1/-1;text-align:center;padding:12px;">Tidak ada rombel</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div style="align-self:flex-end;">
                    <button type="submit" class="btn btn-primary"
                        onclick="return confirm('Generate akun untuk semua siswa di kelas yang dipilih?')"
                        style="background:linear-gradient(135deg,#6366f1,#8b5cf6);white-space:nowrap;">
                        <i class="bi bi-lightning-fill"></i> Generate Massal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAllKelas(check) {
    document.querySelectorAll('.kelas-checkbox').forEach(cb => {
        cb.checked = check;
        updateKelasStyle(cb);
    });
}
function updateKelasStyle(cb) {
    const label = cb.closest('label');
    if (cb.checked) {
        label.style.borderColor = '#6366f1';
        label.style.background  = '#eef2ff';
    } else {
        label.style.borderColor = '#e2e8f0';
        label.style.background  = '#fff';
    }
    const count = document.querySelectorAll('.kelas-checkbox:checked').length;
    document.getElementById('kelasCounter').textContent = count + ' kelas dipilih';
}
</script>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.akun.index') }}">
            <input type="hidden" name="tab" value="siswa">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                <div style="flex:1;min-width:180px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">ROMBEL</label>
                    <select name="rombel" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Rombel</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->rombongan_belajar_id }}" {{ request('rombel') == $r->rombongan_belajar_id ? 'selected' : '' }}>
                                {{ $r->nama_rombel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="flex:1;min-width:140px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">STATUS AKUN</label>
                    <select name="status_akun" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua</option>
                        <option value="sudah" {{ request('status_akun') == 'sudah' ? 'selected' : '' }}>✅ Sudah Ada Akun</option>
                        <option value="belum" {{ request('status_akun') == 'belum' ? 'selected' : '' }}>❌ Belum Ada Akun</option>
                    </select>
                </div>
                <div style="flex:2;min-width:160px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">CARI</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NISN..."
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('admin.akun.index', ['tab'=>'siswa']) }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i> Reset</a>
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
                        <th>Kelas</th>
                        <th>Status Akun</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $i => $siswa)
                    <tr>
                        <td>{{ $siswas->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:600;font-size:13px;">{{ $siswa->nama }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $siswa->jenis_kelamin === 'L' ? '👦' : '👧' }}</div>
                        </td>
                        <td style="font-family:monospace;font-size:13px;">{{ $siswa->nisn ?? '-' }}</td>
                        <td style="font-size:13px;">{{ $siswa->nama_rombel ?? '-' }}</td>
                        <td>
                            @if($siswa->user_id)
                                <span class="badge badge-success">✅ Sudah</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#dc2626;">❌ Belum</span>
                            @endif
                        </td>
                        <td style="font-family:monospace;font-size:12px;color:#64748b;">
                            {{ $siswa->user_id ? $siswa->nisn : '-' }}
                        </td>
                        <td>
                            @if(!$siswa->user_id)
                                @if($siswa->nisn)
                                <form method="POST" action="{{ route('admin.akun.siswa.single') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                    <button type="submit" class="btn btn-sm btn-primary" title="Generate Akun">
                                        <i class="bi bi-person-plus-fill"></i>
                                    </button>
                                </form>
                                @else
                                <span style="font-size:11px;color:#f59e0b;">⚠️ No NISN</span>
                                @endif
                            @else
                                {{-- Reset Password --}}
                                <form method="POST" action="{{ route('admin.akun.reset.password') }}" style="display:inline;"
                                    onsubmit="return confirm('Reset password {{ $siswa->nama }} ke default?')">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $siswa->user_id }}">
                                    <input type="hidden" name="tipe" value="siswa">
                                    <button type="submit" class="btn btn-sm" style="background:#fef3c7;color:#d97706;" title="Reset Password">
                                        <i class="bi bi-key-fill"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">Tidak ada data siswa</td>
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

@endif

{{-- TAB GURU --}}
@if($tab === 'guru')

{{-- Info default --}}
<div style="background:#dbeafe;border:1px solid #bfdbfe;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:13px;color:#1d4ed8;">
    ℹ️ <strong>Username:</strong> NIK (jika ada) atau NIP &nbsp;|&nbsp; <strong>Password default:</strong> guruku@1234
</div>

{{-- Generate Massal Guru --}}
<div class="card" style="margin-bottom:20px;border-left:4px solid #8b5cf6;">
    <div class="card-header"><h3>⚡ Generate Massal Semua Guru</h3></div>
    <div class="card-body">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <p style="font-size:13px;color:#64748b;">
                Generate akun untuk <strong>{{ $totalGuruBelumAkun }} guru</strong> yang belum memiliki akun login.
            </p>
            <form method="POST" action="{{ route('admin.akun.guru.massal') }}">
                @csrf
                <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Generate akun untuk semua guru yang belum memiliki akun?')"
                    style="background:linear-gradient(135deg,#8b5cf6,#6366f1);">
                    <i class="bi bi-lightning-fill"></i> Generate Semua Guru
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Filter Guru --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.akun.index') }}">
            <input type="hidden" name="tab" value="guru">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                <div style="flex:1;min-width:140px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">STATUS AKUN</label>
                    <select name="status_akun" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua</option>
                        <option value="sudah" {{ request('status_akun') == 'sudah' ? 'selected' : '' }}>✅ Sudah Ada Akun</option>
                        <option value="belum" {{ request('status_akun') == 'belum' ? 'selected' : '' }}>❌ Belum Ada Akun</option>
                    </select>
                </div>
                <div style="flex:2;min-width:160px;">
                    <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">CARI</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, NIP, NUPTK..."
                        style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('admin.akun.index', ['tab'=>'guru']) }}" class="btn" style="background:#f1f5f9;color:#374151;"><i class="bi bi-x-lg"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Guru --}}
<div class="card">
    <div class="card-header">
        <h3>👨‍🏫 Daftar Guru & GTK <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $gurus->total() }} guru</span></h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Guru</th>
                        <th>NIK / NIP</th>
                        <th>Jabatan</th>
                        <th>Status Akun</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $i => $guru)
                    @php $username = $guru->nik ?? $guru->nip; @endphp
                    <tr>
                        <td>{{ $gurus->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:600;font-size:13px;">{{ $guru->nama }}</div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $guru->jenis_ptk ?? '-' }}</div>
                        </td>
                        <td style="font-family:monospace;font-size:12px;">
                            <div>NIK: {{ $guru->nik ?? '-' }}</div>
                            <div style="color:#94a3b8;">NIP: {{ $guru->nip ?? '-' }}</div>
                        </td>
                        <td style="font-size:13px;">{{ $guru->jabatan ?? '-' }}</td>
                        <td>
                            @if($guru->user_id)
                                <span class="badge badge-success">✅ Sudah</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#dc2626;">❌ Belum</span>
                            @endif
                        </td>
                        <td style="font-family:monospace;font-size:12px;color:#64748b;">
                            {{ $guru->user_id ? ($username ?? '-') : '-' }}
                        </td>
                        <td>
                            @if(!$guru->user_id)
                                @if($username)
                                <form method="POST" action="{{ route('admin.akun.guru.single') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="guru_id" value="{{ $guru->id }}">
                                    <button type="submit" class="btn btn-sm btn-primary" title="Generate Akun">
                                        <i class="bi bi-person-plus-fill"></i>
                                    </button>
                                </form>
                                @else
                                <span style="font-size:11px;color:#f59e0b;">⚠️ No NIK/NIP</span>
                                @endif
                            @else
                                <form method="POST" action="{{ route('admin.akun.reset.password') }}" style="display:inline;"
                                    onsubmit="return confirm('Reset password {{ $guru->nama }} ke default?')">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $guru->user_id }}">
                                    <input type="hidden" name="tipe" value="guru">
                                    <button type="submit" class="btn btn-sm" style="background:#fef3c7;color:#d97706;" title="Reset Password">
                                        <i class="bi bi-key-fill"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">Tidak ada data guru</td>
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

@endif
@endsection
