# Panduan Update VPS

## Langkah-langkah Update

1. **Login ke VPS**:
   ```bash
   ssh username@IP_VPS
   ```

2. **Navigasi ke direktori project**:
   ```bash
   cd /var/www/html/lapas
   ```

3. **Pull latest changes dari GitHub**:
   ```bash
   git pull origin implementasi-anp
   ```

4. **Jalankan migrations**:
   ```bash
   php spark migrate
   ```

5. **Jalankan seeding untuk semua data**:
   ```bash
   # Seed untuk user (Wali, Kalapas, Bimkes)
   php spark db:seed "App\Database\Seeds\AddWaliAndKalapasUsers" --force

   # Seed untuk data ANP BIMKES (kriteria, subkriteria, edges, pairwise comparison)
   php spark db:seed "App\Database\Seeds\AnpBimkesSeeder" --force

   # Seed untuk data comprehensive (narapidana, penilaian, validasi)
   php spark db:seed "App\Database\Seeds\ComprehensiveSeeder" --force
   ```

6. **Clear cache**:
   ```bash
   php spark cache:clear
   ```

7. **Restart web server**:
   ```bash
   sudo systemctl restart apache2
   ```

## Daftar Seed dan Fungsi

### 1. AddWaliAndKalapasUsers.php
- Menambahkan user dengan role:
  - `tpp02` (TPP) - Password: tpp123
  - `bimkes02` (BIMKEMASWAT) - Password: bimkes123
  - `bimkes03` (BIMKEMASWAT) - Password: bimkes123  
  - `wali01` (WALI_PEMASYARAKATAN) - Password: wali123
  - `kalapas01` (KEPALA_LAPAS) - Password: kalapas123

### 2. AnpBimkesSeeder.php
- **4 Kriteria/Clusters**: Kepribadian (KP), Kemandirian (KM), Sikap (S), Mental (M)
- **10 Subkriteria**:
  - KP1-KP3 (Kepribadian: Kesadaran Beragama, Kesadaran Hukum, Konseling & Rehabilitasi)
  - KM1-KM2 (Kemandirian: Pelatihan Keterampilan, Produksi Barang/Jasa)
  - S1-S2 (Sikap: Keberfungsian & Rutinitas, Pelanggaran Hukum)
  - M1-M3 (Mental: Depresi, Kecemasan, Potensi Bunuh Diri)
- **28 Edges**: Relasi antar subkriteria (influencer → target)
- **56 Pairwise Comparison Data**: Data perbandingan pairwise untuk ANP
- **1 Periode Aktif**: Periode aktif (bulan saat ini)

### 3. ComprehensiveSeeder.php
- **45 Narapidana**: Data narapidana dengan berbagai jenis kejahatan dan profil
- **6 Kriteria**: Kedisiplinan, Kepatuhan, Keterampilan, Perilaku Sosial, Kesehatan, Motivasi Perubahan
- **18 Subkriteria**: Subkriteria untuk masing-masing kriteria
- **6.750 Data Penilaian**: Data penilaian untuk 3 periode terakhir (2025-10, 2025-11, 2025-12)
- **15 Data Validasi**: Data validasi untuk periode 2025-12

## Verifikasi Data di Database

Jalankan query berikut untuk memverifikasi data:

```sql
-- Jumlah user
SELECT role, COUNT(*) as jumlah FROM users GROUP BY role;

-- Jumlah kriteria dan subkriteria
SELECT COUNT(*) as jumlah_kriteria FROM kriteria;
SELECT COUNT(*) as jumlah_subkriteria FROM subkriteria;

-- Jumlah narapidana
SELECT COUNT(*) as jumlah_narapidana FROM narapidana;

-- Jumlah penilaian dan validasi
SELECT periode, COUNT(*) as jumlah FROM penilaian GROUP BY periode;
SELECT periode, COUNT(*) as jumlah FROM validasi GROUP BY periode;

-- Jumlah edges dan clusters di ANP
SELECT COUNT(*) as jumlah_edges FROM anp_edges;
SELECT COUNT(*) as jumlah_clusters FROM anp_clusters;
```

**Expected Output**:
- Users: 5-6 users dengan berbagai role
- Kriteria: 6 (atau 4 jika hanya ANP)
- Subkriteria: 18 (atau 10 jika hanya ANP)
- Narapidana: 45
- Penilaian: ~6.750 data
- Validasi: 15 data

## Troubleshooting

### Error saat migrate:
```bash
# Rollback dan jalankan ulang migrate
php spark migrate:rollback
php spark migrate

# Atau migrasi dari awal (hanya jika data tidak penting)
php spark migrate:refresh
```

### Error saat seed:
```bash
# Jalankan seed dengan verbose mode
php spark db:seed "NamaSeeder" --verbose
```

### Clear cache jika ada masalah:
```bash
php spark cache:clear
php spark view:clear
```

## Testing Aplikasi

Setelah update, buka browser dan test fitur:

1. **Login sebagai Admin**:
   - Username: `admin`
   - Password: `admin123`
   - Verifikasi menu Dashboard, Kriteria, Subkriteria, Laporan

2. **Login sebagai TPP**:
   - Username: `tpp02`
   - Password: `tpp123`
   - Verifikasi menu ANP Target First dan Hasil ANP

3. **Login sebagai BIMKEMASWAT**:
   - Username: `bimkes02`
   - Password: `bimkes123`
   - Verifikasi menu Penilaian BIMKEMASWAT

4. **Login sebagai Kalapas**:
   - Username: `kalapas01`
   - Password: `kalapas123`
   - Verifikasi menu Validasi dan Laporan

5. **Login sebagai Wali**:
   - Username: `wali01`
   - Password: `wali123`
   - Verifikasi menu Dashboard Wali

Jika semua fitur berfungsi, update berhasil!