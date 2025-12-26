# Panduan Deployment Lokal - Sistem Pendukung Keputusan Pembinaan Narapidana (SPK-Pembinaan)

**Bahasa: Indonesia**

## **1. Persiapan Environment Lokal**

### **1.1 Install Software yang Diperlukan**

#### **Option A: Paket All-in-One (Rekomendasi untuk Pemula)**
- **XAMPP** (Windows): https://www.apachefriends.org/
- **MAMP** (Mac): https://www.mamp.info/
- **Laragon** (Windows): https://laragon.org/

#### **Option B: Install Manual**
1. **PHP 8.3+**
2. **MySQL 8.0+** atau MariaDB 10.4+
3. **Apache** atau **Nginx**
4. **Composer** (Package Manager PHP)
5. **Git**

### **1.2 Verifikasi Instalasi**
```bash
# Cek versi PHP
php --version

# Cek versi MySQL
mysql --version

# Cek Composer
composer --version

# Cek Git
git --version
```

---

## **2. Setup Project di Komputer Lokal**

### **2.1 Clone Repository**
```bash
# Buka terminal/command prompt
cd /path/ke/direktori/project
git clone https://github.com/ChaisarAbi/lapas.git spk-pembinaan
cd spk-pembinaan
```

### **2.2 Install Dependencies dengan Composer**
```bash
# Install semua dependencies
composer install

# Jika ada error, coba:
composer install --no-dev --optimize-autoloader
```

### **2.3 Konfigurasi Environment**
```bash
# Salin file .env.example ke .env
copy .env.example .env  # Windows
# atau
cp .env.example .env    # Linux/Mac

# Edit file .env dengan text editor favorit Anda
```

**Konfigurasi `.env` untuk lokal:**
```env
# Mode aplikasi (development untuk lokal)
CI_ENVIRONMENT = development

# Base URL untuk lokal
app.baseURL = 'http://localhost:8080'
# atau
app.baseURL = 'http://localhost/spk-pembinaan/public'

# Konfigurasi Database Lokal
database.default.hostname = localhost
database.default.database = spk_pembinaan
database.default.username = root
database.default.password = ''  # Kosong jika tidak ada password
database.default.DBDriver = MySQLi
database.default.DBPrefix = ''
database.default.port = 3306
```

### **2.4 Generate Encryption Key**
```bash
php spark key:generate
```

---

## **3. Setup Database Lokal**

### **3.1 Buat Database**
```bash
# Login ke MySQL
mysql -u root -p

# Buat database
CREATE DATABASE spk_pembinaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Keluar dari MySQL
EXIT;
```

### **3.2 Jalankan Migrasi Database**
```bash
# Jalankan semua migrasi
php spark migrate

# Jika ada error, coba dengan flag -f
php spark migrate -f
```

### **3.3 Jalankan Seeder (Data Awal)**
```bash
# Jalankan seeder komprehensif
php spark db:seed ComprehensiveSeeder

# Atau jalankan seeder user saja
php spark db:seed AddWaliAndKalapasUsers
```

### **3.4 Cek Data di Database**
```bash
# Login ke MySQL dan cek tabel
mysql -u root -p spk_pembinaan

SHOW TABLES;
SELECT * FROM users LIMIT 5;
SELECT * FROM narapidana LIMIT 5;
```

---

## **4. Menjalankan Aplikasi**

### **4.1 Option A: PHP Built-in Server (Paling Mudah)**
```bash
# Jalankan server di port 8080
php spark serve

# Atau dengan port tertentu
php spark serve --port=8000

# Server akan berjalan di: http://localhost:8080
```

## **5. Testing Aplikasi**

### **5.1 Akses Aplikasi**
- **PHP Built-in Server**: http://localhost:8080
- **Virtual Host**: http://spk.local
- **XAMPP Default**: http://localhost/spk-pembinaan/public

### **5.2 Login dengan User Default**
Setelah seeder dijalankan, gunakan credential berikut:

| Role | Username | Password | Dashboard |
|------|----------|----------|-----------|
| ADMIN | admin | admin123 | /admin/dashboard |
| TPP | tpp_user | tpp123 | /tpp/dashboard |
| BIMKEMASWAT | bimkes_user | bimkes123 | /bimkesmaswat/dashboard |
| WALI_PEMASYARAKATAN | wali_user | wali123 | /wali/dashboard |
| KEPALA_LAPAS | kalapas_user | kalapas123 | /kalapas/dashboard |

