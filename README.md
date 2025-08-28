<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions">
    <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

---

# 🏠 API Sistem Informasi Properti Perumahan  

Sebuah **RESTful API** yang dibangun untuk sistem informasi properti perumahan.  
API ini dirancang untuk mengelola berbagai aspek bisnis properti, meliputi:  

- Penjualan properti  
- Monitoring stok/inventori  
- Pelaporan keuangan  
- Manajemen pengguna  

API ini menyediakan backend yang **aman, terukur, dan mudah diintegrasikan** dengan aplikasi web maupun mobile.

---

## 🚀 Why Laravel?  

Laravel adalah framework **Model-View-Controller (MVC)** berbasis PHP yang elegan dan powerful.  
Alasan pemilihan Laravel pada proyek ini:  

- 🔒 **Robust Security**: Fitur keamanan bawaan untuk melindungi dari kerentanan umum (CSRF, SQL Injection, XSS).  
- ⚡ **Scalability**: Struktur modular & rapi sehingga mudah dikembangkan sesuai pertumbuhan pengguna.  
- 🛠️ **Developer-Friendly Tools**: Ekosistem kaya (routing, database, autentikasi) yang mempercepat proses development.  
- 🧩 **Clean & Maintainable Code**: Pola MVC membantu menjaga keteraturan kode sehingga lebih mudah dipelihara.  

---

## ✨ Key Features  

- 🔑 **JWT Authentication**: Keamanan akun berbasis JSON Web Token untuk autentikasi & otorisasi pengguna.  
- 👥 **Role-Based Access Control (RBAC)**: Mengatur hak akses pengguna sesuai perannya.  
- 💸 **Real-time Transactions**: Proses transaksi keuangan secara langsung dan cepat.  
- 🧾 **Receipt Printing**: Cetak bukti transaksi secara otomatis.  
- 📦 **Inventory Monitoring**: Melacak status & ketersediaan properti di gudang/stok.  
- 📊 **Monthly & Annual Reporting**: Laporan performa penjualan & kesehatan finansial.  
- 🏢 **Branch Management**: Tambah cabang baru properti dengan mudah.  
- 🏷️ **Property Sales Management**: Sistem komprehensif untuk mengelola proses penjualan.  
- 📇 **Customer Data Monitoring**: Kelola & pantau data pembeli properti.  
- 📈 **Sales Status Monitoring**: Update real-time status penjualan properti (*Installment* atau *Paid Off*).  

---

## 📌 Tech Stack  

- **Backend**: Laravel (PHP)  
- **Database**: MySQL / PostgreSQL  
- **Authentication**: JWT  
- **Deployment**: Docker / Laravel Forge (opsional)  

---

## 📖 Getting Started  

### 1. Clone Repository  
```bash
git clone (https://github.com/wynyga/Simpro.git)
cd Simpro
```

### 2. Install Dependencies
```bash
composer install
```
### 3. Setup Environment
```bash
Buat file .env lalu sesuaikan konfigurasi database & JWT.
```

### 4. Jalankan migrasi
```bash
php artisan migrate
```

### 5. Start Server
```bash
php artisan serve

```

### 📬 Contact
Wayan Candra Yoga Kamandanu
wayancandrayoga@gmail.com

