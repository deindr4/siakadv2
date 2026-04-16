@extends('layouts.app')

@section('page-title', 'Tarik Data Dapodik')
@section('page-subtitle', 'Sinkronisasi data dari server Dapodik')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')

{{-- Alert tidak ada pengaturan --}}
@if(!$pengaturan)
<div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
    <i class="bi bi-exclamation-triangle-fill" style="color:#f59e0b;font-size:20px;"></i>
    <div>
        <strong style="color:#92400e;">Pengaturan Dapodik belum dikonfigurasi!</strong>
        <p style="font-size:13px;color:#92400e;margin-top:2px;">
            <a href="{{ route('admin.dapodik.pengaturan') }}" style="color:#d97706;font-weight:600;">Klik di sini</a>
            untuk mengatur koneksi Dapodik terlebih dahulu.
        </p>
    </div>
</div>
@endif

{{-- Alert semester belum ada --}}
@if($totalSemester === 0)
<div style="background:#ede9fe;border:1px solid #6366f1;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
    <i class="bi bi-info-circle-fill" style="color:#6366f1;font-size:20px;"></i>
    <div>
        <strong style="color:#4f46e5;">Belum ada data semester!</strong>
        <p style="font-size:13px;color:#4f46e5;margin-top:2px;">
            Klik tombol <strong>"Tarik Data Semester"</strong> terlebih dahulu sebelum menarik data lainnya.
        </p>
    </div>
</div>
@endif

<div class="page-title">
    <h1>☁️ Tarik Data Dapodik</h1>
    <p>Sinkronisasi data siswa, guru, rombel dari server Dapodik</p>
</div>