**Catatan**: Password di atas adalah contoh. Gunakan password yang di-generate oleh seeder.

### **5.3 Test Fitur Utama**
1. **Login** dengan credential di atas
2. **Dashboard** sesuai role
3. **Cetak Laporan PDF** (Kepala Lapas)
4. **Input Penilaian** (BIMKEMASWAT)
5. **Validasi Hasil** (Kepala Lapas)


## **6. Troubleshooting Lokal**

### **6.1 Error: "Class 'CodeIgniter\Exceptions\...' not found"**
```bash
# Install ulang dependencies
composer install --no-dev --optimize-autoloader

# Atau update composer
composer update
```

### **6.2 Error: Database Connection**
```bash
# Cek konfigurasi .env
# Pastikan username, password, dan database name benar

# Test koneksi MySQL
mysql -u root -p -e "SHOW DATABASES;"

# Buat database jika belum ada
mysql -u root -p -e "CREATE DATABASE spk_pembinaan;"
```

### **6.3 Error: "No input file specified" (Nginx)**
```bash
# Perbaiki konfigurasi Nginx
# Pastikan root path benar dan file index.php ada
```

### **6.4 Error: "Forbidden" (Apache)**
```bash
# Perbaiki permission
chmod -R 755 /path/ke/spk-pembinaan
chmod -R 775 /path/ke/spk-pembinaan/writable

# Perbaiki .htaccess
# Pastikan file .htaccess di public folder ada
```

### **6.5 Error: Session Not Working**
```bash
# Cek permission folder writable/session
chmod 775 writable/session
chmod 775 writable/cache
chmod 775 writable/logs
```

---

## **7. Tools Development yang Direkomendasikan**

### **7.1 Code Editor**
- **Visual Studio Code** (Gratis, rekomendasi)
- **PHPStorm** (Berbayar, fitur lengkap)
- **Sublime Text** (Ringan)

### **7.2 Database Tools**
- **phpMyAdmin** (Web-based)
- **MySQL Workbench** (GUI resmi MySQL)
- **HeidiSQL** (Windows)
- **TablePlus** (Multi-platform)

### **7.3 Testing Tools**
- **Browser Developer Tools** (F12)
- **XDebug** (PHP debugging)

### **7.4 Version Control**
- **Git** (Command line)
- **GitHub Desktop** (GUI)
- **SourceTree** (GUI)

---

## **8. Struktur Project untuk Development**

```
spk-pembinaan/
├── app/
│   ├── Config/          # Konfigurasi aplikasi
│   ├── Controllers/     # Controller
│   ├── Models/          # Model database
│   ├── Views/           # Template view
│   └── ...
├── public/              # Folder public (web root)
│   ├── index.php        # Entry point
│   └── .htaccess        # Apache configuration
├── writable/            # Folder writable (cache, logs, session)
├── tests/               # Unit tests
├── vendor/              # Dependencies Composer
├── .env                 # Environment configuration (buat dari .env.example)
├── composer.json        # Dependencies PHP
└── README.md            # Dokumentasi project
```

---

## **9. Tips Development**

### **9.1 Debugging**
```php
// Gunakan helper dd() untuk debugging
dd($variable);

// Atau var_dump() dengan die()
var_dump($variable); die();

// Log ke file
log_message('error', 'Pesan error: ' . $error);
```

### **9.2 Code Style**
- Ikuti **PSR-12** coding standard
- Gunakan **Bahasa Indonesia** untuk komentar dan UI
- Nama variabel boleh bahasa Inggris
- Dokumentasi fungsi dengan PHPDoc

### **9.3 Security**
- Jangan commit file `.env` ke GitHub
- Gunakan `.gitignore` yang sudah disediakan
- Validasi semua input user
- Gunakan prepared statements untuk query

### *9.4 Performance**
- Gunakan caching untuk data yang jarang berubah
- Optimasi query database
- Minify CSS/JS untuk production

**Dokumen ini terakhir diperbarui: 26 Desember 2025**

**Happy Coding! 🚀**
