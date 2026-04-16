@extends('layouts.app')

@section('page-title', 'Jenis Pelanggaran')
@section('page-subtitle', 'Master data jenis pelanggaran dan poin')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>📋 Jenis Pelanggaran</h1>
    <p>Master data jenis pelanggaran dan poin</p>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#16a34a;font-weight:600;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">🟡</div>
        <div class="stat-info"><h3>{{ $totalRingan }}</h3><p>Pelanggaran Ringan</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#f97316,#ea580c);">🟠</div>
        <div class="stat-info"><h3>{{ $totalSedang }}</h3><p>Pelanggaran Sedang</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626);">🔴</div>
        <div class="stat-info"><h3>{{ $totalBerat }}</h3><p>Pelanggaran Berat</p></div>
    </div>
</div>

<div class="grid grid-2" style="align-items:start;gap:20px;">

    {{-- Form Tambah --}}
    <div class="card">
        <div class="card-header"><h3>➕ Tambah Jenis Pelanggaran</h3></div>
        <div class="card-body">
            @if($errors->any())
            <div style="background:#fee2e2;border-radius:8px;padding:12px;margin-bottom:14px;font-size:13px;color:#dc2626;">
                @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('bk.jenis-pelanggaran.store') }}">
                @csrf
                <div style="display:flex;flex-direction:column;gap:14px;">

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KODE <span style="color:red">*</span></label>
                            <input type="text" name="kode" value="{{ old('kode') }}" placeholder="P001"
                                style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KATEGORI <span style="color:red">*</span></label>
                            <select name="kategori" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                                <option value="ringan" {{ old('kategori') == 'ringan' ? 'selected' : '' }}>🟡 Ringan</option>
                                <option value="sedang" {{ old('kategori') == 'sedang' ? 'selected' : '' }}>🟠 Sedang</option>
                                <option value="berat"  {{ old('kategori') == 'berat'  ? 'selected' : '' }}>🔴 Berat</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA PELANGGARAN <span style="color:red">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Terlambat masuk kelas"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">POIN <span style="color:red">*</span></label>
                        <input type="number" name="poin" value="{{ old('poin') }}" min="1" placeholder="5"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">DESKRIPSI</label>
                        <textarea name="deskripsi" rows="2" placeholder="Keterangan tambahan..."
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;resize:vertical;">{{ old('deskripsi') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Jenis Pelanggaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card">
        <div class="card-header">
            <h3>📋 Daftar Jenis Pelanggaran</h3>
        </div>
        <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9;">
            <form method="GET">
                <div style="display:flex;gap:8px;">
                    <select name="kategori" onchange="this.form.submit()" style="padding:7px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">Semua Kategori</option>
                        <option value="ringan" {{ request('kategori') == 'ringan' ? 'selected' : '' }}>🟡 Ringan</option>
                        <option value="sedang" {{ request('kategori') == 'sedang' ? 'selected' : '' }}>🟠 Sedang</option>
                        <option value="berat"  {{ request('kategori') == 'berat'  ? 'selected' : '' }}>🔴 Berat</option>
                    </select>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                        style="flex:1;padding:7px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Poin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenis as $j)
                        <tr>
                            <td style="font-family:monospace;font-weight:700;font-size:13px;">{{ $j->kode }}</td>
                            <td>
                                <div style="font-size:13px;font-weight:600;">{{ $j->nama }}</div>
                                @if($j->deskripsi)
                                <div style="font-size:11px;color:#94a3b8;">{{ $j->deskripsi }}</div>
                                @endif
                            </td>
                            <td>
                                <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:600;{{ $j->kategoriBadgeStyle() }}">
                                    {{ $j->kategoriLabel() }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:16px;font-weight:800;color:#ef4444;">{{ $j->poin }}</span>
                                <span style="font-size:11px;color:#94a3b8;">poin</span>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    {{-- Edit Modal Trigger --}}
                                    <button onclick="editJenis({{ $j->id }}, '{{ $j->kode }}', '{{ addslashes($j->nama) }}', '{{ $j->kategori }}', {{ $j->poin }}, '{{ addslashes($j->deskripsi ?? '') }}')"
                                        class="btn btn-sm" style="background:#fef3c7;color:#d97706;">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form method="POST" action="{{ route('bk.jenis-pelanggaran.destroy', $j) }}"
                                        onsubmit="return confirm('Hapus jenis pelanggaran ini?')">
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
                            <td colspan="5" style="text-align:center;padding:40px;color:#94a3b8;">
                                Belum ada jenis pelanggaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($jenis->hasPages())
            <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
                {{ $jenis->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;">✏️ Edit Jenis Pelanggaran</h3>
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
                            <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">KATEGORI</label>
                            <select name="kategori" id="edit-kategori" style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                                <option value="ringan">🟡 Ringan</option>
                                <option value="sedang">🟠 Sedang</option>
                                <option value="berat">🔴 Berat</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">NAMA</label>
                        <input type="text" name="nama" id="edit-nama"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">POIN</label>
                        <input type="number" name="poin" id="edit-poin" min="1"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">DESKRIPSI</label>
                        <textarea name="deskripsi" id="edit-deskripsi" rows="2"
                            style="width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;resize:vertical;"></textarea>
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" onclick="document.getElementById('modal-edit').style.display='none'"
                            class="btn" style="background:#f1f5f9;color:#374151;">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editJenis(id, kode, nama, kategori, poin, deskripsi) {
    document.getElementById('edit-kode').value     = kode;
    document.getElementById('edit-nama').value     = nama;
    document.getElementById('edit-kategori').value = kategori;
    document.getElementById('edit-poin').value     = poin;
    document.getElementById('edit-deskripsi').value= deskripsi;
    document.getElementById('form-edit').action    = '/bk/jenis-pelanggaran/' + id;
    document.getElementById('modal-edit').style.display = 'flex';
}
document.getElementById('modal-edit').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endsection
