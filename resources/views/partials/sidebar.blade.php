{{-- resources/views/partials/sidebar.blade.php --}}
{{-- Include ini di semua view/layout, otomatis load sidebar sesuai role --}}
@php
    $role = auth()->user()->getRoleNames()->first() ?? '';
    $sidebarMap = [
        'admin'                => 'partials.sidebar_admin',
        'kepala_sekolah'       => 'partials.sidebar_kepala_sekolah',
        'wakil_kepala_sekolah' => 'partials.sidebar_wakil_kepala',
        'guru'                 => 'partials.sidebar_guru',
        'bk'                   => 'partials.sidebar_bk',
        'tata_usaha'           => 'partials.sidebar_tata_usaha',
        'siswa'                => 'partials.sidebar_siswa',
    ];
    $sidebarView = $sidebarMap[$role] ?? 'partials.sidebar_admin';
@endphp

@include($sidebarView)
