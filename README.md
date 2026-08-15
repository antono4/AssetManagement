# Aplikasi Asset Management (Manajemen Aset IT & Umum)

Aplikasi web full-stack untuk manajemen aset IT & umum, dibangun dengan **CodeIgniter 4** (MVC), **MySQL**, dan antarmuka **dark glassmorphism** dengan animasi particle + custom cursor. Interaksi antarmuka berbasis **AJAX/SPA** — tanpa reload halaman saat berpindah modul.

> Dibuat oleh OpenHands AI agent atas permintaan user.

---

## ✨ Fitur

- **Login & Autentikasi** — halaman login aman dengan validasi session (password hash BCrypt).
- **RBAC (Role Based Access Control)**:
  - **Admin**: CRUD penuh pada aset, kategori, user, dan cetak laporan.
  - **Staff**: hanya melihat daftar aset, status, dan riwayat pemakaian/perawatan.
- **Dashboard** — ringkasan statistik (total aset, tersedia, dipinjam, rusak, total user, nilai aset) + **Chart.js** (doughnut status & bar kategori).
- **Manajemen Aset (CRUD)** — tambah/edit/hapus aset dengan atribut: Kode, Nama, Kategori, Merk/Spesifikasi, Lokasi, Status, Tanggal Pembelian, Harga.
- **Riwayat Aset** — setiap perubahan status/CRUD dicatat di `asset_logs` + tampil di modal detail aset.
- **Kategori (CRUD)** — kelola kategori aset (admin).
- **Manajemen User (CRUD)** — kelola user & role (admin).
- **Laporan** — filter aset berdasarkan rentang tanggal & kategori, ringkasan total, dan **print layout** (CSS `@media print`).
- **UI Premium**:
  - Dark theme + **glassmorphism** (backdrop-blur).
  - **Particle canvas** ringan di halaman login.
  - **Custom interactive cursor** (dot + ring yang mengikuti pointer).
  - Animasi aurora background, toast notification, modal slide-up, skeleton loading.

---

## 🛠️ Tech Stack

| Lapisan      | Teknologi                                   |
|--------------|---------------------------------------------|
| Backend      | PHP 8.1+ / CodeIgniter 4.7                  |
| Database     | MySQL 5.7+ / MariaDB 10+                    |
| Frontend     | Tailwind CSS (CDN), Chart.js, vanilla JS    |
| Arsitektur   | MVC + AJAX/SPA (endpoint `/api/*` JSON)     |

---

## 📁 Struktur Proyek

```
.
├── app/
│   ├── Config/
│   │   ├── App.php            # baseURL, timezone, locale
│   │   ├── Database.php       # koneksi MySQL
│   │   ├── Filters.php        # registrasi filter 'auth'
│   │   ├── Routes.php         # routing web + API JSON
│   │   └── Session.php        # nama cookie session
│   ├── Controllers/
│   │   ├── BaseController.php # helper umum (isAdmin, denyUnlessAdmin, respond)
│   │   ├── Auth.php           # login, logout, setup password
│   │   ├── Dashboard.php      # halaman + stats API
│   │   ├── Asset.php          # CRUD aset + logs (AJAX)
│   │   ├── Category.php       # CRUD kategori (AJAX)
│   │   ├── User.php           # CRUD user (AJAX, admin)
│   │   └── Report.php         # laporan + filter (AJAX)
│   ├── Filters/
│   │   └── AuthFilter.php     # cek session, redirect/401 JSON
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── CategoryModel.php
│   │   ├── AssetModel.php
│   │   └── AssetLogModel.php
│   └── Views/
│       ├── auth/login.php     # halaman login (glassmorphism + particle)
│       └── layouts/app.php    # SPA shell (sidebar + topbar + content)
├── public/
│   ├── css/app.css            # dark glassmorphism styles
│   └── js/
│       ├── app.js             # cursor, toast, api wrapper, modal
│       ├── particles.js       # particle canvas (login)
│       └── pages/
│           ├── dashboard.js   # render dashboard + chart
│           ├── assets.js      # CRUD aset via AJAX
│           ├── categories.js
│           ├── users.js
│           └── reports.js     # filter + print
├── assets_app.sql             # DDL + data dummy
├── composer.json
├── spark                      # CLI CodeIgniter
└── .env.example
```

---

## 🚀 Panduan Instalasi (Langkah-demi-Langkah)

### 1. Prasyarat

- PHP **8.1+** dengan ekstensi: `mysqli`, `mbstring`, `xml`, `intl`, `curl`, `gd` (opsional).
- MySQL 5.7+ atau MariaDB 10+.
- Composer.

### 2. Clone & Install Dependency

```bash
git clone https://github.com/antono4/AssetManagement.git
cd AssetManagement
composer install
```

> Jika `composer install` tidak menyalin ThirdParty (Kint/Escaper/PSR) ke `system/ThirdParty/`, jalankan:
> ```bash
> php -r "CodeIgniter\ComposerScripts::postUpdate();"
> ```
> atau copy manual dari `vendor/` ke `vendor/codeigniter4/framework/system/ThirdParty/`.

