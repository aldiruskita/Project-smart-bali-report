@extends('layouts.app')
@section('title', '500 - Kesalahan Server')
@section('content')
<div class="container" style="max-width:600px;text-align:center;padding:80px 20px;">
    <div style="margin-bottom:32px;">
        <div style="width:120px;height:120px;border-radius:50%;background:rgba(239,68,68,0.06);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
            <span class="material-symbols-rounded" style="font-size:56px;color:var(--danger);font-variation-settings:'FILL' 0;">error</span>
        </div>
        <h1 style="font-family:'Noto Serif',serif;font-size:4rem;font-weight:800;color:var(--primary);line-height:1;margin-bottom:8px;">500</h1>
        <h2 style="font-family:'Noto Serif',serif;font-size:1.3rem;font-weight:600;color:var(--primary);margin-bottom:12px;">Terjadi Kesalahan Server</h2>
        <p style="color:var(--text-muted);font-size:15px;line-height:1.6;max-width:400px;margin:0 auto 32px;">
            Maaf, terjadi kesalahan pada server kami. Tim teknis sudah diberitahu. Silakan coba kembali dalam beberapa saat.
        </p>
    </div>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ url('/') }}" class="btn-primary" style="padding:14px 32px;border-radius:14px;gap:8px;">
            <span class="material-symbols-rounded" style="font-size:18px;">home</span> Kembali ke Beranda
        </a>
        <button onclick="location.reload()" class="btn-secondary" style="padding:14px 32px;border-radius:14px;gap:8px;cursor:pointer;">
            <span class="material-symbols-rounded" style="font-size:18px;">refresh</span> Coba Lagi
        </button>
    </div>
</div>
@endsection
