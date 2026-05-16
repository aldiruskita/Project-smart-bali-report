@extends('layouts.app')
@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div style="margin-bottom:24px;">
            <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:12px;padding:0 16px;font-weight:700;">Menu Admin</div>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="material-symbols-rounded" style="font-size:20px;">dashboard</span> Dashboard
        </a>
        <a href="{{ route('admin.reports') }}" class="sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
            <span class="material-symbols-rounded" style="font-size:20px;">description</span> Kelola Laporan
        </a>
        <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <span class="material-symbols-rounded" style="font-size:20px;">group</span> Kelola User
        </a>

    </aside>
    <main class="admin-content">
        @yield('admin-content')
    </main>
</div>
@endsection
