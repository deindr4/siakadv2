{{-- resources/views/admin/semester/_tambah_manual.blade.php --}}

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#16a34a;font-weight:600;font-size:13px;">
    ✅ {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#dc2626;font-weight:600;font-size:13px;">
    ❌ {{ session('error') }}
</div>
@endif

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#dc2626;font-size:13px;">
    @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
</div>
@endif

{{-- Card Tambah Semester Manual --}}
<div class="card" style="margin-bottom:20px;border-left:4px solid #16a34a;">
    <div class="card-header" style="cursor:pointer;" onclick="toggleTambahSemester()">
        <h3><i class="bi bi-plus-circle-fill" style="color:#16a34a;margin-right:8px;"></i>Tambah Semester Manual</h3>
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:11px;background:#dcfce7;color:#16a34a;padding:2px 10px;border-radius:20px;font-weight:600;">
                Tanpa Dapodik
            </span>
            <i class="bi bi-chevron-down" id="icon-tambah" style="color:#64748b;transition:transform .25s;"></i>
        </div>
    </div>

    <div id="form-tambah-semester" style="display:none;">
        <div class="card-body">
            <p style="font-size:13px;color:#64748b;margin-bottom:16px;">
                <i class="bi bi-info-circle me-1" style="color:#6366f1;"></i>
                Tambah semester baru tanpa bergantung data Dapodik.
                Semester ID akan di-generate otomatis dari tahun ajaran dan tipe.
            </p>

            <form method="POST" action="{{ route('admin.semester.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">
                            TIPE SEMESTER <span style="color:red">*</span>
                        </label>
                        <select name="tipe" required id="select-tipe"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;"
                            onchange="updatePreview()">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="ganjil" {{ old('tipe') === 'ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                            <option value="genap"  {{ old('tipe') === 'genap'  ? 'selected' : '' }}>Semester Genap</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">
                            TAHUN AJARAN <span style="color:red">*</span>
                        </label>
                        <input type="text" name="tahun_ajaran" id="input-tahun"
                            placeholder="Contoh: 2025/2026"
                            value="{{ old('tahun_ajaran') }}"
                            maxlength="9"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;"
                            onkeyup="formatTahunAjaran(this); updatePreview()" required>
                        <p style="font-size:11px;color:#94a3b8;margin-top:3px;">Format: YYYY/YYYY — contoh: 2025/2026</p>
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">
                            TANGGAL MULAI <span style="color:red">*</span>
                        </label>
                        <input type="date" name="tanggal_mulai" required
                            value="{{ old('tanggal_mulai') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                    <div>
                        <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:5px;">
                            TANGGAL SELESAI <span style="color:red">*</span>
                        </label>
                        <input type="date" name="tanggal_selesai" required
                            value="{{ old('tanggal_selesai') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                    </div>

                </div>

                {{-- Preview Nama Semester --}}
                <div id="preview-semester" style="display:none;margin-top:14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;">
                    <p style="font-size:11px;color:#16a34a;font-weight:700;margin-bottom:4px;">PREVIEW</p>
                    <p id="preview-nama" style="font-size:16px;font-weight:800;color:#15803d;"></p>
                    <p id="preview-id"   style="font-size:11px;color:#64748b;margin-top:2px;"></p>
                </div>

                <div style="display:flex;gap:10px;margin-top:16px;justify-content:flex-end;">
                    <button type="button" onclick="toggleTambahSemester()"
                        class="btn" style="background:#f1f5f9;color:#374151;">
                        Batal
                    </button>
                    <button type="submit" class="btn" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;">
                        <i class="bi bi-plus-circle-fill me-2"></i>Tambah Semester
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Daftar Semester --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="bi bi-calendar3 me-2" style="color:#6366f1;"></i>Daftar Semester</h3>
        <span style="font-size:12px;color:#94a3b8;">{{ $semesters->count() }} semester</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nama Semester</th>
                        <th>Tahun Ajaran</th>
                        <th>Tipe</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semesters as $sem)
                    <tr style="{{ $sem->is_aktif ? 'background:#f0fdf4;' : '' }}">
                        <td>
                            <div style="font-weight:700;font-size:13px;">{{ $sem->nama }}</div>
                            @if($sem->is_aktif)
                            <span style="font-size:10px;background:#dcfce7;color:#16a34a;padding:1px 8px;border-radius:20px;font-weight:700;">AKTIF</span>
                            @endif
                        </td>
                        <td style="font-size:13px;">{{ $sem->tahun_ajaran ?? '-' }}</td>
                        <td>
                            @if($sem->tipe)
                            <span style="font-size:11px;padding:2px 10px;border-radius:20px;font-weight:600;
                                background:{{ $sem->tipe === 'ganjil' ? '#fef3c7' : '#e0f2fe' }};
                                color:{{ $sem->tipe === 'ganjil' ? '#d97706' : '#0284c7' }};">
                                {{ ucfirst($sem->tipe) }}
                            </span>
                            @else
                            <span style="color:#94a3b8;font-size:12px;">-</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#64748b;">
                            {{ $sem->tanggal_mulai ? $sem->tanggal_mulai->format('d M Y') : '-' }}
                        </td>
                        <td style="font-size:12px;color:#64748b;">
                            {{ $sem->tanggal_selesai ? $sem->tanggal_selesai->format('d M Y') : '-' }}
                        </td>
                        <td>
                            @if($sem->is_aktif)
                                <span class="badge badge-success">✅ Aktif</span>
                            @else
                                <span class="badge" style="background:#f1f5f9;color:#94a3b8;">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                @if(!$sem->is_aktif)
                                <form method="POST" action="{{ route('admin.semester.set-aktif', $sem) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm"
                                        style="background:#dcfce7;color:#16a34a;"
                                        title="Jadikan Aktif"
                                        onclick="return confirm('Jadikan {{ addslashes($sem->nama) }} sebagai semester aktif?')">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.semester.destroy', $sem) }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete(this.closest('form'))"
                                        title="Hapus Semester">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                                @else
                                <span style="font-size:11px;color:#94a3b8;padding:5px;">Semester aktif</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="bi bi-calendar-x" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            Belum ada data semester
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleTambahSemester() {
    const form = document.getElementById('form-tambah-semester');
    const icon = document.getElementById('icon-tambah');
    const isOpen = form.style.display !== 'none';
    form.style.display  = isOpen ? 'none' : 'block';
    icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

function formatTahunAjaran(input) {
    let val = input.value.replace(/[^0-9\/]/g, '');
    // Auto-insert slash setelah 4 digit
    if (val.length === 4 && !val.includes('/')) {
        val = val + '/';
    }
    input.value = val;
}

function updatePreview() {
    const tipe  = document.getElementById('select-tipe').value;
    const tahun = document.getElementById('input-tahun').value;
    const preview = document.getElementById('preview-semester');
    const namaEl  = document.getElementById('preview-nama');
    const idEl    = document.getElementById('preview-id');

    if (tipe && tahun && /^\d{4}\/\d{4}$/.test(tahun)) {
        const tahunAwal = tahun.substring(0, 4);
        const tipeAngka = tipe === 'ganjil' ? '1' : '2';
        const semId     = tahunAwal + tipeAngka;
        namaEl.textContent = 'Semester ' + (tipe.charAt(0).toUpperCase() + tipe.slice(1)) + ' ' + tahun;
        idEl.textContent   = 'Semester ID (auto): ' + semId;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

// Buka form jika ada validation error dari old input
@if(old('tipe') || $errors->any())
toggleTambahSemester();
@endif
</script>
