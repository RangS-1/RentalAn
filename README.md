# RentalKu Elektronik — Sistem Rental Barang

Aplikasi PHP OOP untuk rental barang elektronik dengan fitur user/admin, alur pinjam-konfirmasi-hitung mundur-kembalikan.

## Cara Install (XAMPP / Laragon)

1. **Extract** folder `rental-elektronik` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. **Buat database**: buka phpMyAdmin → import file `database.sql`. Otomatis membuat database `rental_elektronik` beserta semua tabel + 1 akun admin + 5 contoh barang.
3. **Sesuaikan koneksi** jika perlu di `classes/Database.php` (default: host `localhost`, user `root`, password kosong).
4. **Jalankan**: buka `http://localhost/rental-elektronik/` di browser.

## Akun Default
- **Admin**: username `admin`, password `admin123`
- **User**: daftar via halaman Register.

> Catatan: jika Anda mendaftar dengan username persis `admin`, sistem otomatis memberi role admin.

## Struktur Folder
```
rental-elektronik/
├── classes/         # Class PHP (Database, User, Item, Rental)
├── admin/           # Dashboard & halaman admin
├── user/            # Dashboard & halaman user
├── includes/        # config.php, header.php, footer.php
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   └── images/      # Folder upload gambar barang
├── database.sql     # Schema + sample data
├── index.php
├── login.php
├── register.php
└── logout.php
```

## Fitur

### User
- Register & Login
- Lihat katalog barang
- Pop-up pinjam (nama peminjam, lokasi, lama hari, jumlah)
- Lihat hitung mundur untuk barang aktif
- Pop-up pengembalian (admin penerima, uang dibayar, kembalian otomatis, lokasi)
- Halaman akun

### Admin
- Dashboard dengan statistik
- CRUD Barang (upload gambar, nama, deskripsi, harga/hari, stok)
- Daftar pengguna terdaftar + ubah role
- Konfirmasi permintaan pinjam (stok auto-kurang)
- Konfirmasi pengembalian (stok auto-tambah)

## Status Alur Peminjaman
1. `pending_borrow` → user ajukan, menunggu admin
2. `active` → admin konfirmasi, countdown jalan
3. `pending_return` → user ajukan kembali, menunggu admin
4. `returned` → admin konfirmasi, stok bertambah
5. `rejected` → ditolak admin

## Troubleshooting
- **Gambar tidak tampil**: pastikan folder `assets/images/` writable (chmod 755).
- **Login admin gagal**: jalankan query berikut di phpMyAdmin untuk reset password:
  ```sql
  UPDATE users SET password='<hash_baru>' WHERE username='admin';
  ```
  Atau cukup register ulang dengan username `admin`.
