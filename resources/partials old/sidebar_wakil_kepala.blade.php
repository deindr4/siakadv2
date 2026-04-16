{{-- resources/views/partials/sidebar_wakil_kepala.blade.php --}}
{{-- Role: wakil_kepala_sekolah --}}
{{-- Akses: dashboard, prestasi (view), tiket (view only), laporan (semua kecuali tiket), profile --}}
@php
    $ag = '';
    if (request()->is('dashboard/wakil-kepala')) $ag = 'utama';
    elseif (request()->is('prestasi*'))          $ag = 'prestasi';
    elseif (request()->is('tiket*'))             $ag = 'tiket';
    elseif (request()->is('laporan*'))           $ag = 'laporan';
    elseif (request()->is('profile*'))           $ag = 'sistem';
@endphp
@include('partials._sidebar_style')

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='utama'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-grid nav-gi"></i><span class="nav-gl">Utama</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='utama'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('dashboard.wakil_kepala') }}" class="{{ request()->is('dashboard/wakil-kepala')?'active':'' }}"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='prestasi'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-trophy nav-gi"></i><span class="nav-gl">Prestasi</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='prestasi'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('prestasi.index') }}" class="{{ request()->is('prestasi*')?'active':'' }}"><i class="bi bi-trophy-fill"></i> Data Prestasi</a></div>
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
