@extends('layouts.app')

{{-- resources/views/admin/semester/wizard.blade.php --}}

@section('page-title', 'Wizard Pergantian Semester')
@section('page-subtitle', 'Kelola pergantian semester dan tahun ajaran')

@section('content')
<div class="page-title">
    <h1>🗓️ Wizard Pergantian Semester</h1>
    <p>Gunakan wizard ini untuk mengganti semester aktif atau memproses kenaikan kelas / tahun ajaran baru.</p>
</div>

{{-- Semester Aktif Sekarang --}}
<div class="card" style="margin-bottom:20px;border-left:4px solid #6366f1;">
    <div class="card-body">
        <div style="display:flex;gap:32px;flex-wrap:wrap;align-items:center;">
            <div>
                <p style="font-size:11px;color:#94a3b8;font-weight:700;margin-bottom:4px;">SEMESTER AKTIF SEKARANG</p>
                <p style="font-size:20px;font-weight:800;color:#6366f1;">
                    {{ $semesterAktif?->nama ?? '— Belum ada —' }}
                </p>
                <p style="font-size:12px;color:#64748b;">
                    {{ $semesterAktif?->tahun_ajaran ?? '' }}
                    {{ $semesterAktif?->tipe ? '· ' . ucfirst($semesterAktif->tipe) : '' }}
                </p>
            </div>
            <div style="display:flex;gap:24px;flex-wrap:wrap;">
                <div style="text-align:center;">
                    <p style="font-size:24px;font-weight:900;color:#0f172a;">{{ $stats['total_siswa'] }}</p>
                    <p style="font-size:11px;color:#94a3b8;">Siswa Aktif</p>
                </div>
                <div style="text-align:center;">
                    <p style="font-size:24px;font-weight:900;color:#0f172a;">{{ $stats['total_rombel'] }}</p>
                    <p style="font-size:11px;color:#94a3b8;">Rombel Aktif</p>
                </div>
                <div style="text-align:center;">
                    <p style="font-size:24px;font-weight:900;color:#dc2626;">{{ $stats['siswa_xii'] }}</p>
                    <p style="font-size:11px;color:#94a3b8;">Siswa Kelas XII</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== TAMBAH SEMESTER MANUAL + DAFTAR SEMESTER ===== --}}
@include('admin.semester._tambah_manual')

