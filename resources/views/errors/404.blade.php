@extends('layouts.app')
@section('title', '404 - Halaman Tidak Ditemukan')
@section('content')
<div class="container" style="max-width:600px;text-align:center;padding:80px 20px;">
    <div style="margin-bottom:32px;">
        <div style="width:120px;height:120px;border-radius:50%;background:rgba(212,175,55,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
            <span class="material-symbols-rounded" style="font-size:56px;color:var(--accent);font-variation-settings:'FILL' 0;">explore_off</span>
        </div>
        <h1 style="font-family:'Noto Serif',serif;font-size:4rem;font-weight:800;color:var(--primary);line-height:1;margin-bottom:8px;">404</h1>
        <h2 style="font-family:'Noto Serif',serif;font-size:1.3rem;font-weight:600;color:var(--primary);margin-bottom:12px;">Halaman Tidak Ditemukan</h2>
        <p style="color:var(--text-muted);font-size:15px;line-height:1.6;max-width:400px;margin:0 auto 32px;">
            Maaf, halaman yang Anda cari tidak ada atau mungkin telah dipindahkan.
        </p>
    </div>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ url('/') }}" class="btn-primary" style="padding:14px 32px;border-radius:14px;gap:8px;">
            <span class="material-symbols-rounded" style="font-size:18px;">home</span> Kembali ke Beranda
        </a>
        <a href="{{ route('reports.index') }}" class="btn-secondary" style="padding:14px 32px;border-radius:14px;gap:8px;">
            <span class="material-symbols-rounded" style="font-size:18px;">description</span> Lihat Laporan
        </a>
    </div>
</div>
@endsection
