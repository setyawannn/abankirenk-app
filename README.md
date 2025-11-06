# MafemWok - Kerangka Kerja PHP Prosedural Sederhana

Selamat datang di MafemWok (nama bisa Anda sesuaikan), sebuah kerangka kerja PHP ringan yang dibangun dengan pendekatan **prosedural murni** (tanpa kelas/OOP di bagian inti). Kerangka kerja ini mengadopsi pola **Action-Data-Template (ADT)** untuk organisasi kode yang bersih dan mudah dipahami.

**Filosofi:** Menyediakan struktur dan *helper* modern (seperti routing, *template engine*, konfigurasi `.env`, middleware) tanpa memaksakan paradigma OOP, ideal untuk pengembangan cepat atau proyek yang secara spesifik membutuhkan pendekatan prosedural.

---

## ✨ Fitur Utama

* **Pola ADT (Action-Data-Template):** Organisasi kode yang intuitif.
* **Routing Bersih:** Mendukung URL cantik (`pretty URLs`) dan metode GET/POST.
* **Template Engine Mirip Blade:** Sintaks bersih `{{ }}`, `{!! !!}`, `@extends`, `@section`, `@yield`, `@include`, `@if`, `@foreach`, `@auth`, `@guest`, `@php`, `@push`, `@stack`. Termasuk *caching* otomatis.
* **Middleware:** Melindungi rute berdasarkan status otentikasi atau peran.
* **Konfigurasi Terpusat:** Menggunakan file `.env` dan folder `config/`.
* **Helper Functions:** Fungsi bantuan praktis (`dd`, `config`, `url`, `redirect`, `view`, `log_message`, `flash_message`, `auth`).
* **Database (MySQLi Prosedural):** Koneksi database sederhana via konfigurasi.
* **Error Handling:** Menangkap *exception* dan *error* PHP, menampilkan halaman *debug* (local) atau error 500 (production).
* **Logging:** Sistem *logging* sederhana ke file.
* **Integrasi Aset:** Pengaturan siap pakai untuk Tailwind CSS v4 CLI (`npm run dev`).
* **Perintah CLI:** Perintah `composer template:clear` untuk membersihkan *cache template*.
* **Keamanan Dasar:** Proteksi folder via `.htaccess`, *password hashing*, *output escaping*.

---

## 📋 Persyaratan

* PHP 8.0 atau lebih tinggi.
* Web Server (Apache direkomendasikan dengan `mod_rewrite` aktif).
* Composer (Manajer dependensi PHP).
* Node.js dan npm (Untuk manajemen aset front-end/Tailwind).
* Database MySQL/MariaDB.

---

## 🚀 Instalasi & Setup

1.  **Clone Repository:**
    ```bash
    git clone [URL_REPOSITORY_ANDA] nama-proyek
    cd nama-proyek
    ```

2.  **Install Dependensi PHP:**
    ```bash
    composer install
    ```