{{-- Semester Selector + Tarik Semua --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-body" style="display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap;">
        <div style="flex:1;min-width:250px;">
            <label style="font-size:12px;font-weight:600;color:#64748b;display:block;margin-bottom:6px;">
                PILIH SEMESTER
            </label>
            <select id="semesterSelect" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13.5px;outline:none;font-family:inherit;background:#fff;">
                @if($semesters->isEmpty())
                    <option value="">-- Tarik Data Semester Dulu --</option>
                @else
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->semester_id }}" {{ $sem->is_aktif ? 'selected' : '' }}>
                            {{ $sem->nama }} {{ $sem->is_aktif ? '✅ (Aktif)' : '' }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <button onclick="setSemesterAktif()" class="btn" style="background:#f1f5f9;color:#374151;"
                {{ $semesters->isEmpty() ? 'disabled' : '' }}>
                <i class="bi bi-check2-circle"></i> Set Aktif
            </button>
            <button onclick="tarikSemua()" class="btn btn-primary"
                {{ (!$pengaturan || $semesters->isEmpty()) ? 'disabled' : '' }}>
                <i class="bi bi-cloud-download-fill"></i> Tarik Semua Data
            </button>
        </div>
    </div>
</div>

{{-- Kartu Data --}}
<div class="grid grid-4" style="margin-bottom:24px;">

    {{-- Semester --}}
    <div class="card" style="border-top:4px solid #6366f1;">
        <div class="card-body" style="text-align:center;padding:24px 16px;">
            <div style="width:54px;height:54px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;margin:0 auto 12px;">
                📅
            </div>
            <h2 style="font-size:28px;font-weight:800;color:#0f172a;">{{ $totalSemester }}</h2>
            <p style="font-size:13px;color:#64748b;font-weight:500;margin-bottom:6px;">Data Semester</p>
            <p style="font-size:11px;color:#94a3b8;margin-bottom:16px;">Tidak perlu pilih semester</p>
            <button onclick="tarikKategori('semester')" class="btn btn-sm btn-primary" style="width:100%;"
                {{ !$pengaturan ? 'disabled' : '' }}>
                <i class="bi bi-arrow-clockwise"></i> Tarik Data Semester
            </button>
        </div>
    </div>

    {{-- GTK/Guru --}}
    <div class="card" style="border-top:4px solid #8b5cf6;">
        <div class="card-body" style="text-align:center;padding:24px 16px;">
            <div style="width:54px;height:54px;background:linear-gradient(135deg,#8b5cf6,#6366f1);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;margin:0 auto 12px;">
                👨‍🏫
            </div>
            <h2 style="font-size:28px;font-weight:800;color:#0f172a;">{{ $totalGuru }}</h2>
            <p style="font-size:13px;color:#64748b;font-weight:500;margin-bottom:6px;">Data GTK</p>
            <p style="font-size:11px;color:#94a3b8;margin-bottom:16px;">Pilih semester dahulu</p>
            <button onclick="tarikKategori('guru')" class="btn btn-sm btn-primary" style="width:100%;background:#8b5cf6;"
                {{ (!$pengaturan || $semesters->isEmpty()) ? 'disabled' : '' }}>
                <i class="bi bi-arrow-clockwise"></i> Tarik Data GTK
            </button>
        </div>
    </div>

     {{-- Rombel --}}
    <div class="card" style="border-top:4px solid #f59e0b;">
        <div class="card-body" style="text-align:center;padding:24px 16px;">
            <div style="width:54px;height:54px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;margin:0 auto 12px;">
                🏠
            </div>
            <h2 style="font-size:28px;font-weight:800;color:#0f172a;">{{ $totalRombel }}</h2>
            <p style="font-size:13px;color:#64748b;font-weight:500;margin-bottom:6px;">Data Rombel</p>
            <p style="font-size:11px;color:#94a3b8;margin-bottom:16px;">Pilih semester dahulu</p>
            <button onclick="tarikKategori('rombel')" class="btn btn-sm btn-primary" style="width:100%;background:#f59e0b;"
                {{ (!$pengaturan || $semesters->isEmpty()) ? 'disabled' : '' }}>
                <i class="bi bi-arrow-clockwise"></i> Tarik Data Rombel
            </button>
        </div>
    </div>

    {{-- Siswa --}}
    <div class="card" style="border-top:4px solid #10b981;">
        <div class="card-body" style="text-align:center;padding:24px 16px;">
            <div style="width:54px;height:54px;background:linear-gradient(135deg,#10b981,#059669);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;margin:0 auto 12px;">
                👨‍🎓
            </div>
            <h2 style="font-size:28px;font-weight:800;color:#0f172a;">{{ $totalSiswa }}</h2>
            <p style="font-size:13px;color:#64748b;font-weight:500;margin-bottom:6px;">Data Siswa</p>
            <p style="font-size:11px;color:#94a3b8;margin-bottom:16px;">Pilih semester dahulu</p>
            <button onclick="tarikKategori('siswa')" class="btn btn-sm btn-primary" style="width:100%;background:#10b981;"
                {{ (!$pengaturan || $semesters->isEmpty()) ? 'disabled' : '' }}>
                <i class="bi bi-arrow-clockwise"></i> Tarik Data Siswa
            </button>
        </div>
    </div>

</div>

{{-- Log Sinkronisasi --}}
<div class="card">
    <div class="card-header">
        <h3>📋 Riwayat Sinkronisasi</h3>
        @if($lastSync)
        <span style="font-size:12px;color:#94a3b8;">
            Terakhir: {{ $lastSync->created_at->diffForHumans() }}
        </span>
        @endif
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Jenis</th>
                        <th>Semester</th>
                        <th>Baru</th>
                        <th>Update</th>
                        <th>Arsip</th>
                        <th>Gagal</th>
                        <th>Durasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($syncLogs as $log)
                    <tr>
                        <td style="font-size:12px;color:#94a3b8;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge badge-primary">{{ ucfirst($log->jenis) }}</span></td>
                        <td style="font-size:12px;">{{ $log->semester_id ?? '-' }}</td>
                        <td><span style="color:#10b981;font-weight:700;">+{{ $log->created }}</span></td>
                        <td><span style="color:#6366f1;font-weight:700;">↻{{ $log->updated }}</span></td>
                        <td><span style="color:#f59e0b;font-weight:700;">📦{{ $log->archived }}</span></td>
                        <td><span style="color:#ef4444;font-weight:700;">✗{{ $log->failed }}</span></td>
                        <td style="font-size:12px;color:#94a3b8;">{{ $log->durasi_detik }}s</td>
                        <td>
                            @if($log->status === 'sukses')
                                <span class="badge badge-success">Sukses</span>
                            @elseif($log->status === 'sebagian')
                                <span class="badge badge-warning">Sebagian</span>
                            @else
                                <span class="badge badge-danger">Gagal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="bi bi-clock-history" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            Belum ada riwayat sinkronisasi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function getSemesterId() {
    return document.getElementById('semesterSelect').value;
}

function setSemesterAktif() {
    const semId = getSemesterId();
    if (!semId) return Swal.fire('Perhatian', 'Pilih semester terlebih dahulu!', 'warning');

    Swal.fire({
        title: 'Set Semester Aktif?',
        text: 'Semester ini akan dijadikan semester aktif.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Set Aktif',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch('{{ route('admin.dapodik.semester.aktif') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ semester_id: semId })
        })
        .then(r => r.json())
        .then(data => {
            Swal.fire({
                icon: data.status ? 'success' : 'error',
                title: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        });
    });
}

function tarikSemua() {
    const semId = getSemesterId();
    if (!semId) {
        return Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            html: 'Pilih semester terlebih dahulu!<br><small style="color:#94a3b8;">Tarik Data Semester dulu jika belum ada.</small>'
        });
    }

    Swal.fire({
        title: 'Tarik Semua Data?',
        html: `Data lama akan <strong>diarsipkan</strong> dan diganti dengan data semester baru dari Dapodik.<br><br>
               <small style="color:#94a3b8;">Proses ini mungkin memerlukan beberapa menit.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Tarik Semua!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;
        prosesSync(
            '{{ route('admin.dapodik.tarik.semua') }}',
            { semester_id: semId },
            'Menarik semua data...'
        );
    });
}

function tarikKategori(jenis) {
    const semId = getSemesterId();

    // Khusus semester tidak perlu semId
    if (!semId && jenis !== 'semester') {
        return Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            html: 'Pilih semester terlebih dahulu!<br><small style="color:#94a3b8;">Tarik Data Semester dulu jika belum ada.</small>'
        });
    }

    const label = {
        semester : 'Semester',
        siswa    : 'Data Siswa',
        guru     : 'Data GTK/Guru',
        rombel   : 'Data Rombel'
    };

    Swal.fire({
        title: `Tarik ${label[jenis]}?`,
        html: jenis !== 'semester'
            ? `Data <strong>${label[jenis]}</strong> lama akan diarsipkan dan diganti data baru dari Dapodik.`
            : `Data semester akan disinkronkan dari Dapodik.<br>
               <small style="color:#94a3b8;">Lakukan ini pertama kali sebelum tarik data lainnya.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Tarik!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;
        prosesSync(
            '{{ route('admin.dapodik.tarik.kategori') }}',
            { semester_id: semId ?? '', jenis },
            `Menarik ${label[jenis]}...`
        );
    });
}

