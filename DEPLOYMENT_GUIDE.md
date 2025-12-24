# Deployment Guide - Sistem Pendukung Keputusan Pembinaan Narapidana (SPK-Pembinaan)

## **Spesifikasi Sistem**
- **Sistem Operasi**: Ubuntu 24.04 LTS
- **PHP**: 8.3
- **Web Server**: Apache2 atau Nginx
- **Database**: MySQL 8.0+
- **Framework**: CodeIgniter 4
- **Frontend**: AdminLTE 3, Bootstrap 4, jQuery

---

## **1. Persiapan Server**

### **1.1 Update Sistem**
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common
```

### **1.2 Install PHP 8.3**
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-mbstring \
php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath
```

### **1.3 Install Web Server (Pilih salah satu)**

#### **Option A: Apache2**
```bash
sudo apt install -y apache2 libapache2-mod-php8.3
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

#### **Option B: Nginx**
```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### **1.4 Install MySQL**
```bash
sudo apt install -y mysql-server mysql-client
sudo mysql_secure_installation
```

### **1.5 Install Composer**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### **1.6 Install Git**
```bash
sudo apt install -y git
```

---

## **2. Konfigurasi Database**

### **2.1 Login ke MySQL**
```bash
sudo mysql -u root -p
```

### **2.2 Buat Database dan User**
```sql
-- Buat database
CREATE DATABASE penjara CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Buat user (ganti 'password_anda' dengan password yang kuat)
CREATE USER 'spk_user'@'localhost' IDENTIFIED BY 'password_anda';

-- Berikan hak akses
GRANT ALL PRIVILEGES ON penjara.* TO 'spk_user'@'localhost';

-- Terapkan perubahan
FLUSH PRIVILEGES;

-- Keluar dari MySQL
EXIT;
```

### **2.3 Import Database (Jika ada backup)**
```bash
mysql -u spk_user -p penjara < backup_database.sql
```

---

## **3. Deploy Aplikasi**

### **3.1 Clone Repository**
```bash
cd /var/www
sudo git clone [URL_REPOSITORY_ANDA] spk-pembinaan
cd spk-pembinaan
```

### **3.2 Atur Permission**
```bash
sudo chown -R www-data:www-data /var/www/spk-pembinaan
sudo chmod -R 755 /var/www/spk-pembinaan
sudo chmod -R 775 writable/
```

### **3.3 Install Dependencies**
```bash
composer install --no-dev --optimize-autoloader
```

### **3.4 Konfigurasi Environment**
```bash
# Copy file env example
cp .env.example .env

# Edit file .env
nano .env
```

**Konfigurasi penting di `.env`:**
```env
# Mode aplikasi
CI_ENVIRONMENT = production

# Base URL (sesuaikan dengan domain Anda)
app.baseURL = 'https://domain-anda.com'

# Database
database.default.hostname = localhost
database.default.database = penjara
database.default.username = spk_user
database.default.password = 'password_anda'
database.default.DBDriver = MySQLi
database.default.DBPrefix = ''
database.default.port = 3306
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_general_ci
```

**CATATAN PENTING:** Jangan tambahkan baris `session.savePath = WRITEPATH . 'session'` di file `.env` karena sudah ada di konfigurasi default CodeIgniter 4. Jika ditambahkan, akan menyebabkan error parsing.

### **3.5 Generate Encryption Key**
```bash
php spark key:generate
```

---

## **4. Konfigurasi Web Server**

### **4.1 Apache2 Configuration**
```bash
sudo nano /etc/apache2/sites-available/spk-pembinaan.conf
```

**Isi file:**
```apache
<VirtualHost *:80>
    ServerName domain-anda.com
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/spk-pembinaan/public

    <Directory /var/www/spk-pembinaan/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/spk-pembinaan_error.log
    CustomLog ${APACHE_LOG_DIR}/spk-pembinaan_access.log combined
</VirtualHost>
```

**Aktifkan site:**
```bash
sudo a2ensite spk-pembinaan.conf
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
```

### **4.2 Nginx Configuration**
```bash
sudo nano /etc/nginx/sites-available/spk-pembinaan
```

**Isi file:**
```nginx
server {
    listen 80;
    server_name domain-anda.com;
    root /var/www/spk-pembinaan/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

**Aktifkan site:**
```bash
sudo ln -s /etc/nginx/sites-available/spk-pembinaan /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## **5. Konfigurasi SSL (Opsional tapi Direkomendasikan)**

### **5.1 Install Certbot**
```bash
sudo apt install -y certbot python3-certbot-apache
# atau untuk Nginx:
sudo apt install -y certbot python3-certbot-nginx
```

### **5.2 Generate SSL Certificate**
```bash
# Apache
sudo certbot --apache -d domain-anda.com

# Nginx
sudo certbot --nginx -d domain-anda.com
```

---

## **6. Optimasi dan Keamanan**

### **6.1 Konfigurasi PHP-FPM**
```bash
sudo nano /etc/php/8.3/fpm/php.ini
```

**Parameter penting:**
```ini
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
date.timezone = Asia/Jakarta
```

```bash
sudo systemctl restart php8.3-fpm
```

### **6.2 Firewall Configuration**
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### **6.3 Scheduled Tasks (Cron Jobs)**
```bash
sudo crontab -e
```

**Tambahkan:**
```bash
# Backup database harian
0 2 * * * /usr/bin/mysqldump -u spk_user -p'password_anda' penjara > /var/backups/penjara_$(date +\%Y\%m\%d).sql

# Hapus backup lama (30 hari)
0 3 * * * find /var/backups -name "penjara_*.sql" -mtime +30 -delete
```

---