{{-- Pilih Mode --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" id="mode-selector">

    {{-- Mode A: Ganti Semester --}}
    <div class="mode-card" id="mode-a" onclick="selectMode('a')"
         style="cursor:pointer;border:2px solid #e2e8f0;border-radius:16px;padding:28px;background:#fff;transition:all .2s;">
        <div style="font-size:32px;margin-bottom:12px;">📅</div>
        <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin-bottom:8px;">Ganti Semester</h3>
        <p style="font-size:13px;color:#64748b;line-height:1.6;">
            Pindah dari semester ganjil ke genap (atau sebaliknya) <strong>dalam tahun ajaran yang sama</strong>.
            Rombel semester lama bisa diarsipkan.
        </p>
        <div style="margin-top:16px;padding:10px 14px;background:#f0f9ff;border-radius:8px;font-size:12px;color:#0369a1;">
            ✓ Tidak memproses kelulusan<br>
            ✓ Data siswa tetap<br>
            ✓ Arsip rombel semester lama (opsional)
        </div>
    </div>

    {{-- Mode B: Tahun Ajaran Baru --}}
    <div class="mode-card" id="mode-b" onclick="selectMode('b')"
         style="cursor:pointer;border:2px solid #e2e8f0;border-radius:16px;padding:28px;background:#fff;transition:all .2s;">
        <div style="font-size:32px;margin-bottom:12px;">🎓</div>
        <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin-bottom:8px;">Tahun Ajaran Baru</h3>
        <p style="font-size:13px;color:#64748b;line-height:1.6;">
            Proses kenaikan kelas dan kelulusan untuk <strong>pergantian tahun ajaran</strong>.
            Siswa XII diluluskan dan dipindah ke alumni.
        </p>
        <div style="margin-top:16px;padding:10px 14px;background:#fef3c7;border-radius:8px;font-size:12px;color:#92400e;">
            ⚠️ Siswa XII akan diluluskan massal<br>
            ✓ Arsip semua rombel lama<br>
            ✓ Tarik data Dapodik setelahnya
        </div>
    </div>
</div>

{{-- FORM MODE A: Ganti Semester --}}
<div id="form-a" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>📅 Ganti Semester Aktif</h3>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">
                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:1px;display:block;margin-bottom:8px;">PILIH SEMESTER BARU</label>
                    <select id="semester-a" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">— Pilih Semester —</option>
                        @foreach($semesters as $s)
                        <option value="{{ $s->id }}" {{ $semesterAktif?->id === $s->id ? 'disabled' : '' }}>
                            {{ $s->nama }} {{ $semesterAktif?->id === $s->id ? '(Aktif)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:1px;display:block;margin-bottom:8px;">OPSI ROMBEL</label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;">
                        <input type="checkbox" id="arsip-rombel-a" checked style="width:16px;height:16px;accent-color:#6366f1;">
                        <span style="font-size:13px;color:#374151;">Arsipkan rombel semester lama</span>
                    </label>
                    <p style="font-size:11px;color:#94a3b8;margin-top:6px;">
                        Rombel semester lama tidak akan tampil di form absensi/jurnal baru.
                    </p>
                </div>
            </div>

            {{-- Preview rombel yang akan diarsip --}}
            @if($rombelAktif->count())
            <div style="margin-top:20px;">
                <p style="font-size:12px;font-weight:700;color:#94a3b8;margin-bottom:10px;">
                    ROMBEL YANG AKAN DIARSIPKAN ({{ $rombelAktif->count() }} rombel):
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    @foreach($rombelAktif as $r)
                    <span style="padding:4px 12px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:20px;font-size:12px;color:#0369a1;font-weight:600;">
                        {{ $r->nama_rombel }} ({{ $r->jumlah_siswa }} siswa)
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <div style="margin-top:24px;display:flex;gap:12px;">
                <button onclick="selectMode(null)" class="btn" style="background:#f1f5f9;color:#374151;">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
                <button onclick="eksekusiGantiSemester()" class="btn btn-primary" id="btn-ganti-semester">
                    <i class="bi bi-arrow-repeat"></i> Ganti Semester Aktif
                </button>
            </div>
        </div>
    </div>
</div>

{{-- FORM MODE B: Tahun Ajaran Baru --}}
<div id="form-b" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>🎓 Proses Tahun Ajaran Baru</h3>
        </div>
        <div class="card-body">

            <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:10px;padding:14px 18px;margin-bottom:20px;">
                <p style="font-weight:700;color:#856404;font-size:13px;">⚠️ Perhatian!</p>
                <p style="font-size:12px;color:#856404;margin-top:4px;">
                    Proses ini akan meluluskan <strong>semua siswa kelas XII aktif ({{ $stats['siswa_xii'] }} siswa)</strong>
                    dan mengarsipkan semua rombel semester ini. Pastikan data sudah benar sebelum melanjutkan.
                    Proses ini <strong>tidak bisa dibatalkan</strong>.
                </p>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:20px;">
                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:1px;display:block;margin-bottom:8px;">SEMESTER AKTIF BARU</label>
                    <select id="semester-b" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;background:#fff;">
                        <option value="">— Pilih Semester —</option>
                        @foreach($semesters as $s)
                        <option value="{{ $s->id }}" {{ $semesterAktif?->id === $s->id ? 'disabled' : '' }}>
                            {{ $s->nama }} {{ $semesterAktif?->id === $s->id ? '(Aktif)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:1px;display:block;margin-bottom:8px;">TAHUN LULUS</label>
                    <input type="text" id="tahun-lulus" placeholder="cth: 2025"
                        value="{{ date('Y') }}"
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:1px;display:block;margin-bottom:8px;">TANGGAL LULUS</label>
                    <input type="date" id="tanggal-lulus"
                        value="{{ date('Y-m-d') }}"
                        style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;">
                </div>
            </div>

            {{-- Preview siswa XII --}}
            <div style="background:#f8fafc;border-radius:10px;padding:16px;margin-bottom:20px;">
                <p style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:8px;">YANG AKAN DIPROSES:</p>
                <div style="display:flex;gap:24px;flex-wrap:wrap;">
                    <div style="text-align:center;">
                        <p style="font-size:22px;font-weight:900;color:#dc2626;">{{ $stats['siswa_xii'] }}</p>
                        <p style="font-size:11px;color:#94a3b8;">Siswa XII diluluskan</p>
                    </div>
                    <div style="text-align:center;">
                        <p style="font-size:22px;font-weight:900;color:#d97706;">{{ $rombelAktif->count() }}</p>
                        <p style="font-size:11px;color:#94a3b8;">Rombel diarsipkan</p>
                    </div>
                    <div style="text-align:center;">
                        <p style="font-size:22px;font-weight:900;color:#0369a1;">{{ $stats['total_siswa'] - $stats['siswa_xii'] }}</p>
                        <p style="font-size:11px;color:#94a3b8;">Siswa tetap (X & XI)</p>
                    </div>
                </div>
            </div>

            {{-- Konfirmasi checkbox --}}
            <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;margin-bottom:20px;">
                <input type="checkbox" id="konfirmasi-naik-kelas" style="width:16px;height:16px;margin-top:2px;accent-color:#dc2626;flex-shrink:0;">
                <span style="font-size:13px;color:#374151;">
                    Saya memahami bahwa proses ini akan meluluskan <strong>{{ $stats['siswa_xii'] }} siswa kelas XII</strong>,
                    mengarsipkan <strong>{{ $rombelAktif->count() }} rombel</strong>, dan mengaktifkan semester baru yang dipilih.
                    Proses ini tidak dapat dibatalkan.
                </span>
            </label>

            <div style="display:flex;gap:12px;">
                <button onclick="selectMode(null)" class="btn" style="background:#f1f5f9;color:#374151;">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
                <button onclick="eksekusiNaikKelas()" class="btn" id="btn-naik-kelas"
                        style="background:#dc2626;color:#fff;opacity:.5;cursor:not-allowed;" disabled>
                    <i class="bi bi-mortarboard-fill"></i> Proses Tahun Ajaran Baru
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.mode-card:hover  { border-color:#6366f1 !important; box-shadow:0 4px 20px rgba(99,102,241,.1); }
.mode-card.selected { border-color:#6366f1 !important; background:#fafbff !important; }
@media(max-width:768px) {
    #mode-selector { grid-template-columns:1fr; }
}
</style>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function selectMode(mode) {
    document.getElementById('mode-selector').style.display = mode ? 'none' : 'grid';
    document.getElementById('form-a').style.display = mode === 'a' ? 'block' : 'none';
    document.getElementById('form-b').style.display = mode === 'b' ? 'block' : 'none';
}

document.getElementById('konfirmasi-naik-kelas')?.addEventListener('change', function() {
    const btn = document.getElementById('btn-naik-kelas');
    btn.style.opacity = this.checked ? '1' : '.5';
    btn.style.cursor  = this.checked ? 'pointer' : 'not-allowed';
    btn.disabled      = !this.checked;
});

function eksekusiGantiSemester() {
    const semesterId = document.getElementById('semester-a').value;
    const arsip      = document.getElementById('arsip-rombel-a').checked;

    if (!semesterId) {
        Swal.fire({ icon:'warning', title:'Pilih semester terlebih dahulu!' });
        return;
    }

    Swal.fire({
        title: 'Ganti Semester?',
        html: 'Semester aktif akan diganti.' + (arsip ? ' Rombel semester lama akan diarsipkan.' : ''),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor:  '#94a3b8',
        confirmButtonText:  'Ya, Ganti!',
        cancelButtonText:   'Batal',
    }).then(res => {
        if (!res.isConfirmed) return;
        const btn = document.getElementById('btn-ganti-semester');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';

        fetch('{{ route("admin.semester.ganti") }}', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF },
            body: JSON.stringify({ semester_id_baru:semesterId, arsip_rombel:arsip }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.status) {
                Swal.fire({ icon:'success', title:'Berhasil!', html:data.message, timer:3000, showConfirmButton:false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon:'error', title:'Gagal!', text:data.message });
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Ganti Semester Aktif';
            }
        });
    });
}

function eksekusiNaikKelas() {
    const semesterId   = document.getElementById('semester-b').value;
    const tahunLulus   = document.getElementById('tahun-lulus').value;
    const tanggalLulus = document.getElementById('tanggal-lulus').value;
    const konfirmasi   = document.getElementById('konfirmasi-naik-kelas').checked;

    if (!semesterId || !tahunLulus || !tanggalLulus) {
        Swal.fire({ icon:'warning', title:'Lengkapi semua field terlebih dahulu!' });
        return;
    }
    if (!konfirmasi) return;

    Swal.fire({
        title: 'Proses Tahun Ajaran Baru?',
        html: '<strong>Tindakan ini tidak bisa dibatalkan!</strong><br>Siswa XII akan diluluskan dan rombel akan diarsipkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor:  '#94a3b8',
        confirmButtonText:  'Ya, Proses!',
        cancelButtonText:   'Batal',
    }).then(res => {
        if (!res.isConfirmed) return;
        const btn = document.getElementById('btn-naik-kelas');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';

        fetch('{{ route("admin.semester.naik-kelas") }}', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF },
            body: JSON.stringify({
                semester_id_baru: semesterId,
                tahun_lulus:      tahunLulus,
                tanggal_lulus:    tanggalLulus,
                arsip_rombel:     true,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.status) {
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', html: data.message,
                    confirmButtonColor: '#6366f1',
                    confirmButtonText:  'Tarik Data Dapodik',
                }).then(() => { window.location.href = '{{ route("admin.dapodik.tarik") }}'; });
            } else {
                Swal.fire({ icon:'error', title:'Gagal!', text:data.message });
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-mortarboard-fill"></i> Proses Tahun Ajaran Baru';
            }
        });
    });
}
</script>
@endsection
