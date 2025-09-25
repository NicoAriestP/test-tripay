"# Web E-Commerce Sederhana dalam rangka technical test di PT. Trijaya Digital Group

## 👨‍💻 Developer Information
- **Nama Lengkap**: Nico Ariest Putra
- **Email**: nicoariestputra@gmail.com
- **Nomor HP**: 083832108514

## 🛠️ Teknologi yang Digunakan

### Backend
- **Laravel 12** - PHP Framework
- **MySQL** - Database
- **Tripay API** - Payment Gateway Integration

### Frontend  
- **Vue.js 3** - JavaScript Framework
- **TypeScript** - Type-safe JavaScript
- **Vite** - Build Tool & Development Server
- **PrimeVue** - UI Component Library
- **Tailwind CSS** - Utility-first CSS Framework

## 🚀 Langkah-langkah Instalasi dan Menjalankan Website

### Prasyarat
Pastikan sistem Anda sudah terinstall:
- PHP >= 8.2
- Composer
- Node.js >= 18
- PNPM
- MySQL/MariaDB

### 1. Clone Repository
```bash
git clone https://github.com/NicoAriestP/test-tripay.git
cd test-tripay
```

### 2. Setup Backend (Laravel API)

#### 2.1. Masuk ke direktori API
```bash
cd test-tripay-api
```

#### 2.2. Install Dependencies
```bash
composer install
```

#### 2.3. Setup Environment
```bash
copy .env.example .env
```

#### 2.4. Konfigurasi file `.env`
Buka file `.env` dan sesuaikan konfigurasi berikut:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=test_tripay_api
DB_USERNAME=root
DB_PASSWORD=

# Tripay Configuration (Wajib diisi dengan kredensial Tripay Anda)
TRIPAY_API_SANDBOX_URL=https://tripay.co.id/api-sandbox
TRIPAY_API_KEY=your_tripay_api_key
TRIPAY_PRIVATE_KEY=your_tripay_private_key
TRIPAY_MERCHANT_CODE=your_merchant_code
```

#### 2.5. Generate Application Key
```bash
php artisan key:generate
```

#### 2.6. Setup Database
```bash
# Buat database MySQL dengan nama 'test_tripay_api'
# Kemudian jalankan migrasi dan seeder
php artisan migrate
php artisan db:seed
```

#### 2.7. Jalankan Server Laravel
```bash
php artisan serve
```
Server Laravel akan berjalan di `http://localhost:8000`

### 3. Setup Frontend (Vue.js)

#### 3.1. Masuk ke direktori Frontend (buka terminal baru)
```bash
cd test-tripay-frontend
```

#### 3.2. Install Dependencies
```bash
pnpm install
```

#### 3.3. Setup Environment
```bash
copy .env.example .env
```

#### 3.4. Konfigurasi file `.env`
Buka file `.env` dan sesuaikan base URL API:

```env
# Base URL untuk API Laravel (sesuaikan dengan URL server Laravel Anda)
VITE_BASE_API_URL=http://localhost:8000
```

#### 3.5. Jalankan Development Server
```bash
pnpm dev
```
Frontend akan berjalan di `http://localhost:5173`

### 4. Akses Aplikasi

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000

## 📁 Struktur Project

```
test-tripay/
├── test-tripay-api/          # Backend Laravel
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── routes/
│   └── ...
├── test-tripay-frontend/     # Frontend Vue.js
│   ├── src/
│   ├── public/
│   └── ...
└── README.md
```
---
*Dibuat dengan ❤️ menggunakan Laravel 12 dan Vue.js*" 
