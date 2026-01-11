# Dashboard Riyadlul Huda

Sistem Manajemen Pondok Pesantren berbasis web untuk mengelola data santri, pendidikan, keuangan, dan administrasi secara terpadu.

![Laravel](https://img.shields.io/badge/Laravel-11-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange?logo=mysql)
![License](https://img.shields.io/badge/License-MIT-green)

---

## Production Access

Aplikasi dapat diakses secara live di:
**[https://dashboard.riyadlulhuda.my.id](https://dashboard.riyadlulhuda.my.id)**

---

## Screenshot

### Halaman Login

![Login Page](docs/screenshots/login-page.png)

Halaman login dengan tampilan modern dan sistem Multi-Role. Pengguna dapat memilih role (Admin, Pendidikan, Sekretaris, Bendahara) dan login dengan kredensial masing-masing.

---

### Dashboard Admin

![Dashboard Admin](docs/screenshots/dashboard-admin.png)

Pusat Kontrol Sistem - Admin memiliki akses penuh ke seluruh modul:

-   Statistik total santri, kelas, mata pelajaran, dan dana
-   Quick access ke modul Sekretaris, Bendahara, Pendidikan
-   Informasi sistem (Database, Status, Framework, Developer)

---

### Dashboard Pendidikan

![Dashboard Pendidikan](docs/screenshots/dashboard-pendidikan.png)

Modul Akademik untuk mengelola:

-   Grafik rata-rata nilai per kelas dan sebaran nilai
-   Statistik santri aktif, kelas, dan mata pelajaran
-   Input nilai, absensi, dan monitoring setoran hafalan

---

### Dashboard Sekretaris

![Dashboard Sekretaris](docs/screenshots/dashboard-sekretaris.png)

Manajemen Data Santri:

-   Pengelolaan profil santri putra dan putri
-   Data asrama, kelas, dan kobong
-   Fitur mutasi, kenaikan kelas, dan laporan santri

---

### Dashboard Bendahara

![Dashboard Bendahara](docs/screenshots/dashboard-bendahara.png)

Sistem Keuangan Terintegrasi:

-   Manajemen saldo kas, pemasukan, dan pengeluaran
-   Monitoring tunggakan Syahriah (SPP)
-   Grafik keuangan interaktif per asrama/kelas
-   Fitur penggajian pegawai

---

## Fitur Utama

### Multi-Role Authentication

-   **Admin**: Akses penuh ke semua modul sistem.
-   **Pendidikan**: Pengelolaan akademik, nilai, dan rapor.
-   **Sekretaris**: Pengelolaan data santri dan administrasi.
-   **Bendahara**: Pengelolaan keuangan dan penggajian.

### Modul Pendidikan

-   Input & rekap nilai semester dengan sistem Smart Scoring.
-   Absensi berbagai program (Sorogan, Tahajud, dll).
-   Tracking setoran hafalan (Talaran).
-   Cetak Rapor & Ijazah otomatis dalam format PDF.

### Modul Sekretaris

-   Manajemen Database Santri (Import/Export).
-   Prosedur mutasi dan kenaikan kelas massal.
-   Penempatan asrama dan kobong santri.

### Modul Bendahara

-   Pengelolaan Syahriah (SPP Bulanan).
-   Integrasi notifikasi tagihan via WhatsApp.
-   Laporan arus kas dan penggajian staff.

---

## Instalasi

### Persyaratan

-   PHP 8.2
-   Composer
-   MySQL 8.0
-   Node.js

### Langkah Instalasi

```bash
# Clone repository
git clone https://github.com/mahinutsmannawawi20-svg/dashboard-riyadlul-huda.git
cd dashboard-riyadlul-huda

# Install dependencies
composer install
npm install
npm run build

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migrasi & seeder
php artisan migrate --seed

# Jalankan server development
php artisan serve
```

---

## Akun Default

| Role       | Email                    | Password     |
| ---------- | ------------------------ | ------------ |
| Admin      | *\*\*\*@******.com | **\*\*\*\*** |
| Pendidikan | *\*\*\*@******.com | **\*\*\*\*** |
| Sekretaris | *\*\*\*@******.com | **\*\*\*\*** |
| Bendahara  |*\*\*\*@******.com | **\*\*\*\*** |

---

## Struktur Proyek

```
├── app/                  # Logic aplikasi (Controllers, Models, Services)
├── database/             # Migrasi dan Seeder database
├── resources/views/      # Templates antarmuka pengguna
├── public/               # Assets publik (CSS, JS, Images)
└── routes/               # Definisi rute aplikasi
```

---

## Deployment ke Production

### Optimasi

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Developer

**Mahin Utsman Nawawi, S.H.**

---

## License

Distributed under the MIT License.
