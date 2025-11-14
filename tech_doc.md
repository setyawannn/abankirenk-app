# Dokumen Teknis: Aplikasi Manajemen Yearbook (AbankIrenk)

Dokumen ini merinci arsitektur teknis dan rencana implementasi untuk aplikasi manajemen proyek yearbook "AbankIrenk", yang dibangun di atas kerangka kerja PHP prosedural MafemWok.

## 1. Tinjauan Sistem

*   **Aplikasi:** Sistem Manajemen Proses Bisnis (BPM) untuk alur kerja pembuatan yearbook.
*   **Kerangka Kerja:** MafemWok (PHP Prosedural Kustom).
*   **Pola Arsitektur:** Action-Data-Template (ADT).
*   **Database:** MySQL dengan konvensi penamaan `snake_case`.
*   **Frontend:** HTML dengan styling menggunakan Tailwind CSS v4.

## 2. Arsitektur Inti & Alur Kerja

Aplikasi ini akan secara ketat mengikuti pola ADT yang disediakan oleh MafemWok.

*   **Actions (`app/actions/`):**
    *   Setiap request HTTP akan ditangani oleh sebuah fungsi di dalam file action.
    *   Contoh: Request ke `GET /manajer-marketing/prospek` akan ditangani oleh fungsi `index_action()` di dalam `app/actions/manajer_marketing/prospek_action.php`.
    *   Tugas Action: Memvalidasi input (jika ada), memanggil fungsi dari lapisan Data, menyiapkan array `$data` untuk view, dan memanggil `view('nama_template', $data)`.

*   **Data (`app/data/`):**
    *   Semua interaksi database (CRUD) dan logika bisnis terpusat di sini.
    *   Contoh: Fungsi `prospek_get_all()` di `app/data/prospek_data.php` akan mengambil semua data prospek dari database.
    *   **Penting:** Logika untuk menghasilkan ID order kustom (`ORDyymmddxxx`) akan dibuat di sini, misalnya dalam fungsi `generate_order_id()` di `app/data/order_data.php`. Fungsi ini akan mengambil `sequence` terakhir untuk tanggal hari ini dan menambahkannya.

*   **Templates (`app/templates/`):**
    *   Hanya bertanggung jawab untuk menampilkan data.
    *   Menggunakan sintaks mirip Blade dari MafemWok (`{{ }}`, `@if`, `@foreach`, dll).
    *   Struktur folder akan mengikuti modul dan peran, contoh: `app/templates/manajer_marketing/prospek/index.php`.

## 3. Peran Pengguna & Kontrol Akses (ACL)

Sistem akan memiliki 8 peran yang berbeda, yang didefinisikan dalam `ENUM` di tabel `users`.

*   `manajer_marketing`
*   `tim_marketing`
*   `manajer_produksi`
*   `desainer`
*   `project_officer`
*   `tim_percetakan`
*   `customer_service`
*   `klien`

Kontrol akses akan diimplementasikan melalui middleware kustom MafemWok.
*   **Mekanisme:** Router akan memanggil fungsi `run_middleware()` dari `core/middleware.php` untuk setiap rute yang dilindungi.
*   **Logika:** Middleware akan memeriksa `$_SESSION['user']['role']` dan membandingkannya dengan peran yang diizinkan untuk rute tersebut. Jika tidak cocok, pengguna akan dialihkan atau menerima halaman 403 (Forbidden).
*   **Implementasi:** Setiap rute akan memiliki parameter middleware (contoh: `'auth:manajer_marketing'`).

## 4. Struktur Rute & Pemetaan ke Actions

Definisi rute dalam `routes/web.php` akan mengikuti konvensi yang memetakan URL secara langsung ke struktur folder di `app/actions/`. Pengelompokan berdasarkan peran pengguna akan menjadi panduan utama.

**Konsep Pemetaan:**
URL dengan format `/nama-peran/nama-modul/{id}` akan dipetakan ke file action yang sesuai.

**Contoh Struktur Pemetaan:**

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


Pendekatan ini membuat file `routes/web.php` tetap ringkas dan mudah dipelihara, karena logika pengorganisasiannya terletak pada struktur folder yang intuitif.

## 5. Implementasi Kunci

*   **UI Dinamis (Sidebar):**
    *   Sebuah file `config/menu.php` akan dibuat.
    *   File ini akan berisi array multi-dimensi yang mendefinisikan struktur menu untuk setiap peran.
    *   Layout utama (`layouts/admin.php`) akan memuat file konfigurasi ini, mengambil menu yang sesuai dengan `$_SESSION['user']['role']`, dan me-rendernya secara dinamis.

*   **Notifikasi (Flash Message):**
    *   Fungsi helper `flash_message('success', 'Data berhasil disimpan!')` akan digunakan di dalam Action setelah operasi berhasil.
    *   Layout utama akan memiliki script yang memeriksa flash message di `$_SESSION`. Jika ada, script akan menampilkan notifikasi toast modern (misalnya menggunakan library JavaScript seperti Toastify.js) lalu menghapus pesan dari session.

*   **Form dengan Searchable Dropdown:**
    *   Pada form input prospek, kolom `id_sekolah` akan diimplementasikan sebagai dropdown yang dapat dicari (Select2, TomSelect, atau sejenisnya).
    *   Dropdown ini akan diisi melalui panggilan AJAX ke sebuah endpoint khusus (misal: `GET /api/sekolah`) yang mengembalikan data sekolah dalam format JSON.
    *   Akan ada tombol "Tambah Sekolah Baru" yang membuka modal untuk menambah data sekolah baru tanpa meninggalkan halaman.

## 6. Keamanan

Selain ACL berbasis peran, beberapa langkah keamanan dari kerangka kerja MafemWok akan ditegakkan:
1.  **Password Hashing:** Semua password pengguna **wajib** di-hash menggunakan `password_hash()` dan diverifikasi dengan `password_verify()`.
2.  **Proteksi SQL Injection:** Semua query database yang melibatkan input pengguna **wajib** menggunakan Prepared Statements.
3.  **Proteksi XSS:** Selalu gunakan sintaks `{{ $variabel }}` untuk menampilkan data. Gunakan `{!! $variabel !!}` hanya jika Anda 100% yakin kontennya aman.
4.  **Tugas Tambahan:**
    *   **Proteksi CSRF:** Perlu diimplementasikan untuk semua form yang mengubah data (POST, PUT, DELETE).
    *   **Validasi File Upload:** Perlu ada validasi ketat pada tipe file (MIME type), ukuran, dan sanitasi nama file untuk mencegah eksekusi file berbahaya.