3.  **Konfigurasi Environment:**
    * Salin file `.env.example` (jika ada) menjadi `.env`.
    * Buka file `.env` dan sesuaikan variabel berikut:
        * `APP_NAME`: Nama aplikasi Anda.
        * `APP_ENV`: Set ke `local` untuk development, `production` untuk server live.
        * `BASE_URL`: URL lengkap ke folder `public` Anda (misal: `http://localhost/nama-proyek/public`). **Penting:** Sertakan `/public` di akhir.
        * `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Kredensial database Anda.

4.  **Install Dependensi Node.js (untuk Tailwind):**
    ```bash
    npm install
    ```

5.  **Atur Izin Folder `storage`:** Pastikan web server memiliki izin tulis ke folder `storage` dan subfoldernya (`templates`, `logs`). Di Linux/macOS:
    ```bash
    sudo chown -R www-data:www-data storage  # Ganti www-data jika perlu (misal: daemon)
    sudo chmod -R 775 storage
    ```

6.  **Konfigurasi Web Server (Apache):**
    * Pastikan `mod_rewrite` aktif.
    * Konfigurasikan *Virtual Host* atau arahkan *Document Root* server Anda ke folder `public/` di dalam proyek. Ini sangat penting untuk keamanan dan agar URL cantik berfungsi.

7.  **Jalankan Kompilasi Aset (Development):** Buka terminal baru dan jalankan:
    ```bash
    npm run dev
    ```
    Biarkan proses ini berjalan selama development untuk kompilasi otomatis Tailwind CSS.

8.  **Siapkan Database:** Impor struktur database Anda (jika ada) dan jalankan kueri `INSERT` (atau *seeder*) untuk data awal (seperti pengguna admin).

9. **Penting: Buat Storage Link**

    Untuk memastikan file yang di-upload (seperti gambar dari CKEditor) dapat diakses oleh publik, Anda harus membuat *symbolic link*.

    Jalankan perintah Composer berikut:

    ```bash
    composer storage:link
    ```

    **PENTING (Khusus Pengguna Windows):**
    Di Windows, pembuatan *symbolic link* (`mklink`) memerlukan hak akses Administrator. Anda **HARUS** menjalankan perintah ini dari terminal (CMD, PowerShell, atau Git Bash) yang telah dibuka dengan **"Run as administrator"**.

---

## Server Requirements & Konfigurasi

Proyek ini memiliki beberapa dependensi di sisi server agar dapat berjalan dengan baik.

### 1. Ekstensi PHP: GD Library (Wajib)

Proyek ini menggunakan library `intervention/image` untuk kompresi gambar otomatis saat upload. Library ini memerlukan **GD Library** dari PHP.

#### ➡️ Saat Development (XAMPP):

1.  Buka **XAMPP Control Panel**.
2.  Di baris **Apache**, klik **Config** > **`PHP (php.ini)`**.
3.  Cari (Ctrl+F) baris `;extension=gd`.
4.  **Hapus titik koma (`;`)** di depannya sehingga menjadi `extension=gd`.
5.  **Simpan** file `php.ini`.
6.  **Stop** dan **Start** ulang *service* Apache agar perubahan terbaca.

#### ➡️ Saat Deployment (Linux Server):

Biasanya, `php-gd` sudah terinstal. Jika belum, Anda perlu menginstalnya secara manual (contoh untuk Ubuntu/Debian):

```bash
sudo apt-get update
sudo apt-get install php-gd
sudo systemctl restart apache2 # atau 'php-fpm' jika Anda menggunakan Nginx

