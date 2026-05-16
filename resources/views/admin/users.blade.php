@extends('layouts.admin')
@section('title', 'Kelola User')
@section('admin-content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h1 style="font-family:'Noto Serif',serif;font-size:1.5rem;font-weight:700;color:var(--primary);display:flex;align-items:center;gap:12px;"><span class="material-symbols-rounded" style="font-size:28px;color:var(--accent);">group</span> Kelola User</h1>
    <button class="btn-primary" id="btnTambahUser" style="gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">person_add</span> Tambah User</button>
</div>

{{-- Filters --}}
<div style="background:white;border:1px solid var(--glass-border);border-radius:16px;padding:16px;margin-bottom:24px;">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;">
        <div style="flex:1;min-width:160px;">
            <input type="text" name="search" class="form-input" placeholder="Cari nama/email..." value="{{ request('search') }}">
        </div>
        <select name="role" class="form-select" style="width:auto;">
            <option value="">Semua Role</option>
            <option value="warga" {{ request('role')=='warga'?'selected':'' }}>Warga</option>
            <option value="petugas" {{ request('role')=='petugas'?'selected':'' }}>Petugas</option>
            <option value="admin_desa" {{ request('role')=='admin_desa'?'selected':'' }}>Admin Desa</option>
            <option value="super_admin" {{ request('role')=='super_admin'?'selected':'' }}>Super Admin</option>
        </select>
        <button type="submit" class="btn-primary btn-sm" style="gap:6px;"><span class="material-symbols-rounded" style="font-size:16px;">search</span> Filter</button>
    </form>
</div>

{{-- Users Table --}}
<div style="background:white;border:1px solid var(--glass-border);border-radius:16px;overflow-x:auto;">
    <table class="data-table">
        <thead>
            <tr><th>User</th><th>Role</th><th>Telepon</th><th>Terdaftar</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    <div style="font-weight:600;">{{ $user->name }}</div>
                    <div style="font-size:12px;color:#64748b;">{{ $user->email }}</div>
                </td>
                <td>
                    <span class="badge" style="background:{{ match($user->role) { 'super_admin'=>'rgba(239,68,68,0.1)', 'admin_desa'=>'rgba(245,158,11,0.1)', 'petugas'=>'rgba(59,130,246,0.1)', default=>'rgba(107,114,128,0.1)' } }};color:{{ match($user->role) { 'super_admin'=>'#dc2626', 'admin_desa'=>'#b45309', 'petugas'=>'#1d4ed8', default=>'#4b5563' } }};">
                        {{ $user->role_label }}
                    </span>
                </td>
                <td style="color:var(--text-muted);">{{ $user->phone ?? '-' }}</td>
                <td style="color:var(--text-muted);font-size:13px;">{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}" style="display:flex;gap:4px;">
                            @csrf @method('PATCH')
                            <select name="role" class="form-select" style="width:auto;padding:6px 10px;font-size:12px;border-radius:8px;">
                                <option value="warga" {{ $user->role=='warga'?'selected':'' }}>Warga</option>
                                <option value="petugas" {{ $user->role=='petugas'?'selected':'' }}>Petugas</option>
                                <option value="admin_desa" {{ $user->role=='admin_desa'?'selected':'' }}>Admin Desa</option>
                                <option value="super_admin" {{ $user->role=='super_admin'?'selected':'' }}>Super Admin</option>
                            </select>
                            <input type="hidden" name="name" value="{{ $user->name }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">
                            <button type="submit" class="btn-primary btn-sm"><span class="material-symbols-rounded" style="font-size:16px;">check</span></button>
                        </form>
                        <button type="button" class="btn-hapus-user" data-id="{{ $user->id }}" style="background:#ef4444;color:white;border:none;border-radius:8px;padding:6px 14px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;"><span class="material-symbols-rounded" style="font-size:16px;">delete</span></button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Hidden delete forms OUTSIDE the table --}}
@foreach($users as $user)
<form id="form-delete-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:none;">
    @csrf
    <input type="hidden" name="_method" value="DELETE">
</form>
@endforeach

<div class="pagination" style="margin-top:20px;">{{ $users->withQueryString()->links('pagination.custom') }}</div>

{{-- Add User Modal --}}
<div class="modal-overlay" id="addUserModal">
    <div class="modal-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 class="modal-title" style="margin:0;display:flex;align-items:center;gap:8px;"><span class="material-symbols-rounded" style="font-size:22px;">person_add</span> Tambah User Baru</h2>
            <button id="btnCloseModal" style="background:none;border:none;color:var(--text-muted);cursor:pointer;display:flex;"><span class="material-symbols-rounded" style="font-size:22px;">close</span></button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Telepon</label>
                <input type="text" name="phone" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="warga">Warga</option>
                    <option value="petugas">Petugas</option>
                    <option value="admin_desa">Admin Desa</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required minlength="6">
            </div>
            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">Simpan</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete user buttons
    document.querySelectorAll('.btn-hapus-user').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            if (confirm('Yakin hapus user ini?')) {
                document.getElementById('form-delete-user-' + id).submit();
            }
        });
    });

    // Modal open/close
    var modal = document.getElementById('addUserModal');
    document.getElementById('btnTambahUser').addEventListener('click', function() {
        modal.classList.add('active');
    });
    document.getElementById('btnCloseModal').addEventListener('click', function() {
        modal.classList.remove('active');
    });
});
</script>
@endpush
