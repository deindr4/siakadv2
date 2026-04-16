@extends('layouts.app')

@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola semua pengguna sistem')

@section('sidebar-menu')
    @include('partials.sidebar_admin')
@endsection

@section('content')
<div class="page-title">
    <h1>👥 Manajemen User</h1>
    <p>Total {{ $users->total() }} pengguna terdaftar</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Pengguna</h3>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah User
        </a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#f59e0b);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span style="font-weight:600;">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td style="color:#64748b;">{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                @php
                                    $colors = [
                                        'admin'                => 'badge-danger',
                                        'kepala_sekolah'       => 'badge-primary',
                                        'wakil_kepala_sekolah' => 'badge-primary',
                                        'guru'                 => 'badge-success',
                                        'bk'                   => 'badge-warning',
                                        'tata_usaha'           => 'badge-warning',
                                        'siswa'                => 'badge-primary',
                                    ];
                                    $cls = $colors[$role->name] ?? 'badge-primary';
                                @endphp
                                <span class="badge {{ $cls }}">
                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                </span>
                            @endforeach
                        </td>
                        <td style="color:#94a3b8;font-size:12px;">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="btn btn-sm" style="background:#f1f5f9;color:#374151;">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="btn btn-sm" style="background:#ede9fe;color:#6366f1;">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="confirmDelete(this.closest('form'))">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            Belum ada user terdaftar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div style="padding:16px 22px;border-top:1px solid #f1f5f9;">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
