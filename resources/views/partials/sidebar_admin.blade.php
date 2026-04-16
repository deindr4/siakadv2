{{-- resources/views/partials/sidebar_admin.blade.php --}}
@php
    $ag = '';
    if (request()->is('dashboard/admin'))                                              $ag = 'utama';
    elseif (request()->is('admin/siswa*','admin/mutasi*','admin/alumni*','admin/rombel*','admin/guru*')) $ag = 'akademik';
    elseif (request()->is('admin/mapel*','admin/jurnal*'))                             $ag = 'mapel-jurnal';
    elseif (request()->is('admin/absensi*'))                                           $ag = 'absensi';
    elseif (request()->is('bk/*'))                                                     $ag = 'bk';
    elseif (request()->is('izin*'))                                                    $ag = 'izin';
    elseif (request()->is('prestasi*'))                                                $ag = 'prestasi';
    elseif (request()->is('tiket*'))                                                   $ag = 'tiket';
    elseif (request()->is('laporan*'))                                                 $ag = 'laporan';
    elseif (request()->is('admin/dapodik*'))                                           $ag = 'dapodik';
    elseif (request()->is('admin/settings*','admin/akun*','admin/users*','profile*'))  $ag = 'sistem';
@endphp
@include('partials._sidebar_style')

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='utama'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-grid nav-gi"></i><span class="nav-gl">Utama</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='utama'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('dashboard.admin') }}" class="{{ request()->is('dashboard/admin')?'active':'' }}"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='akademik'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-people nav-gi"></i><span class="nav-gl">Data Akademik</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='akademik'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('admin.siswa.index') }}" class="{{ request()->is('admin/siswa*')?'active':'' }}"><i class="bi bi-person-badge-fill"></i> Data Siswa</a></div>
        <div class="nav-item"><a href="{{ route('admin.mutasi.index') }}" class="{{ request()->is('admin/mutasi*')?'active':'' }}"><i class="bi bi-arrow-left-right"></i> Mutasi Siswa</a></div>
        <div class="nav-item"><a href="{{ route('admin.alumni.index') }}" class="{{ request()->is('admin/alumni*')?'active':'' }}"><i class="bi bi-mortarboard-fill"></i> Alumni</a></div>
        <div class="nav-item"><a href="{{ route('admin.rombel.index') }}" class="{{ request()->is('admin/rombel*')?'active':'' }}"><i class="bi bi-diagram-3-fill"></i> Data Rombel</a></div>
        <div class="nav-item"><a href="{{ route('admin.guru.index') }}" class="{{ request()->is('admin/guru*')?'active':'' }}"><i class="bi bi-person-workspace"></i> Data Guru & GTK</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='mapel-jurnal'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-book nav-gi"></i><span class="nav-gl">Akademik</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='mapel-jurnal'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('admin.mapel.index') }}" class="{{ request()->is('admin/mapel*')?'active':'' }}"><i class="bi bi-book-fill"></i> Mata Pelajaran</a></div>
        <div class="nav-item"><a href="{{ route('admin.jurnal.index') }}" class="{{ request()->is('admin/jurnal*')?'active':'' }}"><i class="bi bi-journal-text"></i> Jurnal Mengajar</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='absensi'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-clipboard-check nav-gi"></i><span class="nav-gl">Absensi</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='absensi'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('admin.absensi.index') }}" class="{{ request()->is('admin/absensi') || request()->is('admin/absensi?*') ? 'active' : '' }}"><i class="bi bi-clipboard-check-fill"></i> Daftar Absensi</a></div>
        <div class="nav-item"><a href="{{ route('admin.absensi.create') }}" class="{{ request()->is('admin/absensi/create')?'active':'' }}"><i class="bi bi-plus-circle-fill"></i> Input Absensi</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='bk'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-shield-exclamation nav-gi"></i><span class="nav-gl">BK / Kesiswaan</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='bk'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('bk.jenis-pelanggaran.index') }}" class="{{ request()->is('bk/jenis-pelanggaran*')?'active':'' }}"><i class="bi bi-list-check"></i> Jenis Pelanggaran</a></div>
        <div class="nav-item"><a href="{{ route('bk.pelanggaran.index') }}" class="{{ request()->is('bk/pelanggaran*')?'active':'' }}"><i class="bi bi-exclamation-triangle-fill"></i> Input Pelanggaran</a></div>
        <div class="nav-item"><a href="{{ route('bk.rekap.index') }}" class="{{ request()->is('bk/rekap*')?'active':'' }}"><i class="bi bi-bar-chart-fill"></i> Rekap Poin</a></div>
        <div class="nav-item"><a href="{{ route('bk.poin-positif.index') }}" class="{{ request()->is('bk/poin-positif') || (request()->is('bk/poin-positif*') && !request()->is('bk/poin-positif/jenis*') && !request()->is('bk/poin-positif/rekap*')) ? 'active' : '' }}"><i class="bi bi-star-fill"></i> Poin Kebaikan</a></div>
        <div class="nav-item"><a href="{{ route('bk.poin-positif.jenis') }}" class="{{ request()->is('bk/poin-positif/jenis*')?'active':'' }}"><i class="bi bi-list-check"></i> Master Kegiatan</a></div>
        <div class="nav-item"><a href="{{ route('bk.poin-positif.rekap') }}" class="{{ request()->is('bk/poin-positif/rekap*')?'active':'' }}"><i class="bi bi-bar-chart-fill"></i> Rekap Net Poin</a></div>
    </div>
</div>

