{{-- resources/views/partials/sidebar_guru.blade.php --}}
{{-- Role: guru --}}
{{-- Akses: jurnal (guru.*), absensi (guru.*), prestasi (view+create+input), tiket (view+create), laporan (jurnal,pelanggaran,prestasi,absensi), profile --}}
@php
    $ag = '';
    if (request()->is('dashboard/guru'))               $ag = 'utama';
    elseif (request()->is('guru/jurnal*'))             $ag = 'jurnal';
    elseif (request()->is('guru/absensi*'))            $ag = 'absensi';
    elseif (request()->is('prestasi*'))                $ag = 'prestasi';
    elseif (request()->is('tiket*'))                   $ag = 'tiket';
    elseif (request()->is('laporan*'))                 $ag = 'laporan';
    elseif (request()->is('profile*'))                 $ag = 'sistem';
@endphp
@include('partials._sidebar_style')

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='utama'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-grid nav-gi"></i><span class="nav-gl">Utama</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='utama'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('dashboard.guru') }}" class="{{ request()->is('dashboard/guru')?'active':'' }}"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='jurnal'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-journal-text nav-gi"></i><span class="nav-gl">Jurnal Mengajar</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='jurnal'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('guru.jurnal.index') }}" class="{{ request()->is('guru/jurnal') || request()->is('guru/jurnal?*') ? 'active' : '' }}"><i class="bi bi-journal-check"></i> Daftar Jurnal</a></div>
        <div class="nav-item"><a href="{{ route('guru.jurnal.create') }}" class="{{ request()->is('guru/jurnal/create')?'active':'' }}"><i class="bi bi-plus-circle-fill"></i> Input Jurnal</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='absensi'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-clipboard-check nav-gi"></i><span class="nav-gl">Absensi</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='absensi'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('guru.absensi.index') }}" class="{{ request()->is('guru/absensi') || request()->is('guru/absensi?*') ? 'active' : '' }}"><i class="bi bi-clipboard-check-fill"></i> Daftar Absensi</a></div>
        <div class="nav-item"><a href="{{ route('guru.absensi.create') }}" class="{{ request()->is('guru/absensi/create')?'active':'' }}"><i class="bi bi-plus-circle-fill"></i> Input Absensi</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='prestasi'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-trophy nav-gi"></i><span class="nav-gl">Prestasi</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='prestasi'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('prestasi.index') }}" class="{{ (request()->is('prestasi') || request()->is('prestasi?*')) && !request()->is('prestasi/create') ? 'active' : '' }}"><i class="bi bi-trophy-fill"></i> Data Prestasi</a></div>
        <div class="nav-item"><a href="{{ route('prestasi.create') }}" class="{{ request()->is('prestasi/create')?'active':'' }}"><i class="bi bi-plus-circle-fill"></i> Input Prestasi</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='tiket'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-ticket-perforated nav-gi"></i><span class="nav-gl">Tiket & Pengaduan</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='tiket'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('tiket.index') }}" class="{{ (request()->is('tiket') || request()->is('tiket?*')) && !request()->is('tiket/create') ? 'active' : '' }}"><i class="bi bi-ticket-perforated-fill"></i> Tiket Saya</a></div>
        <div class="nav-item"><a href="{{ route('tiket.create') }}" class="{{ request()->is('tiket/create')?'active':'' }}"><i class="bi bi-plus-circle-fill"></i> Buat Tiket</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='laporan'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-file-earmark-bar-graph nav-gi"></i><span class="nav-gl">Laporan</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='laporan'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('laporan.jurnal') }}" class="{{ request()->is('laporan/jurnal*')?'active':'' }}"><i class="bi bi-journal-text"></i> Laporan Jurnal</a></div>
        <div class="nav-item"><a href="{{ route('laporan.prestasi') }}" class="{{ request()->is('laporan/prestasi*')?'active':'' }}"><i class="bi bi-trophy-fill"></i> Laporan Prestasi</a></div>
        <div class="nav-sep">— Absensi —</div>
        <div class="nav-item"><a href="{{ route('laporan.absensi.rekap-kelas') }}" class="{{ request()->is('laporan/absensi/rekap-kelas*')?'active':'' }}"><i class="bi bi-table"></i> Rekap Per Kelas</a></div>
        <div class="nav-item"><a href="{{ route('laporan.absensi.detail-kelas') }}" class="{{ request()->is('laporan/absensi/detail-kelas*')?'active':'' }}"><i class="bi bi-calendar3"></i> Detail Per Kelas</a></div>
        <div class="nav-item"><a href="{{ route('laporan.absensi.rekap-siswa') }}" class="{{ request()->is('laporan/absensi/rekap-siswa*')?'active':'' }}"><i class="bi bi-person-lines-fill"></i> Rekap Per Siswa</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='sistem'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-gear nav-gi"></i><span class="nav-gl">Sistem</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='sistem'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('profile.edit') }}" class="{{ request()->is('profile*')?'active':'' }}"><i class="bi bi-person-circle"></i> Profil Saya</a></div>
    </div>
</div>

@include('partials._sidebar_script')
