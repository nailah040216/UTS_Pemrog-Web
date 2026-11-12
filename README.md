# MediaHub — Prototype UTS Pemrograman Web

Prototype aplikasi web pengelolaan katalog media, pengguna, dan simulasi transaksi peminjaman/pengembalian. Dibuat dengan HTML, CSS, JavaScript, PHP 8.x native (tanpa framework), OOP, dan penyimpanan JSON.

## 1. Persyaratan
- PHP 8.1 atau lebih baru
- Browser modern
- Tidak membutuhkan database atau Composer

## 2. Menjalankan aplikasi
Buka terminal pada folder `MediaHub-UTS`, lalu jalankan:

```bash
php -S localhost:8000
```

Buka `http://localhost:8000`.

Alternatif XAMPP/Laragon: salin folder `MediaHub-UTS` ke `htdocs` atau `www`, lalu buka `http://localhost/MediaHub-UTS`.

Pastikan folder `storage/` dapat ditulis oleh PHP.

## 3. Akun demo
| Peran | Email | Password |
|---|---|---|
| Admin | admin@mediahub.test | admin123 |
| Member | member@mediahub.test | member123 |

Pengunjung juga dapat mendaftar sebagai Member.

## 4. Fitur
- Beranda dengan ringkasan koleksi, stok, dan transaksi aktif.
- Katalog buku/film, pencarian, dan filter jenis.
- Login, logout, registrasi, dan role Admin/Member.
- Admin dapat menambah dan menghapus media.
- Member dapat meminjam media jika stok tersedia.
- Pengguna dapat mengembalikan pinjaman; Admin dapat mengelola pengembalian seluruh transaksi.
- Riwayat transaksi dan batas pengembalian 7 hari.
- Data tersimpan di `storage/users.json`, `storage/media.json`, dan `storage/loans.json`.

## 5. Pemetaan rubrik UTS

| Ketentuan | Implementasi |
|---|---|
| Form validation | `required`, `type=email`, `minlength`, `min`, serta `checkValidity()` dan `reportValidity()` |
| Elemen semantik | `header`, `nav`, `main`, `section`, `article`, `form`, `table`, `footer` |
| Responsive layout | CSS Grid/Flexbox dan media query pada `assets/css/style.css` |
| Interaktivitas JS | Validasi form, batas input angka, konfirmasi aksi |
| Class & Object | `Media`, `Book`, `Movie`, `User`, `Admin`, `Member`, `Loan` |
| Encapsulation | Properti `protected`/`private` dan akses melalui getter/setter |
| Inheritance | `Book` dan `Movie` mewarisi `Media`; `Admin` dan `Member` mewarisi `User` |
| Polymorphism | `getType()` pada `Book`/`Movie`, `getRole()` pada `Admin`/`Member`; kode bekerja melalui tipe induk |
| Namespace | `App\\Models`, `App\\Services`, `App\\Controllers` |
| Struktur | Pemisahan Models, Services, Controllers, Views, Assets, Storage |

## 6. Struktur folder

```text
MediaHub-UTS/
├── index.php
├── README.md
├── src/
│   ├── bootstrap.php
│   ├── Controllers/
│   │   └── AppController.php
│   ├── Models/
│   │   ├── Media.php
│   │   ├── Book.php
│   │   ├── Movie.php
│   │   ├── User.php
│   │   ├── Admin.php
│   │   ├── Member.php
│   │   └── Loan.php
│   └── Services/
│       ├── JsonStorage.php
│       ├── MediaRepository.php
│       ├── AuthService.php
│       └── LoanService.php
├── views/
│   └── layout.php
├── assets/
│   ├── css/style.css
│   └── js/app.js
└── storage/
    ├── users.json
    ├── media.json
    └── loans.json
```

## 7. Skenario pengujian demo
1. Buka beranda dan tunjukkan layout desktop serta mobile.
2. Coba daftar dengan email tidak valid/password terlalu pendek untuk menunjukkan validasi.
3. Login sebagai Member, pinjam satu media, lalu buka menu Transaksi.
4. Kembalikan media dan periksa perubahan status.
5. Logout, login sebagai Admin, tambah media baru, lalu hapus media tersebut.
6. Tunjukkan file JSON berubah setelah transaksi dan pengelolaan katalog.

## Catatan
- Ini prototype simulasi peminjaman, bukan sistem pembayaran atau integrasi layanan eksternal.
- Transaksi memakai tanggal dan batas kembali otomatis 7 hari.
- Untuk penggunaan produksi perlu ditambah CSRF protection, pembatasan percobaan login, validasi server yang lebih ketat, dan penguatan otorisasi.
- Akun demo dibuat otomatis saat file data pengguna belum berisi data. Jika ingin mengulang data awal, hapus isi file JSON di `storage/` dan muat ulang aplikasi.