{{-- IZIN BERENCANA --}}
<div class="nav-group">
    <div class="nav-group-header {{ $ag==='izin'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-file-earmark-check nav-gi"></i><span class="nav-gl">Izin Berencana</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='izin'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('izin.index') }}" class="{{ request()->is('izin') || (request()->is('izin*') && !request()->is('izin/laporan*')) ? 'active' : '' }}"><i class="bi bi-file-earmark-text-fill"></i> Daftar Izin</a></div>
        <div class="nav-item"><a href="{{ route('izin.laporan') }}" class="{{ request()->is('izin/laporan*')?'active':'' }}"><i class="bi bi-bar-chart-fill"></i> Laporan Izin</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='prestasi'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-trophy nav-gi"></i><span class="nav-gl">Prestasi</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='prestasi'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('prestasi.index') }}" class="{{ (request()->is('prestasi') || request()->is('prestasi?*')) && !request()->is('prestasi/kategori*') ? 'active' : '' }}"><i class="bi bi-trophy-fill"></i> Data Prestasi</a></div>
        <div class="nav-item"><a href="{{ route('prestasi.kategori.index') }}" class="{{ request()->is('prestasi/kategori*')?'active':'' }}"><i class="bi bi-tags-fill"></i> Kategori Prestasi</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='tiket'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-ticket-perforated nav-gi"></i><span class="nav-gl">Tiket & Pengaduan</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='tiket'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('tiket.index') }}" class="{{ request()->is('tiket*')?'active':'' }}"><i class="bi bi-ticket-perforated-fill"></i> Daftar Tiket</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='laporan'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-file-earmark-bar-graph nav-gi"></i><span class="nav-gl">Laporan</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='laporan'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('laporan.jurnal') }}" class="{{ request()->is('laporan/jurnal*')?'active':'' }}"><i class="bi bi-journal-text"></i> Laporan Jurnal</a></div>
        <div class="nav-item"><a href="{{ route('laporan.pelanggaran') }}" class="{{ request()->is('laporan/pelanggaran*')?'active':'' }}"><i class="bi bi-exclamation-triangle-fill"></i> Laporan Pelanggaran</a></div>
        <div class="nav-item"><a href="{{ route('laporan.prestasi') }}" class="{{ request()->is('laporan/prestasi*')?'active':'' }}"><i class="bi bi-trophy-fill"></i> Laporan Prestasi</a></div>
        <div class="nav-item"><a href="{{ route('laporan.tiket') }}" class="{{ request()->is('laporan/tiket*')?'active':'' }}"><i class="bi bi-ticket-perforated-fill"></i> Laporan Tiket</a></div>
        <div class="nav-sep">— Absensi —</div>
        <div class="nav-item"><a href="{{ route('laporan.absensi.rekap-kelas') }}" class="{{ request()->is('laporan/absensi/rekap-kelas*')?'active':'' }}"><i class="bi bi-table"></i> Rekap Per Kelas</a></div>
        <div class="nav-item"><a href="{{ route('laporan.absensi.detail-kelas') }}" class="{{ request()->is('laporan/absensi/detail-kelas*')?'active':'' }}"><i class="bi bi-calendar3"></i> Detail Per Kelas</a></div>
        <div class="nav-item"><a href="{{ route('laporan.absensi.rekap-siswa') }}" class="{{ request()->is('laporan/absensi/rekap-siswa*')?'active':'' }}"><i class="bi bi-person-lines-fill"></i> Rekap Per Siswa</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='dapodik'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-cloud-arrow-down nav-gi"></i><span class="nav-gl">Dapodik</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='dapodik'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('admin.dapodik.pengaturan') }}" class="{{ request()->is('admin/dapodik/pengaturan*')?'active':'' }}"><i class="bi bi-gear-wide-connected"></i> Pengaturan Dapodik</a></div>
        <div class="nav-item"><a href="{{ route('admin.dapodik.tarik') }}" class="{{ request()->is('admin/dapodik/tarik*')?'active':'' }}"><i class="bi bi-cloud-download-fill"></i> Tarik Data</a></div>
        <div class="nav-item"><a href="{{ route('admin.semester.wizard') }}" class="{{ request()->is('admin/semester*')?'active':'' }}"><i class="bi bi-arrow-repeat"></i> Pergantian Semester</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='sistem'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-gear nav-gi"></i><span class="nav-gl">Sistem</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='sistem'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('admin.settings.index') }}" class="{{ request()->is('admin/settings*')?'active':'' }}"><i class="bi bi-building-gear"></i> Pengaturan Sekolah</a></div>
        <div class="nav-item"><a href="{{ route('admin.akun.index') }}" class="{{ request()->is('admin/akun*')?'active':'' }}"><i class="bi bi-person-lock"></i> Generate Akun</a></div>
        <div class="nav-item"><a href="{{ route('admin.users.index') }}" class="{{ request()->is('admin/users*')?'active':'' }}"><i class="bi bi-people-fill"></i> Manajemen User</a></div>
        <div class="nav-item"><a href="{{ route('profile.edit') }}" class="{{ request()->is('profile*')?'active':'' }}"><i class="bi bi-person-circle"></i> Profil Saya</a></div>
        <div class="nav-item"><a href="{{ route('admin.backup.index') }}" class="{{ request()->is('admin/backup*')?'active':'' }}"><i class="bi bi-database-fill-gear"></i> Backup & Restore</a></div>
        <div class="nav-item"><a href="{{ route('admin.activity-log.index') }}" class="{{ request()->is('admin/activity-log*')?'active':'' }}"><i class="bi bi-journal-text"></i> Log Aktivitas</a></div>
    </div>
</div>

@include('partials._sidebar_script')
