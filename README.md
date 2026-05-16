<p align="center">
  <img src="https://github.com/aldiruskita/Project-smart-bali-report/blob/2664a5789dc2c0aa38bad56f895ac0224e27e59a/Foto_Smart_Bali_Report.png" width="600" alt="Smart Bali Report Banner">
</p>
<h1 align="center">🌴 Smart Bali Report — Sistem Citizen Reporting</h1>

<p align="center">
  <strong>Harmonisasi Pelayanan Publik Berbasis Tradisi Tri Hita Karana</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite 7">
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="MIT License">
</p>

---

## 👤 Informasi Pengembang

| | Detail |
|---|---|
| **Nama** | **Made Aldi Ruskita Salahin** |
| **Proyek** | Smart Bali Report (Sistem Citizen Reporting) |
| **Teknologi** | Laravel 12, PHP 8.2+, MySQL, Vite, Tailwind CSS |

---

## 📖 Tentang Proyek

**Smart Bali Report** adalah platform pelaporan warga (*Citizen Reporting System*) berbasis web yang dirancang untuk mewujudkan Bali yang bersih, aman, dan tertata melalui partisipasi aktif warga dalam menjaga fasilitas publik. Sistem ini terinspirasi dari filosofi **Tri Hita Karana** — tiga penyebab keharmonisan dalam budaya Bali.

Platform ini memungkinkan warga untuk melaporkan permasalahan di lingkungan mereka, seperti kerusakan infrastruktur, masalah kebersihan, keamanan, dan lainnya. Setiap laporan kemudian diverifikasi oleh admin, ditugaskan kepada petugas lapangan, dan dipantau hingga masalah terselesaikan secara transparan.

---

## 🎬 Demo Aplikasi

<p align="center">
  <a href="">
    <img src="https://img.shields.io/badge/▶️_Lihat_Demo_Aplikasi-Click_Here-success?style=for-the-badge" alt="Demo Aplikasi">
  </a>
</p>

<p align="center">
  <em>Klik tombol di atas untuk melihat video demo aplikasi Smart Bali Report.</em>
</p>

---

## ✨ Fitur Utama

### 🏠 Halaman Beranda (Landing Page)
- **Hero Section** premium dengan gambar Candi Bentar Bali dan gradient overlay
- **Statistik Real-time** (Total Laporan, Sedang Diproses, Laporan Selesai) dalam layout Bento Card
- **Kategori Pengaduan** dengan grid responsif dan ikon dinamis
- **Cara Kerja** — panduan 3 langkah interaktif (Buat Laporan → Tim Tindak Lanjut → Masalah Terselesaikan)
- **Peta Interaktif** menggunakan Leaflet.js dengan marker cluster untuk memantau sebaran laporan secara real-time
- **Laporan Terkini** — tampilan card laporan terbaru dengan status badge, vote count, dan komentar

### 📝 Sistem Pelaporan
- **Buat Laporan** — formulir lengkap dengan judul, deskripsi, kategori, foto/media, dan lokasi GPS
- **Klasifikasi AI Otomatis** — integrasi OpenAI untuk mengklasifikasi kategori laporan secara otomatis
- **Upload Media** — dukungan upload gambar/foto sebagai bukti laporan
- **Geolocation** — integrasi peta untuk menandai lokasi masalah dengan koordinat (latitude/longitude)
- **Laporan Anonim** — opsi untuk melaporkan tanpa menampilkan identitas
- **Status Tracking** — pantau status laporan secara real-time:
  - `Pending` → `Terverifikasi` → `Sedang Diproses` → `Selesai`
  - `Ditolak` (jika laporan tidak valid)

### 👍 Interaksi Sosial
- **Voting/Upvote** — warga dapat memberikan dukungan pada laporan yang relevan
- **Komentar** — diskusi terbuka pada setiap laporan
- **Rating** — warga dapat memberikan rating setelah masalah selesai ditangani

### 🔔 Sistem Notifikasi
- Notifikasi real-time untuk perubahan status laporan
- Tandai notifikasi sebagai sudah dibaca (satu per satu atau semua sekaligus)

### 👤 Profil Pengguna
- Edit profil (nama, email, telepon, avatar)
- Ganti password
- Riwayat laporan pribadi (*My Reports*)

### 🛡️ Panel Admin
- **Dashboard** — statistik keseluruhan dengan grafik/chart interaktif (API chart data)
- **Manajemen Laporan** — verifikasi, ubah status, assign petugas, hapus laporan
- **Manajemen Pengguna** — CRUD pengguna, atur role (tambah, edit, hapus user)
- **Assign Petugas** — tugaskan petugas lapangan untuk menangani laporan tertentu

### 🔧 Panel Petugas Lapangan (Officer)
- **Dashboard Petugas** — daftar tugas yang ditugaskan
- **Detail Tugas** — lihat detail lengkap tugas beserta laporan terkait
- **Accept/Reject Tugas** — terima atau tolak tugas dengan alasan
- **Update Progress** — perbarui progress penanganan (0–100%)
- **Upload Lampiran** — upload foto dokumentasi (sebelum, sesudah, progres)
- **Komentar Tugas** — komunikasi internal terkait tugas

