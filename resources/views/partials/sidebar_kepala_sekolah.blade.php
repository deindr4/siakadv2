{{-- resources/views/partials/sidebar_kepala_sekolah.blade.php --}}
{{-- Role: kepala_sekolah --}}
@php
    $ag = '';
    if (request()->is('dashboard/kepala-sekolah')) $ag = 'utama';
    elseif (request()->is('izin*'))                $ag = 'izin';
    elseif (request()->is('prestasi*'))            $ag = 'prestasi';
    elseif (request()->is('tiket*'))               $ag = 'tiket';
    elseif (request()->is('laporan*'))             $ag = 'laporan';
    elseif (request()->is('profile*'))             $ag = 'sistem';
@endphp
@include('partials._sidebar_style')

<div class="nav-group">
    <div class="nav-group-header {{ $ag==='utama'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-grid nav-gi"></i><span class="nav-gl">Utama</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='utama'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('dashboard.kepala_sekolah') }}" class="{{ request()->is('dashboard/kepala-sekolah')?'active':'' }}"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></div>
    </div>
</div>

{{-- IZIN BERENCANA — kepsek approve/tolak --}}
<div class="nav-group">
    <div class="nav-group-header {{ $ag==='izin'?'open':'' }}" onclick="toggleGroup(this)">
        <i class="bi bi-file-earmark-check nav-gi"></i><span class="nav-gl">Izin Berencana</span><i class="bi bi-chevron-down nav-gc"></i>
    </div>
    <div class="nav-group-body {{ $ag==='izin'?'open':'' }}">
        <div class="nav-item"><a href="{{ route('izin.index') }}" class="{{ request()->is('izin') || (request()->is('izin*') && !request()->is('izin/laporan*')) ? 'active' : '' }}"><i class="bi bi-file-earmark-text-fill"></i> Daftar Izin</a></div>
        <div class="nav-item"><a href="{{ route('izin.index', ['status' => 'pending']) }}" class="{{ request()->is('izin') && request('status') === 'pending' ? 'active' : '' }}"><i class="bi bi-hourglass-split"></i> Menunggu Approval</a></div>
        <div class="nav-item"><a href="{{ route('izin.laporan') }}" class="{{ request()->is('izin/laporan*')?'active':'' }}"><i class="bi bi-bar-chart-fill"></i> Laporan Izin</a></div>
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
        <div class="nav-item"><a href="{{ route('laporan.tiket') }}" class="{{ request()->is('laporan/tiket*')?'active':'' }}"><i class="bi bi-ticket-perforated-fill"></i> Laporan Tiket</a></div>
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