## **7. Database Migration dan Seeding**

### **7.1 Jalankan Migrasi**
```bash
cd /var/www/spk-pembinaan
php spark migrate
```

### **7.2 Jalankan Seeder (Data Awal)**
```bash
php spark db:seed ComprehensiveSeeder
```

### **7.3 Buat User Admin Default**
```sql
INSERT INTO users (username, password, nama_lengkap, role, created_at) 
VALUES ('admin', '$2y$10$YourHashedPasswordHere', 'Administrator Sistem', 'ADMIN', NOW());
```

**Untuk generate password hash:**
```bash
php -r "echo password_hash('password_admin', PASSWORD_DEFAULT);"
```

---

## **8. Testing dan Monitoring**

### **8.1 Test Aplikasi**
```bash
# Test routing
curl -I http://localhost/

# Test database connection
php spark db:table users
```

### **8.2 Monitoring Logs**
```bash
# Apache logs
sudo tail -f /var/log/apache2/spk-pembinaan_error.log

# Nginx logs
sudo tail -f /var/log/nginx/spk-pembinaan_error.log

# Application logs
tail -f /var/www/spk-pembinaan/writable/logs/log-*.log
```

### **8.3 Performance Monitoring**
```bash
# Install monitoring tools
sudo apt install -y htop nmon

# Check PHP-FPM status
sudo systemctl status php8.3-fpm

# Check MySQL status
sudo systemctl status mysql
```

---

## **9. Backup dan Recovery**

### **9.1 Backup Script**
Buat file `/usr/local/bin/backup-spk.sh`:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/spk-pembinaan"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u spk_user -p'password_anda' penjara > $BACKUP_DIR/db_$DATE.sql

# Backup aplikasi
tar -czf $BACKUP_DIR/app_$DATE.tar.gz --exclude="writable/logs/*" --exclude="writable/cache/*" /var/www/spk-pembinaan

# Hapus backup lama (7 hari)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

```bash
sudo chmod +x /usr/local/bin/backup-spk.sh
```

### **9.2 Recovery Procedure**
```bash
# Restore database
mysql -u spk_user -p penjara < backup_file.sql

# Restore aplikasi
tar -xzf backup_app.tar.gz -C /
```

---

## **10. Troubleshooting**

### **10.1 Common Issues**

#### **Issue 1: 500 Internal Server Error**
```bash
# Check error logs
sudo tail -f /var/log/apache2/error.log

# Check file permissions
sudo chmod -R 755 /var/www/spk-pembinaan
sudo chmod -R 775 /var/www/spk-pembinaan/writable
```

#### **Issue 2: Database Connection Error**
```bash
# Test database connection
mysql -u spk_user -p -e "SHOW DATABASES;"

# Check .env configuration
cat /var/www/spk-pembinaan/.env | grep database
```

#### **Issue 3: Session Not Working**
```bash
# Check session directory permissions
sudo chmod 775 /var/www/spk-pembinaan/writable/session
sudo chown www-data:www-data /var/www/spk-pembinaan/writable/session
```

#### **Issue 4: Error "Class CodeIgniter\Exceptions\InvalidArgumentException not found"**
```bash
# Penyebab: Dependencies belum terinstall dengan benar atau file .env format salah

# 1. Pastikan composer dependencies sudah diinstall
composer install --no-dev --optimize-autoloader

# 2. Hapus file .env yang salah dan buat ulang
rm .env
cp .env.example .env

# 3. Edit .env hanya dengan konfigurasi dasar (jangan tambahkan session.savePath)
nano .env

# 4. Coba jalankan spark command lagi
php spark key:generate
```

#### **Issue 5: Error parsing .env file**
```bash
# Penyebab: Format .env file salah

# 1. Hapus semua baris yang mengandung WRITEPATH atau konstanta PHP di .env
sed -i '/WRITEPATH/d' .env
sed -i '/FCPATH/d' .env
sed -i '/APPPATH/d' .env

# 2. Pastikan format .env benar:
#    - Gunakan format: key = value
#    - Jangan gunakan kutip ganda kecuali untuk string yang mengandung spasi
#    - Jangan tambahkan titik koma (;) di akhir baris
```

### **10.2 Performance Issues**
```bash
# Check PHP memory usage
sudo php -i | grep memory_limit

# Check MySQL slow queries
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# Tambahkan:
slow_query_log = 1
slow_query_log_file = /var/log/mysql/mysql-slow.log
long_query_time = 2
```

---

## **11. Maintenance**

### **11.1 Update Aplikasi**
```bash
cd /var/www/spk-pembinaan
sudo git pull origin main
composer install --no-dev --optimize-autoloader
php spark migrate
sudo systemctl restart apache2  # atau nginx
```

### **11.2 Cleanup**
```bash
# Clear cache
php spark cache:clear

# Clear logs (pertahankan 7 hari terakhir)
find /var/www/spk-pembinaan/writable/logs -name "*.log" -mtime +7 -delete
```

---

## **12. Kontak dan Support**

- **Developer**: Tim Pengembang SPK-Pembinaan
- **Email**: support@domain-anda.com
- **Documentation**: [Link ke dokumentasi]

---

## **Catatan Penting**

1. **Security First**: Selalu gunakan password yang kuat dan update secara berkala
2. **Regular Backup**: Lakukan backup harian database dan mingguan aplikasi
3. **Monitoring**: Pantau log dan performance secara rutin
4. **Update**: Update sistem operasi, PHP, dan dependencies secara berkala
5. **Testing**: Lakukan testing setelah setiap update atau perubahan konfigurasi

---

**Dokumen ini terakhir diperbarui: 24 Desember 2025**
