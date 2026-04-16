<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\LaporanAbsensiController;
use App\Http\Controllers\IzinBerencanaController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\CacheController;
use App\Http\Controllers\Auth\GoogleAuthController;

Route::get('/', function () {
    return redirect('/login');
});

    // ── RESET PASSWORD (publik, tanpa auth) ──
    Route::get('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showForm'])->name('password.reset.form');
    Route::post('/reset-password/verify', [App\Http\Controllers\Auth\ResetPasswordController::class, 'verify'])->name('password.reset.verify');
    Route::post('/reset-password/update', [App\Http\Controllers\Auth\ResetPasswordController::class, 'update'])->name('password.reset.update');

    // ===== PUBLIC — VERIFIKASI ALUMNI =====
    Route::get('/alumni', [App\Http\Controllers\Public\AlumniVerifikasiController::class, 'index'])->name('public.alumni.index');
    Route::post('/alumni/verify', [App\Http\Controllers\Public\AlumniVerifikasiController::class, 'verify'])->name('public.alumni.verify');

    // ===== Google OAuth =====
    Route::get('/auth/google',          [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

    // ===== DISABLE REGISTER ===== 
    Route::get('/register',  fn() => abort(404));
    Route::post('/register', fn() => abort(404));

// Group 1: auth saja (tanpa check.default.password)
Route::middleware(['auth','session.timeout'])->group(function () {

    Route::get('/profile',    [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/password/change', function() {
        return view('auth.change-password');
    })->name('password.change');

    Route::post('/password/update', function(\Illuminate\Http\Request $request) {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.min'       => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        auth()->user()->update([
            'password'              => \Illuminate\Support\Facades\Hash::make($request->password),
            'is_default_password'   => false,
            'default_password_hint' => null,
        ]);

        $user = auth()->user();

        if ($user->hasRole('admin'))                     $redirect = '/dashboard/admin';
        elseif ($user->hasRole('wakil_kepala_sekolah'))  $redirect = '/dashboard/wakil-kepala';
        elseif ($user->hasRole('guru'))                  $redirect = '/dashboard/guru';
        elseif ($user->hasRole('siswa'))                 $redirect = '/dashboard/siswa';
        elseif ($user->hasRole('kepala_sekolah'))        $redirect = '/dashboard/kepala-sekolah';
        elseif ($user->hasRole('bk'))                    $redirect = '/dashboard/bk';
        elseif ($user->hasRole('tata_usaha'))            $redirect = '/dashboard/tata-usaha';
        else                                             $redirect = '/dashboard/admin';

        return redirect($redirect)->with('success', 'Password berhasil diubah!');

    })->name('password.change.update');

});