function prosesSync(url, body, loadingText) {
    Swal.fire({
        title: loadingText,
        html: 'Mohon tunggu, sedang berkomunikasi dengan server Dapodik...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        if (data.status) {
            let detail = '';
            if (data.detail) {
                const d = data.detail;
                if (d.siswa || d.guru) {
                    // Tarik semua (nested)
                    detail = `<table style="width:100%;font-size:13px;margin-top:12px;border-collapse:collapse;">
                        <tr style="background:#f8fafc;">
                            <th style="text-align:left;padding:6px 10px;">Jenis</th>
                            <th style="padding:6px 10px;">Baru</th>
                            <th style="padding:6px 10px;">Update</th>
                            <th style="padding:6px 10px;">Arsip</th>
                        </tr>
                        ${Object.entries(d).map(([k,v]) => `
                            <tr>
                                <td style="padding:6px 10px;text-transform:capitalize;">${k}</td>
                                <td style="padding:6px 10px;color:#10b981;font-weight:700;">+${v.created||0}</td>
                                <td style="padding:6px 10px;color:#6366f1;font-weight:700;">↻${v.updated||0}</td>
                                <td style="padding:6px 10px;color:#f59e0b;font-weight:700;">📦${v.archived||0}</td>
                            </tr>`).join('')}
                    </table>`;
                } else {
                    detail = `<div style="margin-top:12px;padding:12px;background:#f8fafc;border-radius:8px;font-size:13px;">
                        <span style="color:#10b981;font-weight:700;">+${d.created||0} Baru</span> &nbsp;
                        <span style="color:#6366f1;font-weight:700;">↻${d.updated||0} Update</span> &nbsp;
                        <span style="color:#f59e0b;font-weight:700;">📦${d.archived||0} Arsip</span> &nbsp;
                        <span style="color:#ef4444;font-weight:700;">✗${d.failed||0} Gagal</span>
                    </div>`;
                }
            }
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                html: data.message + detail,
            }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Error!', text: 'Koneksi ke server gagal!' });
    });
}
</script>
@endpush
