# 📘 Sistem Informasi Perawatan Kendaraan Bermotor

Aplikasi Perawatan Kendaraan Bermotor menggunakan Laravel

---

## 📝 1. Informasi Umum

- **Nama Proyek**: Sistem Informasi Perawatan Kendaraan Bermotor
- **Framework**: Laravel 11
- **Admin Panel**: Filament
- **Tanggal Mulai**: [26/04/2025]
- **Versi**: v3.2

---

## ⚙️ 2. Persyaratan Sistem

### Server

- PHP ≥ 8.3
- Composer
- MySQL / MariaDB
- Node.js + NPM

### Library Tambahan

- `filament/filament`
- `spatie/laravel-permission`
- `maatwebsite/excel`

---

## 🛠️ 3. Instalasi & Setup

### a. Clone Repository

```bash
git clone https://github.com/namauser/nama-proyek.git
cd nama-proyek
```

### b. Install Dependency

```bash
composer install
cp .env.example .env
php artisan key:generate
```

#### c. Konfigurasi Database
Buat database baru di MySQL, lalu perbarui file .env:
```bash
DB_DATABASE=pemeliharaan-kendaraan
DB_USERNAME=root
DB_PASSWORD=
```

### d. Migrasi & Seeder

```bash
php artisan migrate --seed
```

### e. Instalasi User Super Admin

```bash
php artisan make:user
php artisan shield:super-admin
php artisan shield:generate --all
```

## 🛠️ 4. Fitur dalam aplikasi

### Master :

- Tahun
- Jenis Perawatan
- Merk Kendaraan

### Fitur Utama :

- Dashboard
- User
- Role
- Kendaraan
- Perawatan
- Laporan


