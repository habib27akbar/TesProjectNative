# TesProjectNative - PHP Native CRUD

Aplikasi PHP Native sederhana untuk:
- Login
- Manajemen User CRUD
- Manajemen Pegawai CRUD
- Upload foto pegawai JPG/JPEG maksimal 300KB

## Struktur Folder

```text
TesProjectNative/
├── assets/
│   ├── css/style.css
│   └── uploads/pegawai/
├── config/
│   └── koneksi.php
├── database/
│   └── db_tesproject_native.sql
├── helpers/
│   └── auth.php
├── layouts/
│   ├── header.php
│   ├── navbar.php
│   ├── sidebar.php
│   └── footer.php
├── views/
│   ├── auth/login.php
│   ├── user/index.php
│   └── pegawai/index.php
├── index.php
└── logout.php
```

## Cara Instalasi

1. Copy folder `TesProjectNative` ke `htdocs` jika menggunakan XAMPP.
2. Buat database MySQL dengan import file:
   `database/db_tesproject_native.sql`
3. Sesuaikan konfigurasi database di:
   `config/koneksi.php`
4. Jalankan melalui browser:
   `http://localhost/TesProjectNative/index.php`

## Akun Login Default

- Username: `Admin`
- Password: `Admin`

## Catatan Upload Foto

- Format: JPG/JPEG
- Maksimal ukuran: 300KB
- Folder penyimpanan: `assets/uploads/pegawai/`
