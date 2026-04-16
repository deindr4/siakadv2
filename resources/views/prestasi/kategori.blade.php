@extends('layouts.app')
@section('page-title', 'Kategori Prestasi')
@section('sidebar-menu') @include('partials.sidebar_admin') @endsection

@section('content')
<style>
    /* Layout Utama */
    .kategori-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: start;
    }

    /* Header Mobile Friendly */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* Table Responsif */
    .table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 400px; /* Mencegah tabel terlalu menciut */
    }

    /* Alert Styling */
    .alert-custom {
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-weight: 600;
        border: 1px solid;
    }

    /* Breakpoint untuk Mobile */
    @media (max-width: 768px) {
        .kategori-wrapper {
            grid-template-columns: 1fr; /* Stack kolom jadi satu */
        }

        .page-title h1 {
            font-size: 1.5rem;
        }

        .btn-responsive {
            width: 100%; /* Tombol jadi lebar penuh di mobile jika perlu */
            justify-content: center;
        }
    }

    /* Input Styling */
    .input-field {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        font-family: inherit;
        transition: border-color 0.2s;
    }

    .input-field:focus {
        border-color: #6366f1;
    }
</style>

<div class="header-section">
    <div class="page-title" style="margin:0;"><h1>🏷️ Kategori Prestasi</h1></div>
    <a href="{{ route('prestasi.index') }}" class="btn btn-responsive" style="background:#f1f5f9;color:#374151;">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

{{-- Notifikasi --}}
@if(session('success'))
<div class="alert-custom" style="background:#dcfce7;border-color:#bbf7d0;color:#16a34a;">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-custom" style="background:#fee2e2;border-color:#fecaca;color:#dc2626;">❌ {{ session('error') }}</div>
@endif

<div class="kategori-wrapper">

    {{-- Form Tambah --}}
    <div class="card shadow-sm">
        <div class="card-header"><h3>➕ Tambah Kategori</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('prestasi.kategori.store') }}">
                @csrf
                <div style="display:flex;flex-direction:column;gap:15px;">
                    <div>
                        <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:#475569;">NAMA KATEGORI <span style="color:red">*</span></label>
                        <input type="text" name="nama" required value="{{ old('nama') }}" placeholder="cth: Debat Bahasa Inggris" class="input-field">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:#475569;">JENIS <span style="color:red">*</span></label>
                        <select name="jenis" required class="input-field" style="background:#fff;">
                            <option value="akademik">Akademik</option>
                            <option value="non_akademik">Non-Akademik</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:#475569;">WARNA BADGE</label>
                        <div style="display:flex;gap:12px;align-items:center;">
                            <input type="color" name="warna" value="{{ old('warna', '#6366f1') }}" style="width:50px;height:38px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;padding:2px;">
                            <span style="font-size:12px;color:#94a3b8;">Warna unik untuk badge kategori</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-circle-fill"></i> Tambah Kategori</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Kategori --}}
    <div class="card shadow-sm">
        <div class="card-header"><h3>📋 Daftar Kategori ({{ $kategoris->count() }})</h3></div>
        <div class="card-body" style="padding:0;">
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:12px 16px;text-align:left;font-size:12px;color:#64748b;font-weight:700;border-bottom:1px solid #f1f5f9;">Kategori</th>
                            <th style="padding:12px 16px;text-align:center;font-size:12px;color:#64748b;font-weight:700;border-bottom:1px solid #f1f5f9;">Prestasi</th>
                            <th style="padding:12px 16px;text-align:center;font-size:12px;color:#64748b;font-weight:700;border-bottom:1px solid #f1f5f9;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kategoris as $k)
                        <tr style="border-bottom:1px solid #f8fafc;">
                            <td style="padding:12px 16px;">
                                <div style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $k->warna }}22;color:{{ $k->warna }};border:1px solid {{ $k->warna }}33;">
                                    {{ $k->nama }}
                                </div>
                                <div style="font-size:10px;color:#94a3b8;margin-top:4px;text-transform:uppercase;letter-spacing:0.5px;">{{ $k->jenisLabel() }}</div>
                            </td>
                            <td style="padding:12px 16px;text-align:center;font-weight:700;color:#334155;">{{ $k->prestasi_count }}</td>
                            <td style="padding:12px 16px;">
                                <div style="display:flex;gap:6px;justify-content:center;">
                                    <button type="button" onclick="editKategori({{ $k->id }}, '{{ addslashes($k->nama) }}', '{{ $k->jenis }}', '{{ $k->warna }}')"
                                        class="btn" style="padding:6px 10px;font-size:12px;background:#fef9c3;color:#d97706;border:1px solid #fef08a;">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    @if($k->prestasi_count === 0)
                                    <form method="POST" action="{{ route('prestasi.kategori.destroy', $k) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn" style="padding:6px 10px;font-size:12px;background:#fee2e2;color:#dc2626;border:1px solid #fecaca;">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada kategori yang ditambahkan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Kategori --}}
<div id="modal-edit" style="display:none;position:fixed;inset:0;background:rgba(15, 23, 42, 0.6);z-index:1000;align-items:center;justify-content:center;padding:20px;backdrop-filter: blur(2px);">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:400px;box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:16px;font-weight:700;color:#1e293b;">Edit Kategori</h3>
            <button onclick="document.getElementById('modal-edit').style.display='none'" style="background:#f1f5f9;border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;color:#64748b;">✕</button>
        </div>
        <div style="padding:20px;">
            <form id="form-edit-kategori" method="POST">
                @csrf @method('PUT')
                <div style="display:flex;flex-direction:column;gap:15px;">
                    <div>
                        <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:#475569;">NAMA KATEGORI</label>
                        <input type="text" name="nama" id="edit-nama" required class="input-field">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:#475569;">JENIS</label>
                        <select name="jenis" id="edit-jenis" class="input-field" style="background:#fff;">
                            <option value="akademik">Akademik</option>
                            <option value="non_akademik">Non-Akademik</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:#475569;">WARNA</label>
                        <input type="color" name="warna" id="edit-warna" style="width:50px;height:38px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;padding:2px;">
                    </div>
                    <div style="display:flex;gap:10px;margin-top:10px;">
                        <button type="button" onclick="document.getElementById('modal-edit').style.display='none'" class="btn" style="flex:1;background:#f1f5f9;color:#374151;font-weight:600;">Batal</button>
                        <button type="submit" class="btn btn-primary" style="flex:1;font-weight:600;">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editKategori(id, nama, jenis, warna) {
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-jenis').value = jenis;
    document.getElementById('edit-warna').value = warna;
    document.getElementById('form-edit-kategori').action = `/prestasi/kategori/${id}`;
    document.getElementById('modal-edit').style.display = 'flex';
}

// Menutup modal jika area luar di-klik
document.getElementById('modal-edit').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endsection
