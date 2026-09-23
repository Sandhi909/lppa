# Revisi Sistem Kategori Peserta LPPA

## Ringkasan Perubahan

Sistem LPPA telah diperbarui untuk mendukung **Kategori Peserta** dengan dua tipe:
- **Reguler**: Menghasilkan poin dan fee rekomendator
- **Kategori Khusus**: TIDAK menghasilkan poin dan fee, tetapi tetap memiliki Wali Kelas/Wali Santri

## Perubahan Database

### Migration Script
File: `database/migration_kategori_peserta.php`

Menambahkan field `kategori_peserta` ke tabel `students`:
- Tipe: `ENUM('reguler', 'khusus')`
- Default: `'reguler'`
- Data existing otomatis di-set ke `'reguler'`

### Cara Menjalankan Migration
```bash
php database/migration_kategori_peserta.php
```

Pastikan MySQL/XAMPP berjalan sebelum menjalankan migration.

## Perubahan Form dan UI

### 1. Form Siswa (`includes/student_form.php`)
- Menambahkan dropdown "Kategori Peserta" dengan opsi: Reguler / Kategori Khusus
- Default: Reguler
- Form validation: Kategori Peserta wajib diisi
- Tooltip menjelaskan bahwa Kategori Khusus tidak menghasilkan poin/fee

### 2. Tabel Siswa (`includes/student_table.php`)
- Menambahkan kolom "Kategori" di tabel
- Badge warna:
  - Reguler: Badge hijau
  - Kategori Khusus: Badge kuning dengan icon bintang

### 3. Detail Siswa (`includes/students_page.php`)
- Menampilkan kategori peserta pada halaman detail siswa
- Badge visual yang sama dengan tabel

### 4. Filter Bar (`includes/queries.php`)
- Menambahkan filter "Kategori Peserta" di filter bar
- Opsi: Semua / Reguler / Kategori Khusus

## Perubahan Logic Backend

### 1. CRUD Siswa (`includes/student_crud.php`)
- Menambahkan `kategori_peserta` ke array data saat INSERT/UPDATE
- Default value: `'reguler'`

### 2. Import Admin (`admin/import.php`)
- Kolom CSV baru: "Kategori Peserta" (index 5)
- Format: isi dengan "khusus" atau "reguler" (case-insensitive)
- Default: reguler
- Update INSERT dan UPDATE query untuk menyimpan kategori_peserta

### 3. Import Mitra (`mitra/import.php`)
- Kolom CSV baru: "Kategori Peserta" (index 5)
- Format sama dengan import admin
- Update INSERT dan UPDATE query

### 4. Template CSV
- `admin/template.php`: Menambahkan kolom Kategori Peserta dengan contoh data
- `mitra/template.php`: Menambahkan kolom Kategori Peserta dengan contoh data

## Perubahan Logic Poin dan Fee

### 1. Rekap Rekomendator (`includes/queries.php`)
Fungsi `rekap_rekomendator()` diperbarui:
- Menambahkan pengecekan `kategori_peserta` sebelum menghitung poin
- Jika `kategori_peserta = 'khusus'`, maka:
  - Tidak menambah poin
  - Tidak menambah fee
- Menambahkan tracking:
  - `siswa_reguler`: jumlah siswa Reguler
  - `siswa_khusus`: jumlah siswa Kategori Khusus
  - `siswa`: total siswa (tetap terhitung)

### 2. Poin per Baris (`wali/_scope.php`)
Fungsi `poin_baris()` diperbarui:
- Cek `kategori_peserta` dari data siswa
- Jika `'khusus'`, return `0.0`
- Jika `'reguler'`, hitung poin normal

### 3. Dashboard Wali (`wali/index.php`)
- Update label: "Siswa Direkomendasikan" → "Total Siswa Terhubung"
- Menampilkan breakdown Reguler/Khusus di card Total Poin
- Menambahkan kolom "Kategori" di tabel siswa terbaru

### 4. Rekap Admin (`admin/recap.php`)
- Menambahkan kolom "Reguler" dan "Khusus" di tabel
- Update tfoot untuk menampilkan total Reguler dan Khusus
- Total poin dan fee hanya dari siswa Reguler

### 5. Dashboard Admin (`admin/index.php`)
- Menambahkan kolom "Reguler" dan "Khusus" di tabel rekomendator teratas

## Rules Bisnis

### Reguler
- Menghasilkan poin rekomendator (normal)
- Menghasilkan fee rekomendator (normal)
- Memiliki Wali Kelas/Wali Santri (wajib)
- Diikutsertakan dalam semua laporan dan dashboard

### Kategori Khusus
- **TIDAK** menghasilkan poin rekomendator (poin = 0)
- **TIDAK** menghasilkan fee rekomendator (fee = Rp0)
- **TETAP** memiliki Wali Kelas/Wali Santri (wajib)
- **TETAP** terlihat di daftar siswa Wali Kelas/Wali Santri
- **TETAP** dihitung dalam total siswa
- **TETAP** diikutsertakan dalam laporan umum (total siswa, biaya, dll)
- **TIDAK** dihitung dalam perhitungan poin dan fee

### Wali Kelas / Wali Santri
- Wajib diisi untuk SEMUA kategori (Reguler dan Kategori Khusus)
- Siswa Kategori Khusus tetap muncul di daftar siswa mereka
- Poin dan fee hanya dihitung dari siswa Reguler