### 3. Setup Database

Buat database & import skema:

```bash
mysql -u root -p -e "CREATE DATABASE asset_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p asset_db < assets_app.sql
```

### 4. Konfigurasi Environment

Salin `.env`:

```bash
cp .env.example .env
```

Edit `.env` — sesuaikan koneksi database:

```ini
database.default.hostname = localhost
database.default.database = asset_db
database.default.username = root        # ganti sesuai user MySQL Anda
database.default.password = yourpass    # ganti sesuai password
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Sesuaikan juga `app.baseURL` bila bukan `http://localhost:8080/`.

### 5. Setup Password Awal (PENTING)

Karena hash BCrypt bersifat acak, file SQL hanya berisi placeholder. Jalankan **satu kali** untuk menghasilkan hash valid:

**Opsi A — via browser** (mudah):

```bash
php spark serve
```

Lalu buka `http://localhost:8080/setup`. Endpoint ini mereset:
- `admin` → `admin123`
- `staff` → `staff123`

**Opsi B — via PHP CLI**:

```bash
php -r "require 'vendor/autoload.php'; \$db=\Config\Database::connect();
\$db->query('UPDATE users SET password=? WHERE username=?',[password_hash('admin123',PASSWORD_BCRYPT),'admin']);
\$db->query('UPDATE users SET password=? WHERE username=?',[password_hash('staff123',PASSWORD_BCRYPT),'staff']);"
```

> ⚠️ **Setelah password berhasil**, **HAPUS route `setup`** di `app/Config/Routes.php` (baris yang mengandung `setup`) untuk keamanan produksi.

### 6. Jalankan Aplikasi

```bash
php spark serve
```

Buka `http://localhost:8080/` → login dengan:

| Role  | Username | Password  |
|-------|----------|-----------|
| Admin | `admin`  | `admin123`|
| Staff | `staff`  | `staff123`|

---

## 🔐 Catatan Keamanan (Produksi)

1. Set `CI_ENVIRONMENT = production` di `.env`.
2. Hapus route `/setup` setelah setup password awal.
3. Aktifkan CSRF filter (uncomment `csrf` di `app/Config/Filters.php` `$globals['before']`) — pastikan AJAX mengirim token.
4. Set `app.forceGlobalSecureRequests = true` (HTTPS).
5. Ganti password default admin/staff segera setelah login.
6. Jangan commit file `.env`.

---

## 🗄️ Skema Database

Empat tabel utama (lihat `assets_app.sql` untuk DDL lengkap):

- **`users`** — `id, name, username, email, password, role(enum:admin/staff), is_active, created_at, updated_at`
- **`categories`** — `id, name, description, created_at`
- **`assets`** — `id, asset_code, name, category_id(FK), brand_spec, location, status(enum:tersedia/dipinjam/rusak), purchase_date, price, created_at, updated_at`
- **`asset_logs`** — `id, asset_id(FK), user_id(FK), action, note, created_at`

Data dummy: 5 kategori, 2 user (admin+staff), 10 aset, 6 log entries.

---

## 🧭 Arsitektur AJAX/SPA

- Route web (`/dashboard`, `/asset`, dll) hanya merender **SPA shell** (`layouts/app.php`) yang berisi sidebar + topbar + container kosong.
- Setiap halaman punya file JS di `public/js/pages/*.js` yang mendaftarkan renderer ke `window.PAGE_RENDERERS`.
- Saat user klik nav, `loadPage(page)` memanggil renderer → renderer fetch data dari `/api/*` (JSON) → inject HTML ke `#spaContent`. **Tidak ada reload halaman.**
- Operasi CRUD (modal form) juga via `fetch` ke `/api/*`, lalu re-render halaman.

Endpoint API JSON (semua dilindungi filter `auth`):

| Method | Endpoint                      | Fungsi                     |
|--------|-------------------------------|----------------------------|
| GET    | `/api/dashboard/stats`        | Statistik dashboard        |
| GET    | `/api/assets`                 | List aset (+ kategori)     |
| GET    | `/api/assets/{id}`            | Detail aset                |
| POST   | `/api/assets`                 | Tambah aset (admin)        |
| POST   | `/api/assets/{id}`            | Update aset (admin)        |
| POST   | `/api/assets/{id}/delete`     | Hapus aset (admin)         |
| GET    | `/api/assets/{id}/logs`       | Riwayat aset               |
| GET    | `/api/categories`             | List kategori (+ count)    |
| POST   | `/api/categories`             | Tambah kategori (admin)    |
| POST   | `/api/categories/{id}`        | Update kategori (admin)    |
| POST   | `/api/categories/{id}/delete` | Hapus kategori (admin)     |
| GET    | `/api/users`                  | List user (admin)          |
| POST   | `/api/users`                  | Tambah user (admin)        |
| POST   | `/api/users/{id}`             | Update user (admin)        |
| POST   | `/api/users/{id}/delete`      | Hapus user (admin)         |
| GET    | `/api/reports/assets`         | Laporan + filter           |

---

## 📜 Lisensi

MIT License.