```

---

## 📁 Struktur Folder

Struktur folder yang jelas, teliti, dan sudah mengadopsi penamaan Action-Data-Template (ADT) untuk kerangka kerja PHP prosedural kita:

```
/nama-proyek/
│
├── 📁 app/                    # Folder inti logika aplikasi
│   ├── 📁 actions/             # (Pengganti Controllers) Berisi file logika penanganan request.
│   │   │                     # Contoh: home_action.php, auth_action.php
│   │   └── 📁 manajer_marketing/ # Sub-folder untuk mengelompokkan action berdasarkan peran/modul
│   │       └── prospek_action.php
│   │
│   ├── 📁 data/               # (Pengganti Models) Berisi file logika interaksi database.
│   │   │                     # Contoh: user_data.php, prospek_data.php
│   │   └── ...
│   │
│   └── 📁 templates/          # (Pengganti Views) Berisi file template HTML (.php).
│       ├── 📁 layouts/         # Template dasar/induk (cth: admin.php, app.php)
│       │   └── admin.php
│       ├── 📁 partials/       # Komponen UI kecil yang bisa dipakai ulang (cth: sidebar, footer)
│       │   ├── admin_sidebar.php
│       │   └── admin_topbar.php
│       ├── 📁 auth/           # Template khusus otentikasi (login, register)
│       │   ├── login.php
│       │   └── register.php
│       ├── 📁 errors/         # Template halaman error (403, 404, 500)
│       │   ├── 403.php
│       │   ├── 404.php
│       │   └── 500.php
│       ├── 📁 dashboard/       # Template khusus dashboard
│       │   └── index.php
│       └── 📁 manajer_marketing/ # Template khusus modul Manajer Marketing
│           └── 📁 manajemen_prospek/
│               └── index.php
│
├── 📁 config/               # File konfigurasi aplikasi.
│   ├── app.php             # Konfigurasi umum (nama app, env, base_url)
│   └── database.php        # Konfigurasi kredensial database
│
├── 📁 core/                 # File inti/mesin kerangka kerja (semua prosedural).
│   ├── database.php        # Fungsi koneksi DB (mysqli_connect)
│   ├── exceptions.php      # Fungsi penanganan error & exception global
│   ├── functions.php       # Fungsi helper global (dd, config, url, view, dll)
│   ├── middleware.php      # Fungsi logika middleware (auth, guest, admin)
│   └── router.php          # Fungsi routing & dispatch request
│
├── 📁 public/               # <-- !! WEB SERVER DOCUMENT ROOT !!
│   ├── .htaccess           # Aturan rewrite (ke index.php) & Allow Access
│   ├── index.php           # Titik masuk utama (Front Controller)
│   ├── css/                # File CSS hasil kompilasi
│   │   └── style.css
│   ├── js/                 # File JavaScript (cth: jquery.js)
│   │   └── jquery.js
│   └── assets/             # (Opsional) Folder untuk gambar, font, dll.
│
├── 📁 resources/            # Aset sumber (source assets) sebelum kompilasi.
│   └── css/
│       └── app.css         # File input utama untuk Tailwind CSS
│
├── 📁 routes/               # Definisi pemetaan URL ke Action.
│   └── web.php
│
├── 📁 scripts/              # Skrip CLI untuk tugas-tugas development.
│   └── clear-template-cache.php
│
├── 📁 storage/              # Folder penyimpanan (cache, logs) - HARUS WRITABLE.
│   ├── logs/               # File log aplikasi.
│   │   └── app.log
│   └── templates/          # Cache file template yang sudah di-compile.
│
├── 📁 vendor/               # Dependensi PHP dari Composer (diabaikan Git).
│
├── .env                    # Variabel environment (kredensial, dll) - JANGAN DI-COMMIT.
├── .gitignore              # Daftar file/folder yang diabaikan oleh Git.
├── .htaccess               # Aturan keamanan root (Deny from all).
├── composer.json           # Konfigurasi Composer (dependensi PHP, skrip CLI).
├── package.json            # Konfigurasi npm (dependensi Node.js, skrip dev).
└── README.md               # Dokumentasi proyek.