### Tahun Ajaran
- Kategori Peserta mengikuti Tahun Ajaran
- Perhitungan poin dan fee selalu berdasarkan Tahun Ajaran aktif
- Tidak ada pencampuran data antar Tahun Ajaran

## Testing Checklist

### TEST 1: Reguler + Wali Kelas
- [ ] Buat siswa baru dengan Kategori: Reguler
- [ ] Pilih Wali Kelas
- [ ] Simpan
- **Expected:**
  - Siswa tersimpan
  - Wali Kelas tersimpan
  - Siswa muncul di daftar Wali Kelas
  - Poin dihitung (1 poin)
  - Fee dihitung

### TEST 2: Kategori Khusus + Wali Kelas
- [ ] Buat siswa baru dengan Kategori: Kategori Khusus
- [ ] Pilih Wali Kelas
- [ ] Simpan
- **Expected:**
  - Siswa tersimpan
  - Wali Kelas tersimpan
  - Siswa muncul di daftar Wali Kelas
  - Poin = 0
  - Fee = Rp0

### TEST 3: Kategori Khusus + Wali Santri
- [ ] Buat siswa baru dengan Kategori: Kategori Khusus
- [ ] Set PSAM = Ya
- [ ] Pilih Wali Santri
- [ ] Simpan
- **Expected:**
  - Siswa tersimpan
  - Wali Santri tersimpan
  - Siswa muncul di daftar Wali Santri
  - Poin = 0
  - Fee = Rp0

### TEST 4: Wali Kelas dengan Campuran
- [ ] Buat 5 siswa Reguler dengan Wali Kelas A
- [ ] Buat 2 siswa Kategori Khusus dengan Wali Kelas A
- **Expected:**
  - Total siswa terhubung = 7
  - Siswa Reguler = 5
  - Siswa Khusus = 2
  - Siswa menghasilkan poin = 5
  - Poin hanya dari 5 siswa Reguler

### TEST 5: Perubahan Kategori Antar Tahun Ajaran
- [ ] TA 2025/2026: Set siswa = Kategori Khusus
- [ ] Expected: Poin = 0
- [ ] TA 2026/2027: Ubah siswa = Reguler
- [ ] Expected: Poin dihitung normal
- **Expected:** Histori kedua TA tidak tercampur

### TEST 6: Import CSV
- [ ] Download template CSV
- [ ] Isi data dengan kategori_peserta = "khusus"
- [ ] Import file
- **Expected:**
  - Data berhasil diimport
  - Kategori tersimpan sebagai "khusus"
  - Poin = 0
  - Fee = Rp0

### TEST 7: Filter Kategori
- [ ] Buka halaman siswa
- [ ] Filter Kategori Peserta = Kategori Khusus
- **Expected:** Hanya menampilkan siswa Kategori Khusus
- [ ] Filter Kategori Peserta = Reguler
- **Expected:** Hanya menampilkan siswa Reguler

### TEST 8: Dashboard Wali
- [ ] Login sebagai Wali Kelas
- [ ] Lihat dashboard
- **Expected:**
  - Total Siswa Terhubung = semua siswa (Reguler + Khusus)
  - Reguler / Khusus breakdown terlihat
  - Total Poin hanya dari siswa Reguler
  - Tabel siswa menampilkan kolom Kategori

### TEST 9: Rekap Admin
- [ ] Buka Rekap Rekomendator
- **Expected:**
  - Kolom Total Siswa = semua siswa
  - Kolom Reguler = siswa Reguler
  - Kolom Khusus = siswa Kategori Khusus
  - Total Poin hanya dari siswa Reguler
  - Total Fee hanya dari siswa Reguler

### TEST 10: Edit Siswa
- [ ] Edit siswa Reguler → ubah ke Kategori Khusus
- **Expected:**
  - Data tersimpan
  - Poin rekomendator menyesuaikan (berkurang)
  - Fee rekomendator menyesuaikan (berkurang)

## Catatan Penting

1. **Data Existing**: Semua data siswa lama otomatis di-set ke `'reguler'` oleh migration
2. **Wali Kelas/Wali Santri**: TETAP wajib diisi untuk Kategori Khusus (tidak boleh NULL)
3. **Tidak Menghapus Fitur Orang Tua**: Fitur orang tetap dinonaktifkan sesuai permintaan sebelumnya
4. **Backup Database**: Sebaiknya backup database sebelum menjalankan migration
5. **Multi-Program**: Revisi ini belum termasuk implementasi multi-program lengkap, hanya kategori peserta

## File yang Diubah

### Database
- `database/migration_kategori_peserta.php` (NEW)

### Forms & UI
- `includes/student_form.php`
- `includes/student_table.php`
- `includes/students_page.php`

### CRUD & Import
- `includes/student_crud.php`
- `admin/import.php`
- `mitra/import.php`
- `admin/template.php`
- `mitra/template.php`

### Logic Backend
- `includes/queries.php`
- `wali/_scope.php`

### Dashboard & Laporan
- `wali/index.php`
- `admin/recap.php`
- `admin/index.php`

## Reaktivasi (Jika Diperlukan)

Untuk mengaktifkan kembali fitur orang tua (jika diminta di masa depan):
1. Uncomment kode yang ditandai `DISABLED SEMENTARA` di:
   - `includes/student_crud.php`
   - `admin/import.php`
   - `mitra/import.php`
   - `includes/students_page.php`
   - `includes/student_table.php`
2. Uncomment kolom "Akun Orang Tua" di tabel siswa
3. Uncomment tampilan informasi akun di halaman detail siswa
