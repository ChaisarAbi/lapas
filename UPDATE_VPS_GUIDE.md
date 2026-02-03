# Panduan Update Web di VPS

## Informasi Update
**Branch:** `implementasi-anp`  
**Commit:** `7015e80`  
**Tanggal:** 3 Februari 2026  
**Perubahan:** Implementasi algoritma TOPSIS untuk perhitungan ranking narapidana dan konsistensi across all views

## Fitur yang Diperbarui

### 1. Perhitungan Ranking TOPSIS Konsisten
- **Algoritma TOPSIS Standar** - Diimplementasikan dengan benar sesuai metode TOPSIS standar
- **Konsistensi Across Views** - Semua view yang menampilkan ranking (validasi, preview cetak, cetak laporan) now use the same TOPSIS implementation
- **Perhitungan Akurat** - Handle berbagai kasus khusus (semua nilai sama, division by zero, data kosong)

### 2. Fitur Utama yang Diubah
- **KalapasController** - Added `hitungTOPSIS` method (mirroring RankingController)
- **Validasi View** - Uses TOPSIS instead of simple ranking
- **Preview Cetak View** - Uses TOPSIS instead of simple ranking  
- **Cetak Laporan View** - Uses TOPSIS instead of simple ranking
- **RankingController** - Updated TOPSIS method to be consistent with RankingController

### 3. Detail Perhitungan TOPSIS
- **Matriks Keputusan** - Menghitung rata-rata nilai per kriteria dari subkriteria
- **Normalisasi** - Normalisasi matriks keputusan dengan metode euclidean
- **Matriks Terbobot** - Menggunakan bobot kriteria dari database
- **Solusi Ideal** - Menentukan solusi ideal positif dan negatif per kriteria
- **Perhitungan Jarak** - Hitung jarak ke solusi ideal positif (D+) dan negatif (D-)
- **Nilai Preferensi** - Menghitung Ci = D-/(D+ + D-) sebagai indikator preferensi

### 4. Status Remisi Perbaikan
- **Remisi Penuh** - Nilai preferensi ≥ 0.8500
- **Remisi Separuh** - Nilai preferensi ≥ 0.7500  
- **Tidak Layak Remisi** - Nilai preferensi < 0.7500

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
- Test menu laporan (dapat melihat hasil TOPSIS)
- Test cetak laporan (seharusnya konsisten dengan preview)

### 2. Login sebagai Kalapas
- **Test Validasi** - Buka halaman validasi, pastikan ranking terurut benar
- **Test Status Remisi** - Periksa apakah status remisi muncul sesuai nilai preferensi
- **Test Preview Cetak** - Buka preview cetak, pastikan ranking sama dengan halaman validasi
- **Test Cetak Laporan** - Cetak laporan, pastikan nilai preferensi sama

### 3. Login sebagai Wali
- Test dashboard dan hasil penilaian

### 4. Login sebagai TPP
- Test pairwise comparison dan hasil ANP

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
- Ranking di halaman validasi, preview cetak, dan cetak laporan **sama**
- Status remisi muncul dengan benar
- Tidak ada error di log
- Semua fitur berfungsi normal

## Perbedaan dengan Update Sebelumnya

**Update 3 Februari 2026 (TOPSIS):**
- Semua perhitungan ranking now use TOPSIS (sebelumnya: mix between simple ranking and TOPSIS)
- Konsistensi hasil ranking across semua view
- Perhitungan lebih akurat dan sesuai dengan metode standar

**Update Sebelumnya (1 Februari 2026):**
- Revisi menu structure dan fitur utama
- Implementasi pairwise comparison target-first
- Manajemen user terpisah per role