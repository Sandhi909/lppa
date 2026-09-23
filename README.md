# SISTEM MANAJEMEN KEMITRAAN BIMBEL LPPA

Aplikasi web PHP 8 Native + MySQL untuk LPPA/Yayasan dalam mengelola kerja sama dengan Mitra Bimbingan Belajar.
Teknologi: **PHP 8 Native, MySQL (PDO), HTML5, CSS3, Bootstrap 5, JavaScript Vanilla, Font Awesome 6, Chart.js 4**.
Tanpa framework, tanpa Node.js — siap dijalankan di **XAMPP** maupun **shared hosting** biasa.

## 1. Struktur Folder

```
lppa-bimbel/
├── index.php                  Pengalih ke login / dashboard sesuai role
├── config/
│   ├── database.php           Koneksi PDO (ubah host, nama DB, user, password)
│   └── app.php                Konfigurasi aplikasi, session, base_url()
├── includes/
│   ├── auth.php               Session, require_login(), require_role()
│   ├── functions.php          Helper: format Rupiah, CSRF, audit, rumus bisnis
│   ├── queries.php            Query siswa, filter, rekap poin & fee, pagination
│   ├── student_crud.php       Logika CRUD siswa (Admin & Mitra)
│   ├── student_form.php       Form tambah/edit siswa
│   ├── student_table.php      Tabel siswa
│   ├── students_page.php      Halaman siswa (dipakai Admin & Mitra)
│   ├── header.php / footer.php / navbar.php / sidebar.php
├── assets/
│   ├── css/style.css          Tema biru tua profesional, responsif
│   ├── js/app.js              Sidebar, modal hapus, kalkulasi otomatis
│   └── images/
├── uploads/                   Berkas unggahan (import)
├── auth/
│   ├── login.php              Halaman login
│   └── logout.php             Logout aman
├── admin/
│   ├── index.php              Dashboard Admin + Chart.js
│   ├── partners.php           CRUD Mitra + buat akun mitra
│   ├── recommenders.php       CRUD Wali Kelas / Wali Santri + akun + reset password
│   ├── students.php           Data seluruh siswa
│   ├── master.php             Tahun Ajaran, Unit, Program
│   ├── profit_sharing.php     Pengaturan bagi hasil Yayasan + riwayat
│   ├── fee_rules.php          Pengaturan fee bertingkat rekomendator
│   ├── users.php              Manajemen akun pengguna
│   ├── recap.php              Rekap rekomendator
│   └── audit.php              Riwayat aktivitas
├── mitra/
│   ├── index.php              Dashboard Mitra
│   ├── students.php           CRUD siswa milik mitra
│   ├── import.php             Import CSV/XLSX + pratinjau + validasi
│   └── template.php           Unduh template import
├── wali/
│   ├── index.php              Dashboard Wali (poin, fee)
│   ├── students.php           Daftar siswa rekomendasi
│   ├── fee.php                Rincian fee & rekap periode
│   └── _scope.php             Pembatas akses data milik sendiri
├── reports/
│   ├── index.php              Laporan + filter lengkap
│   ├── export.php             Export CSV / Excel
│   └── _scope.php             Pembangun data laporan
└── database/
    └── database.sql           Skema + data contoh
```

## 2. Panduan Instalasi (XAMPP)

1. Instal **XAMPP** (PHP 8.1+), jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
2. Salin seluruh folder aplikasi ke `C:\xampp\htdocs\lppa-bimbel`.
3. Buka **phpMyAdmin**: `http://localhost/phpmyadmin`.
4. Menu **Import** → pilih `database/database.sql` → **Go**.
   File tersebut otomatis membuat database `lppa_bimbel` beserta tabel dan data contoh.
5. Buka aplikasi: `http://localhost/lppa-bimbel/`.

### Shared hosting
1. Upload semua file ke `public_html` (atau subfolder).
2. Buat database MySQL dari cPanel, lalu import `database/database.sql`.
3. Sesuaikan `config/database.php` dengan kredensial hosting.

## 3. Konfigurasi Database

Edit `config/database.php`:

```php
define('DB_HOST', '127.0.0.1');   // localhost
define('DB_NAME', 'lppa_bimbel'); // nama database
define('DB_USER', 'root');        // user MySQL
define('DB_PASS', '');            // kosong pada XAMPP default
```

## 4. Akun Login Contoh

| Role        | Username          | Password  |
|-------------|-------------------|-----------|
| Admin LPPA  | `admin`           | `admin123`|
| Mitra Bimbel| `mitra_demo`      | `mitra123`|
| Wali Kelas  | `walikelas_demo`  | `wali123` |
| Wali Santri | `walisantri_demo` | `wali123` |

Semua password tersimpan sebagai `password_hash()` (bcrypt) dan diverifikasi dengan `password_verify()`.
Ganti password default setelah instalasi melalui menu **Akun Pengguna**.

## 5. Fitur yang Tersedia

**Admin LPPA** — dashboard lengkap dengan 10 kartu statistik dan 5 grafik Chart.js (siswa per mitra, pembayaran & pendapatan Yayasan per mitra, siswa per unit, status pembayaran, pendaftaran per bulan) beserta filter Tahun Ajaran/Mitra/Unit/periode; CRUD Mitra (tambah, ubah, nonaktifkan, hapus jika belum punya data, buat akun); CRUD Rekomendator (Wali Kelas & Wali Santri, buat akun, reset password, filter unit/jenis); pengelolaan seluruh data siswa; data master Tahun Ajaran, Unit, Program; pengaturan bagi hasil Yayasan; pengaturan fee bertingkat; manajemen akun pengguna; rekap rekomendator; laporan + export; riwayat aktivitas.

**Mitra Bimbel** — dashboard khusus datanya sendiri; CRUD siswa; pencarian & filter; import CSV/XLSX dengan pratinjau, validasi data kosong/duplikat, jumlah berhasil dan gagal beserta alasannya; unduh template; perbarui total pembayaran (status berubah otomatis); laporan & export miliknya sendiri.

**Wali Kelas / Wali Santri** — dashboard pribadi (jumlah siswa rekomendasi, total poin, total fee, fee periode berjalan), daftar siswa rekomendasi, rincian fee, rekap per periode. Data wali lain tidak dapat diakses.

### Aturan bisnis otomatis
- `Total Tagihan = Biaya Program − Diskon`
- `Sisa Pembayaran = Total Tagihan − Total Pembayaran`
- Status: `Belum Bayar` (bayar Rp0), `Cicilan` (0 < bayar < tagihan), `Lunas` (bayar ≥ tagihan) — tidak pernah diinput manual.
- `Pendapatan Yayasan = Total Pembayaran × Persentase Aktif` (default 15%, tersimpan di tabel `profit_sharing_rules`, dapat diubah kapan saja berikut masa berlaku dan riwayatnya).
- Poin: hanya Wali Kelas → Wali Kelas 1 poin. Ada Wali Santri → Wali Santri 1 poin, Wali Kelas 0,5 poin (mendukung 0,5 / 1,5 / 7,5 dst.).
- Fee diambil dari tabel `fee_rules` (bertingkat 1–3, 4–5, 6–9, 10–14, 15+ poin) — nominal tidak dikunci di kode.
- LPPA tidak mencatat metode pembayaran, bukti transfer, atau nomor transaksi. Mitra cukup memperbarui nilai **Total Pembayaran**.

## 6. Keamanan
PDO + prepared statement pada seluruh query, `password_hash`/`password_verify`, session dengan `httponly` + `session_regenerate_id`, proteksi role per halaman, CSRF token pada semua form penting, validasi input server-side, sanitasi output (`htmlspecialchars`), validasi ekstensi & ukuran berkas upload (maks 2 MB), logout aman, serta audit log setiap aksi penting.

## 7. Import XLSX (opsional)
Import CSV berjalan tanpa dependensi apa pun. Untuk XLSX, jalankan di folder aplikasi:
```
composer require phpoffice/phpspreadsheet
```
Aplikasi otomatis mendeteksi `vendor/autoload.php`.