### 🤖 Integrasi AI (Artificial Intelligence)
- Klasifikasi otomatis kategori laporan menggunakan AI
- Skor kepercayaan (confidence score) hasil klasifikasi
- Preview klasifikasi sebelum submit laporan

### 🗺️ Peta & Geolokasi
- Peta interaktif menggunakan **Leaflet.js** + **OpenStreetMap**
- **Marker Clustering** untuk mengelompokkan titik laporan yang berdekatan
- Legenda status (Mendesak, Dalam Proses, Selesai) dengan kode warna
- Popup informasi detail pada setiap marker

---

## 🏗️ Arsitektur & Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend** | Laravel 12 (PHP 8.2+) |
| **Frontend** | Blade Templates, Vanilla CSS, Tailwind CSS 4.0 |
| **Build Tool** | Vite 7.x |
| **Database** | MySQL 8.x |
| **Map** | Leaflet.js + OpenStreetMap + MarkerCluster |
| **AI** | OpenAI API (Klasifikasi Otomatis) |
| **Icons** | Google Material Symbols (Rounded) |
| **Font** | Noto Serif, Public Sans |
| **Testing** | Pest PHP 3.x |

---

## 🗄️ Struktur Database

```bash
├── users                 # Pengguna (warga, petugas, admin_desa, super_admin)
├── categories            # Kategori pengaduan
├── reports               # Laporan warga
│   ├── report_media      # Media/foto laporan
│   ├── report_logs       # Log perubahan status laporan
│   ├── comments          # Komentar pada laporan
│   ├── votes             # Vote/dukungan pada laporan
│   └── ratings           # Rating penyelesaian laporan
├── assignments           # Penugasan petugas
├── tasks                 # Tugas petugas lapangan
│   ├── task_comments     # Komentar internal tugas
│   └── task_attachments  # Lampiran dokumentasi tugas
├── notifications         # Notifikasi pengguna
├── cache                 # Cache system
├── sessions              # Session management
└── jobs / failed_jobs    # Queue & Background Jobs
```

---

## 👥 Sistem Role & Hak Akses

| Role | Hak Akses |
|---|---|
| **Warga** | Buat laporan, lihat laporan, vote, komentar, rating, kelola profil |
| **Petugas** | Semua hak warga + terima/tolak tugas, update progress, upload dokumentasi |
| **Admin Desa** | Semua hak petugas + dashboard admin, kelola laporan, assign petugas, kelola pengguna, verifikasi tugas |
| **Super Admin** | Semua hak admin desa + akses penuh ke seluruh sistem |

---

## 🚀 Instalasi & Setup

### Prasyarat

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL 8.x
- XAMPP / Laragon / Valet (opsional)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/aldiruskita/Project-smart-bali-report.git

# 2. Masuk ke folder project
cd Project-smart-bali-report

# 3. Install dependensi PHP
composer install

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi database pada file .env

# 7. Jalankan migrasi database
php artisan migrate

# 8. Jalankan seeder (opsional)
php artisan db:seed

# 9. Buat symbolic link storage
php artisan storage:link

# 10. Install dependency frontend
npm install

# 11. Build asset frontend
npm run build
```

### Menjalankan Aplikasi

```bash
# Menjalankan seluruh service sekaligus
composer dev

# Atau manual
php artisan serve
php artisan queue:listen
npm run dev
```

Aplikasi berjalan di:

```bash
http://localhost:8000
```

---

## 🔑 Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Super Admin | `superadmin@scrs.test` | `password` |
| Admin Desa | `admin@scrs.test` | `password` |
| Petugas Lapangan | `petugas@scrs.test` | `password` |
| Warga 1 | `budi@scrs.test` | `password` |
| Warga 2 | `siti@scrs.test` | `password` |

---

## ⚙️ Konfigurasi Environment

```env
APP_NAME="Smart Bali Report"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=porto1
DB_USERNAME=root
DB_PASSWORD=

AI_API_KEY=your-openai-api-key

QUEUE_CONNECTION=database
```

---

## 🎨 Desain & UI/UX

- Tema warna terinspirasi dari alam Bali
- Responsive untuk desktop, tablet, dan mobile
- Glassmorphism UI
- Material Symbols Rounded
- Micro animations & smooth transitions
- Modern dashboard layout dengan Bento Grid

---

## 📄 Lisensi

Proyek ini menggunakan lisensi [MIT License](https://opensource.org/licenses/MIT).

---

<p align="center">
  Dibuat dengan ❤️ oleh <strong>Made Aldi Ruskita Salahin</strong>
</p>

<p align="center">
  <em>Mewujudkan Bali yang bersih, aman, dan tertata melalui teknologi.</em>
</p>
