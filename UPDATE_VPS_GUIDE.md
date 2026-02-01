# Panduan Update Web di VPS

## Informasi Update
**Branch:** `implementasi-anp`  
**Commit:** `273efaf`  
**Tanggal:** 1 Februari 2026  
**Perubahan:** Implementasi revisi sistem berdasarkan permintaan dosen

## Fitur yang Diperbarui

### 1. Menu Pairwise Comparison (TPP)
- Pilih subkriteria yang dipengaruhi dulu
- Input satu persatu subkriteria yang mempengaruhi (skala 1-9)

### 2. Menu Kelola Kriteria (TPP)
- Tidak perlu input bobot dan jenis kriteria
- Kriteria nilainya setara, hanya untuk pengelompokan subkriteria
- Bobot untuk TOPSIS diambil dari bobot global subkriteria

### 3. Detail Perhitungan
- Tambah detail perhitungan metode ANP (TPP)
- Tambah detail perhitungan metode TOPSIS (BIMKES)

### 4. Menu Kelola Periode
- Dipindah dari TPP ke Admin

### 5. Perhitungan TOPSIS
- Tidak perlu menu perhitungan TOPSIS terpisah
- Sudah ada di manajemen laporan (admin)

### 6. Format Validasi Hasil
- Sama dengan manajemen laporan admin (kalapas) + status
- Status remisi:
  - Nilai preferensi (Ci): ≥0.8500 → Status: Remisi Penuh
  - Nilai preferensi (Ci): ≥0.7500 → Status: Remisi Separuh  
  - Nilai preferensi (Ci): <0.7500 → Status: Tidak Layak Remisi

### 7. Manajemen Laporan (Admin)
- Narapidana terbaik → Remisi Penuh
- Rata-rata → Remisi Separuh
- Perlu perhatian → Tidak Layak

### 8. Menu Admin Baru
- Kelola Admin, TPP, BIMKES, Kalapas, Wali secara terpisah (tampilan saja)
- Semua mengarah ke satu tempat: `/admin/users`
- Admin bisa kelola kriteria, subkriteria, dan melihat hasil ANP

## Langkah-langkah Update di VPS

### 1. Backup Database (Wajib!)
```bash
# Login ke VPS
ssh user@vps-ip

# Backup database
mysqldump -u username -p nama_database > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Update Kode dari GitHub
```bash
# Masuk ke direktori proyek
cd /var/www/html/lapas

# Backup file .env
cp .env .env.backup

# Stash perubahan lokal jika ada
git stash

# Switch ke branch implementasi-anp
git fetch origin
git checkout implementasi-anp
git pull origin implementasi-anp
```

### 3. Update Dependencies
```bash
# Update composer dependencies
composer install --no-dev --optimize-autoloader
```

### 4. Jalankan Migrasi Database
```bash
# Jalankan migrasi baru
php spark migrate

# Jika ada error, coba migrasi spesifik:
php spark migrate -n App\Database\Migrations -g default
```

### 5. Update File Environment
```bash
# Restore file .env dari backup
cp .env.backup .env

# Pastikan konfigurasi database benar
nano .env
```

### 6. Clear Cache
```bash
# Clear cache CodeIgniter
php spark cache:clear

# Clear opcache jika menggunakan PHP-FPM
sudo service php8.x-fpm reload
```

### 7. Update Permission
```bash
# Set permission yang benar
sudo chown -R www-data:www-data /var/www/html/lapas/writable
sudo chmod -R 755 /var/www/html/lapas/writable
```

### 8. Restart Web Server
```bash
# Restart Apache atau Nginx
sudo systemctl restart apache2
# atau
sudo systemctl restart nginx
```

## Troubleshooting

### 1. Error Migrasi
Jika ada error migrasi:
```bash
# Check status migrasi
php spark migrate:status

# Rollback migrasi terakhir
php spark migrate:rollback

# Jalankan migrasi lagi
php spark migrate
```

### 2. Error Route
Jika ada error route:
```bash
# Clear routes cache
rm -f writable/cache/routes*
```

### 3. Error Database
Jika ada error database:
```bash
# Restore dari backup
mysql -u username -p nama_database < backup_file.sql
```

### 4. Error Permission
```bash
sudo chmod -R 755 writable/
sudo chown -R www-data:www-data writable/
```

## Testing Setelah Update

### 1. Login sebagai Admin
- Cek menu baru di sidebar admin
- Test kelola user dengan menu terpisah
- Test akses kriteria, subkriteria, hasil ANP

### 2. Login sebagai TPP
- Test pairwise comparison
- Test kelola kriteria (tanpa input bobot)
- Test hasil ANP

### 3. Login sebagai BIMKES
- Test input penilaian
- Test detail perhitungan TOPSIS

### 4. Login sebagai Kalapas
- Test validasi hasil
- Test status remisi
- Test cetak laporan

### 5. Login sebagai Wali
- Test ranking dengan perhitungan sederhana
- Test hasil penilaian

## Rollback Plan
Jika ada masalah serius:

```bash
# Kembali ke branch sebelumnya
git checkout main
git pull origin main

# Restore database dari backup
mysql -u username -p nama_database < backup_file.sql

# Clear cache
php spark cache:clear
```

## Kontak Support
Jika ada masalah, hubungi:
- **Developer:** Chaisar Abi
- **Email:** chaisarabi@email.com
- **GitHub:** https://github.com/ChaisarAbi/lapas

## Catatan Penting
1. **Selalu backup database** sebelum update
2. **Test semua fitur** setelah update
3. **Monitor error log** setelah deploy
4. **Informasikan user** tentang perubahan fitur

---
**Update berhasil jika:**
- Semua menu baru muncul
- Tidak ada error di log
- Semua fitur berfungsi normal
- Status remisi muncul dengan benar