// Group 2: auth + check.default.password
Route::middleware(['auth', 'check.default.password','session.timeout'])->group(function () {

    // Dashboard per role
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin')->name('dashboard.admin');
    Route::get('/dashboard/kepala-sekolah', [DashboardController::class, 'kepalaSekolah'])
        ->middleware('role:kepala_sekolah')->name('dashboard.kepala_sekolah');
    Route::get('/dashboard/wakil-kepala', [DashboardController::class, 'wakilKepala'])
        ->middleware('role:wakil_kepala_sekolah')->name('dashboard.wakil_kepala');
    Route::get('/dashboard/guru', [DashboardController::class, 'guru'])
        ->middleware('role:guru')->name('dashboard.guru');
    Route::get('/dashboard/bk', [DashboardController::class, 'bk'])
        ->middleware('role:bk')->name('dashboard.bk');
    Route::get('/dashboard/tata-usaha', [DashboardController::class, 'tataUsaha'])
        ->middleware('role:tata_usaha')->name('dashboard.tata_usaha');
    Route::get('/dashboard/siswa', [DashboardController::class, 'siswa'])
        ->middleware('role:siswa')->name('dashboard.siswa');

    // ==========================================
    // ADMIN
    // ==========================================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        // Manajemen User
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);

        // Generate Akun
        Route::get('akun', [App\Http\Controllers\Admin\AkunController::class, 'index'])->name('akun.index');
        Route::post('akun/siswa/massal', [App\Http\Controllers\Admin\AkunController::class, 'generateSiswaMassal'])->name('akun.siswa.massal');
        Route::post('akun/siswa/single', [App\Http\Controllers\Admin\AkunController::class, 'generateSiswaSingle'])->name('akun.siswa.single');
        Route::post('akun/guru/massal', [App\Http\Controllers\Admin\AkunController::class, 'generateGuruMassal'])->name('akun.guru.massal');
        Route::post('akun/guru/single', [App\Http\Controllers\Admin\AkunController::class, 'generateGuruSingle'])->name('akun.guru.single');
        Route::post('akun/reset-password', [App\Http\Controllers\Admin\AkunController::class, 'resetPassword'])->name('akun.reset.password');

        // Data Akademik - Siswa
        Route::resource('siswa', App\Http\Controllers\Admin\SiswaController::class);

        // Data Akademik - Mutasi
        Route::post('mutasi/store', [App\Http\Controllers\Admin\MutasiSiswaController::class, 'store'])->name('mutasi.store');
        Route::patch('mutasi/{siswa}/restore', [App\Http\Controllers\Admin\MutasiSiswaController::class, 'restore'])->name('mutasi.restore');
        Route::resource('mutasi', App\Http\Controllers\Admin\MutasiSiswaController::class)->only(['index']);

        // Data Akademik - Alumni
        Route::get('alumni/luluskan-massal', fn() => redirect()->route('admin.alumni.index'));
        Route::post('alumni/luluskan-massal', [App\Http\Controllers\Admin\AlumniController::class, 'luluskanMassal'])->name('alumni.luluskan.massal');
        Route::post('alumni/import', [App\Http\Controllers\Admin\AlumniController::class, 'importExcel'])->name('alumni.import');
        Route::resource('alumni', App\Http\Controllers\Admin\AlumniController::class, ['parameters' => ['alumni' => 'alumni']])->only(['index', 'create', 'store', 'show', 'edit', 'update']);

        // Data GTK
        Route::resource('guru', App\Http\Controllers\Admin\GuruController::class);

        // Data Rombel
        Route::resource('rombel', App\Http\Controllers\Admin\RombelController::class)->only(['index', 'show']);

        // Mata Pelajaran
        Route::resource('mapel', App\Http\Controllers\Admin\MataPelajaranController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Jurnal - Admin full akses
        Route::get('jurnal', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'index'])->name('jurnal.index');
        Route::get('jurnal/create', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'create'])->name('jurnal.create');
        Route::post('jurnal', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'store'])->name('jurnal.store');
        Route::get('jurnal/{jurnal}', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'show'])->name('jurnal.show');
        Route::delete('jurnal/{jurnal}', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'destroy'])->name('jurnal.destroy');

        // Dapodik - Pengaturan
        Route::get('/dapodik/pengaturan',  [App\Http\Controllers\Admin\PengaturanDapodikController::class, 'index'])->name('dapodik.pengaturan');
        Route::post('/dapodik/pengaturan', [App\Http\Controllers\Admin\PengaturanDapodikController::class, 'store'])->name('dapodik.pengaturan.store');
        Route::get('/dapodik/test-koneksi',[App\Http\Controllers\Admin\PengaturanDapodikController::class, 'testKoneksi'])->name('dapodik.test');

        // Dapodik - Tarik Data
        Route::get('/dapodik/tarik',           [App\Http\Controllers\Admin\TarikDataController::class, 'index'])->name('dapodik.tarik');
        Route::post('/dapodik/tarik/semua',    [App\Http\Controllers\Admin\TarikDataController::class, 'tarikSemua'])->name('dapodik.tarik.semua');
        Route::post('/dapodik/tarik/kategori', [App\Http\Controllers\Admin\TarikDataController::class, 'tarikKategori'])->name('dapodik.tarik.kategori');
        Route::post('/dapodik/semester/aktif', [App\Http\Controllers\Admin\TarikDataController::class, 'setSemesterAktif'])->name('dapodik.semester.aktif');

        // Settings Sekolah
        Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

        // ===== BACKUP & RESTORE =====
        Route::prefix('backup')->name('backup.')->group(function () {
            Route::get('/',                    [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
            Route::post('/create',             [App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
            Route::get('/download',            [App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
            Route::delete('/destroy',          [App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('destroy');
            Route::post('/restore',            [App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
            Route::post('/restore-existing',   [App\Http\Controllers\Admin\BackupController::class, 'restoreExisting'])->name('restore-existing');
        });

        // ===== SEMESTER WIZARD =====
        Route::get('semester/wizard', [App\Http\Controllers\Admin\SemesterController::class, 'wizard'])->name('semester.wizard');
        Route::post('semester/ganti', [App\Http\Controllers\Admin\SemesterController::class, 'gantiSemester'])->name('semester.ganti');
        Route::post('semester/naik-kelas', [App\Http\Controllers\Admin\SemesterController::class, 'naikKelas'])->name('semester.naik-kelas');

        // Semester Manual
        Route::post('semester',                    [App\Http\Controllers\Admin\SemesterController::class, 'store'])->name('semester.store');
        Route::post('semester/{semester}/aktif',   [App\Http\Controllers\Admin\SemesterController::class, 'setAktif'])->name('semester.set-aktif');
        Route::delete('semester/{semester}',       [App\Http\Controllers\Admin\SemesterController::class, 'destroy'])->name('semester.destroy');

        // ===== ABSENSI ADMIN =====
        Route::get('absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::post('absensi/{absensi}/lock', [AbsensiController::class, 'toggleLock'])->name('absensi.lock');
        Route::resource('absensi', AbsensiController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

        // Activity Log
        Route::get('activity-log', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('activity-log/download', [App\Http\Controllers\Admin\ActivityLogController::class, 'download'])->name('activity-log.download');
        Route::delete('activity-log/destroy-old', [App\Http\Controllers\Admin\ActivityLogController::class, 'destroyOld'])->name('activity-log.destroy-old');
        Route::delete('activity-log/destroy-all', [App\Http\Controllers\Admin\ActivityLogController::class, 'destroyAll'])->name('activity-log.destroy-all');
        Route::post('clear-cache', [CacheController::class, 'clearAll'])->name('clear-cache');

    });

    // ==========================================
    // PRESTASI - Multi Role
    // ==========================================
    Route::prefix('prestasi')->name('prestasi.')->middleware('role:admin|bk|tata_usaha|guru|siswa|wakil_kepala_sekolah|kepala_sekolah')->group(function () {

        // PENTING: route spesifik sebelum resource
        Route::get('siswa-by-rombel', [App\Http\Controllers\PrestasiController::class, 'getSiswaByRombel'])->name('siswa-by-rombel');

        // Kategori (admin only)
        Route::get('kategori', [App\Http\Controllers\PrestasiController::class, 'kategoriIndex'])
            ->name('kategori.index')->middleware('role:admin');
        Route::post('kategori', [App\Http\Controllers\PrestasiController::class, 'kategoriStore'])
            ->name('kategori.store')->middleware('role:admin');
        Route::put('kategori/{kategori}', [App\Http\Controllers\PrestasiController::class, 'kategoriUpdate'])
            ->name('kategori.update')->middleware('role:admin');
        Route::delete('kategori/{kategori}', [App\Http\Controllers\PrestasiController::class, 'kategoriDestroy'])
            ->name('kategori.destroy')->middleware('role:admin');

        // Verifikasi (admin|bk|tata_usaha)
        Route::patch('{prestasi}/verifikasi', [App\Http\Controllers\PrestasiController::class, 'verifikasi'])
            ->name('verifikasi')->middleware('role:admin|bk|tata_usaha');

        // CRUD Prestasi
        Route::resource('/', App\Http\Controllers\PrestasiController::class)
            ->parameters(['' => 'prestasi']);
    });

    // ==========================================
    // TIKET - Kritik, Saran & Pengaduan
    // ==========================================
    Route::prefix('tiket')->name('tiket.')->middleware('role:admin|kepala_sekolah|guru|siswa|bk|tata_usaha|wakil_kepala_sekolah')->group(function () {

        // PENTING: route spesifik SEBELUM resource
        Route::patch('{tiket}/respon',    [App\Http\Controllers\TiketController::class, 'respon'])->name('respon');
        Route::patch('{tiket}/tutup',     [App\Http\Controllers\TiketController::class, 'tutup'])->name('tutup')->middleware('role:admin|kepala_sekolah');
        Route::patch('{tiket}/buka',      [App\Http\Controllers\TiketController::class, 'buka'])->name('buka')->middleware('role:admin|kepala_sekolah');
        Route::patch('{tiket}/prioritas', [App\Http\Controllers\TiketController::class, 'updatePrioritas'])->name('prioritas')->middleware('role:admin|kepala_sekolah');

        // Resource — hanya index, create, store, show
        Route::resource('/', App\Http\Controllers\TiketController::class)
            ->parameters(['' => 'tiket'])
            ->only(['index', 'create', 'store', 'show']);
    });

    // ============================================================
    // IZIN BERENCANA
    // ============================================================
    Route::prefix('izin')->name('izin.')->middleware('role:admin|kepala_sekolah|bk|waka|siswa|wakil_kepala_sekolah')->group(function () {
        Route::get('/',                    [IzinBerencanaController::class, 'index'])->name('index');
        Route::get('/laporan',             [IzinBerencanaController::class, 'laporan'])->name('laporan');
        Route::get('/create',              [IzinBerencanaController::class, 'create'])->name('create')->middleware('role:siswa');
        Route::post('/',                   [IzinBerencanaController::class, 'store'])->name('store')->middleware('role:siswa');
        Route::get('/{izin}',              [IzinBerencanaController::class, 'show'])->name('show');
        Route::post('/{izin}/approve',     [IzinBerencanaController::class, 'approve'])->name('approve');
        Route::post('/{izin}/tolak',       [IzinBerencanaController::class, 'tolak'])->name('tolak');
        Route::post('/{izin}/batalkan',    [IzinBerencanaController::class, 'batalkan'])->name('batalkan');
        Route::get('/{izin}/cetak',        [IzinBerencanaController::class, 'cetakSurat'])->name('cetak');
    });

    // ==========================================
    // BK - Admin & BK
    // ==========================================
        Route::prefix('bk')->name('bk.')->middleware('role:admin|bk')->group(function () {
        Route::resource('jenis-pelanggaran', App\Http\Controllers\BK\JenisPelanggaranController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::resource('pelanggaran', App\Http\Controllers\BK\PelanggaranController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::get('rekap', [App\Http\Controllers\BK\RekapController::class, 'index'])->name('rekap.index');

        // Absensi (BK bisa input & edit hari sebelumnya)
        Route::get('absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/{absensi}', [AbsensiController::class, 'show'])->name('absensi.show');
    });

    // Poin Positif
        Route::prefix('bk/poin-positif')->name('bk.poin-positif.')->group(function () {
        Route::get('/',                  [App\Http\Controllers\BK\PoinPositifController::class, 'index'])->name('index');
        Route::post('/',                 [App\Http\Controllers\BK\PoinPositifController::class, 'store'])->name('store');
        Route::delete('/{poinPositif}',  [App\Http\Controllers\BK\PoinPositifController::class, 'destroy'])->name('destroy');
        Route::get('/rekap',             [App\Http\Controllers\BK\PoinPositifController::class, 'rekap'])->name('rekap');
        Route::get('/jenis',             [App\Http\Controllers\BK\PoinPositifController::class, 'jenisIndex'])->name('jenis');
        Route::post('/jenis',            [App\Http\Controllers\BK\PoinPositifController::class, 'jenisStore'])->name('jenis.store');
        Route::put('/jenis/{jenis}',     [App\Http\Controllers\BK\PoinPositifController::class, 'jenisUpdate'])->name('jenis.update');
        Route::delete('/jenis/{jenis}',  [App\Http\Controllers\BK\PoinPositifController::class, 'jenisDestroy'])->name('jenis.destroy');
    });

    // ==========================================
    // GURU - Jurnal & Absensi
    // ==========================================
    Route::prefix('guru')->name('guru.')->middleware('role:guru|wakil_kepala_sekolah|bk')->group(function () {
        // Jurnal
        Route::get('jurnal', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'index'])->name('jurnal.index');
        Route::get('jurnal/create', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'create'])->name('jurnal.create');
        Route::post('jurnal', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'store'])->name('jurnal.store');
        Route::get('jurnal/{jurnal}', [App\Http\Controllers\Guru\JurnalMengajarController::class, 'show'])->name('jurnal.show');

        // Absensi
        Route::get('absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/{absensi}', [AbsensiController::class, 'show'])->name('absensi.show');
    });

    // ==========================================
    // LAPORAN - Multi Role
    // ==========================================
    Route::prefix('laporan')->name('laporan.')->middleware('role:admin|guru|kepala_sekolah|wakil_kepala_sekolah|bk')->group(function () {

        // Jurnal Mengajar
        Route::get('jurnal', [App\Http\Controllers\LaporanController::class, 'jurnal'])->name('jurnal');
        Route::get('jurnal/pdf', [App\Http\Controllers\LaporanController::class, 'jurnalPdf'])->name('jurnal.pdf');
        Route::get('jurnal/excel', [App\Http\Controllers\LaporanController::class, 'jurnalExcel'])->name('jurnal.excel');

        // Pelanggaran
        Route::get('pelanggaran', [App\Http\Controllers\LaporanController::class, 'pelanggaran'])->name('pelanggaran');
        Route::get('pelanggaran/pdf', [App\Http\Controllers\LaporanController::class, 'pelanggaranPdf'])->name('pelanggaran.pdf');
        Route::get('pelanggaran/excel', [App\Http\Controllers\LaporanController::class, 'pelanggaranExcel'])->name('pelanggaran.excel');

        // Upload scan (admin only)
        Route::post('jurnal/{jurnal}/scan', [App\Http\Controllers\LaporanController::class, 'uploadScan'])
            ->name('jurnal.scan')->middleware('role:admin');

        // ===== ABSENSI =====
        Route::get('absensi/rekap-kelas', [LaporanAbsensiController::class, 'rekapKelas'])->name('absensi.rekap-kelas');
        Route::get('absensi/rekap-kelas/pdf', [LaporanAbsensiController::class, 'rekapKelasPdf'])->name('absensi.rekap-kelas.pdf');
        Route::get('absensi/rekap-kelas/excel', [LaporanAbsensiController::class, 'rekapKelasExcel'])->name('absensi.rekap-kelas.excel');

        Route::get('absensi/detail-kelas', [LaporanAbsensiController::class, 'detailKelas'])->name('absensi.detail-kelas');
        Route::get('absensi/detail-kelas/pdf', [LaporanAbsensiController::class, 'detailKelasPdf'])->name('absensi.detail-kelas.pdf');

        Route::get('absensi/rekap-siswa', [LaporanAbsensiController::class, 'rekapSiswa'])->name('absensi.rekap-siswa');
        Route::get('absensi/rekap-siswa/pdf', [LaporanAbsensiController::class, 'rekapSiswaPdf'])->name('absensi.rekap-siswa.pdf');
        Route::get('absensi/rekap-siswa/excel', [LaporanAbsensiController::class, 'rekapSiswaExcel'])->name('absensi.rekap-siswa.excel');

        // ===== PRESTASI =====
        Route::get('prestasi', [App\Http\Controllers\LaporanPrestasiController::class, 'index'])->name('prestasi');
        Route::get('prestasi/pdf', [App\Http\Controllers\LaporanPrestasiController::class, 'pdf'])->name('prestasi.pdf');
        Route::get('prestasi/excel', [App\Http\Controllers\LaporanPrestasiController::class, 'excel'])->name('prestasi.excel');

        // ===== TIKET =====
        Route::get('tiket', [App\Http\Controllers\LaporanTiketController::class, 'index'])->name('tiket')
            ->middleware('role:admin|kepala_sekolah');
        Route::get('tiket/pdf', [App\Http\Controllers\LaporanTiketController::class, 'pdf'])->name('tiket.pdf')
            ->middleware('role:admin|kepala_sekolah');
        Route::get('tiket/excel', [App\Http\Controllers\LaporanTiketController::class, 'excel'])->name('tiket.excel')
            ->middleware('role:admin|kepala_sekolah');

    });

    // ==========================================
    // SISWA
    // ==========================================
    Route::prefix('siswa')->name('siswa.')->middleware('role:siswa')->group(function () {
        Route::get('jurnal-kelas', [App\Http\Controllers\LaporanController::class, 'jurnalKelas'])->name('jurnal.kelas');
        Route::get('absensi', [AbsensiController::class, 'milikSiswa'])->name('absensi');
        Route::get('pelanggaran', [App\Http\Controllers\Siswa\PelanggaranSiswaController::class, 'index'])->name('pelanggaran.index');
    });

});

require __DIR__.'/auth.php';
