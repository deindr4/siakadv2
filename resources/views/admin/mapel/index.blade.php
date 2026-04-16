{{-- resources/views/admin/mapel/index.blade.php --}}

@extends('layouts.app')

@section('page-title', 'Mata Pelajaran')
@section('page-subtitle', 'Master data mata pelajaran')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>📚 Mata Pelajaran</h1>
    <p>Master data mata pelajaran untuk jurnal mengajar</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">
    ✅ {{ session('success') }}
</div>
@endif

<div class="grid grid-2" style="align-items:start;gap:20px;">

    {{-- Form Tambah --}}
    <div class="card">
        <div class="card-header"><h3>➕ Tambah Mata Pelajaran</h3></div>
        <div class="card-body">
            @if($errors->any())
            <div style="background:#fee2e2;border-radius:8px;padding:12px;margin-bottom:14px;font-size:13px;color:#dc2626;">
                @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('admin.mapel.store') }}">
                @csrf
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KODE <span style="color:red">*</span></label>
                            <input type="text" name="kode" value="{{ old('kode') }}" placeholder="MTK"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">JAM/MINGGU</label>
                            <input type="number" name="jam_per_minggu" value="{{ old('jam_per_minggu', 2) }}" min="1"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA MATA PELAJARAN <span style="color:red">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Matematika"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KELOMPOK</label>
                            <select name="kelompok" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                                <option value="">Pilih...</option>
                                @foreach(['Umum','Kejuruan','Muatan Lokal','Pengembangan Diri'] as $k)
                                    <option value="{{ $k }}" {{ old('kelompok') == $k ? 'selected' : '' }}>{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TINGKAT</label>
                            <input type="text" name="tingkat" value="{{ old('tingkat') }}" placeholder="7,8,9 / Semua"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Mata Pelajaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card">
        <div class="card-header">
            <h3>📚 Daftar Mata Pelajaran
                <span style="font-size:13px;font-weight:400;color:#94a3b8;margin-left:8px;">{{ $total }} mapel</span>
            </h3>
        </div>
        <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9;">
            <form method="GET" style="display:flex;gap:8px;">
                <select name="kelompok" onchange="this.form.submit()" style="padding:7px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                    <option value="">Semua Kelompok</option>
                    @foreach($kelompoks as $k)
                        <option value="{{ $k }}" {{ request('kelompok') == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mapel..."
                    style="flex:1;padding:7px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kelompok</th>
                            <th>Jam/Minggu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mapels as $m)
                        <tr>
                            <td style="font-family:monospace;font-weight:700;font-size:13px;">{{ $m->kode }}</td>
                            <td>
                                <div style="font-size:13px;font-weight:600;">{{ $m->nama }}</div>
                                @if($m->tingkat)
                                <div style="font-size:11px;color:#94a3b8;">Kelas {{ $m->tingkat }}</div>
                                @endif
                            </td>
                            <td>
                                @if($m->kelompok)
                                <span style="font-size:11px;padding:3px 10px;border-radius:20px;background:#dbeafe;color:#1d4ed8;font-weight:600;">
                                    {{ $m->kelompok }}
                                </span>
                                @else
                                <span style="color:#94a3b8;font-size:12px;">-</span>
                                @endif
                            </td>
                            <td style="text-align:center;font-weight:700;">{{ $m->jam_per_minggu }} jam</td>
                            <td>
                                @if($m->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge" style="background:#f1f5f9;color:#94a3b8;">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <button onclick="editMapel({{ $m->id }}, '{{ $m->kode }}', '{{ addslashes($m->nama) }}', '{{ $m->kelompok }}', '{{ $m->tingkat }}', {{ $m->jam_per_minggu }}, {{ $m->is_active ? 1 : 0 }})"
                                        class="btn btn-sm" style="background:#fef3c7;color:#d97706;">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.mapel.destroy', $m) }}"
                                        onsubmit="return confirm('Hapus mata pelajaran ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                                Belum ada mata pelajaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($mapels->hasPages())
            <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
                {{ $mapels->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;">✏️ Edit Mata Pelajaran</h3>
            <button onclick="document.getElementById('modal-edit').style.display='none'"
                style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <div style="padding:24px;">
            <form method="POST" id="form-edit">
                @csrf @method('PUT')
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KODE</label>
                            <input type="text" name="kode" id="edit-kode"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">JAM/MINGGU</label>
                            <input type="number" name="jam_per_minggu" id="edit-jam"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA</label>
                        <input type="text" name="nama" id="edit-nama"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KELOMPOK</label>
                            <select name="kelompok" id="edit-kelompok" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                                <option value="">Pilih...</option>
                                @foreach(['Umum','Kejuruan','Muatan Lokal','Pengembangan Diri'] as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">TINGKAT</label>
                            <input type="text" name="tingkat" id="edit-tingkat"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">STATUS</label>
                        <select name="is_active" id="edit-status" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" onclick="document.getElementById('modal-edit').style.display='none'"
                            class="btn" style="background:#f1f5f9;color:#374151;">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editMapel(id, kode, nama, kelompok, tingkat, jam, isActive) {
    document.getElementById('edit-kode').value     = kode;
    document.getElementById('edit-nama').value     = nama;
    document.getElementById('edit-kelompok').value = kelompok;
    document.getElementById('edit-tingkat').value  = tingkat;
    document.getElementById('edit-jam').value      = jam;
    document.getElementById('edit-status').value   = isActive;
    document.getElementById('form-edit').action    = '/admin/mapel/' + id;
    document.getElementById('modal-edit').style.display = 'flex';
}
document.getElementById('modal-edit').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endsection
