{{-- resources/views/partials/sidebar_siswa.blade.php --}}
{{-- Role: siswa --}}
@php
    $ag = '';
    if (request()->is('dashboard/siswa'))        $ag = 'utama';
    elseif (request()->is('siswa/absensi*'))     $ag = 'absensi';
    elseif (request()->is('siswa/jurnal*'))      $ag = 'jurnal';
    elseif (request()->is('siswa/pelanggaran*')) $ag = 'bk';
    elseif (request()->is('izin*'))              $ag = 'izin';
    elseif (request()->is('prestasi*'))          $ag = 'prestasi';
    elseif (request()->is('tiket*'))             $ag = 'tiket';
    elseif (request()->is('profile*'))           $ag = 'sistem';
@endphp
@include('partials._sidebar_style')

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='utama'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-grid nav-gi"></i><span class="nav-gl">Utama</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='utama'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('dashboard.siswa') }}" class="{{ request()->is('dashboard/siswa')?'active':'' }}"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='jurnal'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-journal-text nav-gi"></i><span class="nav-gl">Jurnal Kelas</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='jurnal'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('siswa.jurnal.kelas') }}" class="{{ request()->is('siswa/jurnal*')?'active':'' }}"><i class="bi bi-journal-bookmark-fill"></i> Jurnal Kelas Saya</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='absensi'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-clipboard-check nav-gi"></i><span class="nav-gl">Absensi</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='absensi'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('siswa.absensi') }}" class="{{ request()->is('siswa/absensi*')?'active':'' }}"><i class="bi bi-clipboard-data-fill"></i> Rekap Absensi Saya</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='bk'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-shield-exclamation nav-gi"></i><span class="nav-gl">BK / Kesiswaan</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='bk'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('siswa.pelanggaran.index') }}" class="{{ request()->is('siswa/pelanggaran*')?'active':'' }}"><i class="bi bi-exclamation-triangle-fill"></i> Pelanggaran Saya</a></div>
    </div>
</div>

{{-- IZIN BERENCANA --}}
<div class="nav-group">
    <div class="nav-group-header {{ $ag==='izin'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-file-earmark-check nav-gi"></i><span class="nav-gl">Izin Berencana</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='izin'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('izin.index') }}" class="{{ request()->is('izin') || (request()->is('izin/*') && !request()->is('izin/create')) ? 'active' : '' }}"><i class="bi bi-file-earmark-text-fill"></i> Izin Saya</a></div>
        <div class="nav-item"><a href="{{ route('izin.create') }}" class="{{ request()->is('izin/create')?'active':'' }}"><i class="bi bi-plus-circle-fill"></i> Ajukan Izin</a></div>
    </div>
</div>

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='prestasi'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-trophy nav-gi"></i><span class="nav-gl">Prestasi</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='prestasi'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('prestasi.index') }}" class="{{ (request()->is('prestasi') || request()->is('prestasi?*')) && !request()->is('prestasi/create') ? 'active' : '' }}"><i class="bi bi-trophy-fill"></i> Prestasi Saya</a></div>
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
    <div class="nav-group-header {{ $ag==='sistem'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-gear nav-gi"></i><span class="nav-gl">Sistem</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='sistem'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('profile.edit') }}" class="{{ request()->is('profile*')?'active':'' }}"><i class="bi bi-person-circle"></i> Profil Saya</a></div>
    </div>
</div>

@include('partials._sidebar_script')
