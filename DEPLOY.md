# 🚀 Deploy Laravel ke Niagahoster (hPanel)

Panduan singkat untuk men-deploy aplikasi Laravel ke hosting Niagahoster menggunakan **hPanel**.

---

## ✅ 1. Persiapan Awal

- Akun Niagahoster aktif dengan domain dan hosting (minimal paket **Premium**)
- Aplikasi Laravel sudah siap (tested secara lokal)
- Akses ke hPanel Niagahoster

---

## 📁 2. Struktur Folder Upload

1. **Buat folder baru** di root (misal: `laravel-app`)
2. **Upload semua isi project Laravel** (kecuali folder `public`) ke `laravel-app/`
3. **Upload isi folder `public/`** ke `public_html/`

```
/
├── laravel-app/         # folder berisi semua file Laravel (app, routes, vendor, dll)
└── public_html/         # upload isi folder `public/` ke sini
```

---

## ⚙️ 3. Konfigurasi `index.php`

Edit file `index.php` di `public_html/` agar mengenali struktur Laravel:

```php
<?php

require __DIR__ . '/../laravel-app/vendor/autoload.php';
$app = require_once __DIR__ . '/../laravel-app/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
```

---

## 🗂️ 4. Konfigurasi `.env`

1. Salin `.env.example` jadi `.env` di folder `laravel-app`
2. Ubah konfigurasi database sesuai dengan info MySQL di hPanel:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username_db
DB_PASSWORD=password_db
APP_URL=https://namadomainkamu.com
```

---

## 🛠️ 5. Jalankan Migrasi (Opsional)

Gunakan fitur **Terminal di hPanel** atau buat file PHP untuk menjalankan:

```bash
php artisan migrate --seed
php artisan storage:link
```

Jika tidak ada akses terminal, buat file `artisan.php` di `public_html`:

```php
<?php
require __DIR__ . '/../laravel-app/vendor/autoload.php';
$app = require_once __DIR__ . '/../laravel-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo '<pre>';
$kernel->call('migrate');
echo '</pre>';
```

---

## 📸 6. Simbolik Link `storage`

Karena `php artisan storage:link` tidak bisa dijalankan di shared hosting, buat manual:

1. Masuk ke **File Manager**
2. Buka `public_html`
3. Buat shortcut (symlink) dari:

```
public_html/storage → ../laravel-app/storage/app/public
```

Jika tidak bisa, **copy manual** isi `storage/app/public` ke `public_html/storage`

---

## 🔐 7. Permissions

Set permission `755` untuk semua folder, dan `644` untuk semua file.  
Pastikan folder ini writable:

- `laravel-app/bootstrap/cache`
- `laravel-app/storage`

---

## ✅ 8. Selesai

Akses aplikasi kamu melalui domain:  
📍 `https://namadomainkamu.com`

---

## 📌 Catatan Tambahan

- Hindari menyimpan `.env`, `vendor/`, dan file sensitif lainnya di `public_html`
- Gunakan `.htaccess` untuk keamanan tambahan jika diperlukan
- Pastikan SSL aktif di domain