```

---

## 💡 Konsep Inti: Action - Data - Template (ADT)

Kerangka kerja ini sengaja menghindari terminologi MVC untuk menekankan sifat proseduralnya.

* **Action (`app/actions/`)**: Menggantikan Controller. Berisi file-file PHP dengan **fungsi-fungsi** yang menangani permintaan HTTP. Tugasnya: menerima input, memanggil logika dari lapisan *Data*, menyiapkan data, dan memanggil *Template* untuk ditampilkan. Contoh fungsi: `products_list_action()`, `user_create_action()`.
* **Data (`app/data/`)**: Menggantikan Model. Berisi file-file PHP dengan **fungsi-fungsi** yang bertanggung jawab untuk semua interaksi dengan database (CRUD) dan logika bisnis terkait data. Fungsi-fungsi ini dipanggil oleh *Actions*. Contoh: `prospek_get_all()`, `user_find_by_email()`.
* **Template (`app/templates/`)**: Menggantikan View. Berisi file-file `.php` dengan HTML dan sintaks *template engine* mirip Blade. Tugasnya hanya menampilkan data yang diberikan oleh *Action*.

---

## ⚙️ Komponen Inti (`core/`)

Folder `core/` berisi "mesin" dari kerangka kerja ini, semuanya ditulis dalam gaya prosedural.

### 1. Router (`router.php`)
* Bertindak sebagai *dispatcher* utama setelah `public/index.php`.
* Membaca URL yang masuk (sudah dibersihkan dari subfolder).
* Mencocokkannya dengan definisi rute di `routes/web.php`.
* Memanggil *middleware* yang terdaftar untuk rute tersebut.
* Memuat file *Action* yang sesuai dan memanggil fungsi *Action* yang benar.
* Menangani error 404 (Not Found) jika rute tidak cocok.

### 2. Functions (`functions.php`) - Helper Global
File ini berisi fungsi-fungsi global yang bisa dipanggil dari mana saja.
* `dd(...$vars)`: *Dump and Die*. Menampilkan `var_dump` variabel dengan format bagus lalu menghentikan skrip.
* `config(string $key)`: Mengambil nilai dari file konfigurasi di `config/` menggunakan notasi titik (cth: `config('database.host')`).
* `env_get(string $key, $default = null)`: Mengambil nilai dari variabel `.env` dengan aman (memberikan nilai default jika tidak ada).
* `url(string $path = '')`: Membuat URL absolut berdasarkan `BASE_URL` di `.env` (cth: `url('/login')`).
* `redirect(string $to)`: Melakukan *redirect* ke URL internal menggunakan fungsi `url()`.
* `view(string $viewName, array $data = [])`: Merender *template* menggunakan *template engine* kita.
* `compile_view(string $viewName)`: Bagian dari *template engine* yang meng-*compile* file *template* ke PHP biasa dan menyimpannya di *cache*. **Nonaktifkan cache otomatis saat `APP_ENV=local`**.
* `log_message(string $level, string $message)`: Mencatat pesan ke `storage/logs/app.log`.
* `flash_message(string $key, string $message = null)`: Menetapkan atau mengambil pesan *flash* dari *session* (pesan yang hanya muncul sekali).
* `auth()`: Mengambil data pengguna yang sedang login dari `$_SESSION['user']`.

### 3. Template Engine (di dalam `functions.php`)
* Meng-*compile* sintaks mirip Blade di `app/templates/` menjadi PHP biasa dan menyimpannya di `storage/templates/`.
* **Sintaks yang Didukung:**
    * `{{ $variable }}`: `echo htmlspecialchars($variable);` (Aman dari XSS)
    * `{!! $variable !!}`: `echo $variable;` (Tidak aman, gunakan hati-hati)
    * `{{-- Komentar --}}`: Komentar yang tidak akan dirender.
    * `@extends('layouts.nama')`: Menggunakan file *layout*.
    * `@section('nama') ... @endsection`: Mendefinisikan blok konten.
    * `@yield('nama', 'default_value')`: Menampilkan konten dari `@section`.
    * `@include('partials.nama')`: Memasukkan konten file *template* lain.
    * `@if (kondisi) ... @elseif (kondisi) ... @else ... @endif`: Struktur kondisional.
    * `@foreach ($items as $item) ... @endforeach`: Struktur perulangan.
    * `@auth ... @endauth`: Menampilkan konten jika pengguna sudah login.
    * `@guest ... @endguest`: Menampilkan konten jika pengguna belum login.
    * `@php ... @endphp`: Menulis kode PHP mentah.
    * `@push('nama_stack') ... @endpush`: Menambahkan konten (biasanya `<script>` atau `<style>`) ke "tumpukan".
    * `@stack('nama_stack')`: Merender semua konten yang di-*push* ke tumpukan tersebut (biasanya di *layout* utama).

### 4. Middleware (`middleware.php`)
* Berisi fungsi `run_middleware($name)` yang dipanggil oleh *router*.
* Fungsi ini melakukan pengecekan berdasarkan nama *middleware* (`'auth'`, `'guest'`, `'admin'`).
* Jika kondisi tidak terpenuhi, ia akan melakukan *redirect* atau memanggil fungsi `abort_403()` (Akses Ditolak).

### 5. Database (`database.php`)
* Berisi fungsi `db_connect()` yang membuat koneksi database menggunakan **MySQLi prosedural**.
* Mengambil kredensial dari `config('database.*')` (yang bersumber dari `.env`).
* Menggunakan pola *singleton* sederhana (koneksi hanya dibuat sekali per *request*).

### 6. Exceptions (`exceptions.php`)
* Berisi `custom_exception_handler()` dan `custom_error_handler()`.
* Handler ini didaftarkan di `public/index.php` menggunakan `set_exception_handler()` dan `set_error_handler()`.
* Tujuannya: menangkap semua *error* dan *exception* yang tidak tertangkap, mencatatnya ke *log* (`log_message`), dan menampilkan halaman *debug* detail (jika `APP_ENV=local`) atau halaman error 500 generik (jika `APP_ENV=production`).

---

## 🗺️ Routing (`routes/web.php`)

* File ini adalah tempat Anda mendefinisikan semua URL aplikasi Anda.
* Gunakan fungsi `route_get()` dan `route_post()`.
* **Format:** `route_method(string $uri, array $action, ?string $middleware = null)`
    * `$uri`: Path URL setelah `BASE_URL` (cth: `/users`, `/products/{id}`). *Placeholder* seperti `{id}` belum didukung di versi ini.
    * `$action`: Array berisi dua elemen:
        1.  `string`: Path ke file *action* relatif dari `app/` (cth: `'actions/user_action.php'`).
        2.  `string`: Nama fungsi *action* yang akan dipanggil (cth: `'index_action'`).
    * `$middleware`: (Opsional) Nama *middleware* yang harus dijalankan sebelum *action* (cth: `'auth'`, `'admin'`).

---

## 🎨 Aset Statis (CSS & JavaScript)

* **Tailwind CSS:**
    * File sumber utama ada di `resources/css/app.css`. Di sinilah Anda menempatkan direktif `@tailwind`, `@theme` (konfigurasi v4), dan CSS kustom Anda.
    * Jalankan `npm run dev` untuk meng-*compile* `app.css` menjadi `public/css/style.css`.
    * Sertakan `style.css` di *layout* utama Anda menggunakan `{{ url('css/style.css') }}`.
* **JavaScript:**
    * Letakkan file JS Anda (seperti `jquery.js` atau skrip kustom) di dalam `public/js/`.
    * Muat di *layout* utama menggunakan `{{ url('js/nama_file.js') }}`.
    * Gunakan `@push('scripts')` untuk menambahkan JS spesifik halaman dari *template* anak.

---

## 💾 Penyimpanan (`storage/`)

* **`storage/templates/`**: Direktori ini menyimpan versi *cache* dari file *template* Anda yang sudah di-*compile*. **Jangan di-commit ke Git.** Kerangka kerja akan otomatis membuatnya jika tidak ada. Folder ini harus bisa ditulisi oleh web server.
* **`storage/logs/`**: Menyimpan file log `app.log`. **Jangan di-commit ke Git.** Folder ini harus bisa ditulisi oleh web server.

---

## 🛠️ Perintah CLI

* **`composer template:clear`**: Menjalankan skrip `scripts/clear-template-cache.php` untuk menghapus semua file *cache template* di `storage/templates/`. Berguna saat Anda melakukan perubahan pada *template* tetapi tidak melihat perubahannya di browser.

---

## 🔒 Keamanan

* **Document Root:** Pastikan web server Anda dikonfigurasi untuk menunjuk ke folder `public/`. Ini mencegah akses langsung ke file inti aplikasi.
* **.htaccess:** Gunakan dua file `.htaccess`:
    * Di **root proyek:** Berisi `Deny from all` untuk memblokir akses web secara default.
    * Di **folder `public/`:** Berisi `Allow from all` dan aturan *rewrite* untuk mengarahkan semua permintaan ke `index.php`.
* **Password Hashing:** Logika registrasi dan login **wajib** menggunakan `password_hash()` dan `password_verify()`.
* **SQL Injection:** Gunakan **Prepared Statements** (via `mysqli_prepare`, `mysqli_stmt_bind_param`, `mysqli_stmt_execute`) untuk semua kueri database yang melibatkan input pengguna.
* **Cross-Site Scripting (XSS):** *Template engine* secara default menggunakan `htmlspecialchars()` pada sintaks `{{ }}` untuk meng-*escape* output. Gunakan `{!! !!}` hanya jika Anda 100% yakin outputnya aman.
* **CSRF Protection:** *Belum diimplementasikan*. Perlu ditambahkan jika Anda menangani form sensitif.
* **File Uploads:** *Belum diimplementasikan*. Perlu penanganan khusus untuk validasi dan penyimpanan yang aman.

---

Semoga dokumentasi ini membantu Anda atau orang lain dalam menggunakan dan mengembangkan kerangka kerja MafemWok!