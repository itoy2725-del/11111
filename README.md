# Siber Güvenlik Başvuru Yönetim ve Operatör Takip Sistemi (CRM)

Bu proje, Meta Ads Lead Form üzerinden gelen siber güvenlik / kripto dolandırıcılığı başvurularını yönetmek, operatörlere atamak, görüşme notları ve görevleri takip etmek ve tüm işlemleri audit log sistemiyle kayıt altına almak için geliştirilmiş production seviyesinde bir CRM sistemidir.

---

## 🛠 Teknoloji Stack

- **Backend:** Laravel 12, PHP 8.3+, Eloquent ORM
- **Frontend:** Blade Templates, Tailwind CSS (lokal), Alpine.js (lokal)
- **Veritabanı:** MariaDB 10+ (`utf8mb4_unicode_ci`)
- **Sunucu:** Debian 12 VPS, Nginx, Supervisor, PHP-FPM

---

## 🚀 Kurulum Adımları

### 1. Depoyu Klonlayın ve Bağımlılıkları Yükleyin

```bash
git clone <repository-url>
cd <repository-folder>

# Composer bağımlılıklarını yükleyin
composer install --no-dev --optimize-autoloader

# NPM paketlerini yükleyin ve lokal asset'leri derleyin
npm install
npm run build
```

### 2. Çevre Değişkenleri (.env) Ayarı

`.env.example` dosyasını `.env` olarak kopyalayın ve veritabanı ayarlarını yapılandırın:

```bash
cp .env.example .env
php artisan key:generate
```

`.env` dosyanızda veritabanı bilgilerinizi güncelleyin:

```env
APP_NAME="Siber Güvenlik CRM"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://crm.siteniz.com

DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siber_guvenlik_crm
DB_USERNAME=crm_user
DB_PASSWORD=guclu_sifreniz
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

QUEUE_CONNECTION=database
```

### 3. Veritabanı Migration ve Seeder

```bash
# Tabloları oluşturun ve varsayılan verileri yükleyin
php artisan migrate --seed
```

---

## 🔑 Varsayılan Giriş Bilgileri

Seeder çalıştırıldığında oluşturulan varsayılan hesaplar:

| Rol | E-posta | Varsayılan Şifre |
|-----|---------|------------------|
| **Super Admin** | `admin@sistem.local` | `SiberCRM2024!` |
| **Operatör 1** | `ali@sistem.local` | `Operator2024!` |
| **Operatör 2** | `ayse@sistem.local` | `Operator2024!` |

> [!CAUTION]
> Production ortamında canlıya geçmeden önce bu şifreleri mutlaka değiştirin!

---

## 🐧 Debian 12 VPS Deploy ve Konfigürasyon Dokümantasyonu

### 1. Nginx Web Sunucusu Konfigürasyonu

`/etc/nginx/sites-available/siber-crm` dosyasını oluşturun:

```nginx
server {
    listen 80;
    server_name crm.siteniz.com;
    root /var/www/siber-crm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktif edin ve Nginx'i yeniden başlatın:

```bash
ln -s /etc/nginx/sites-available/siber-crm /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

### 2. Certbot ile SSL Kurulumu

```bash
apt install certbot python3-certbot-nginx -y
certbot --nginx -d crm.siteniz.com
```

### 3. Supervisor Queue Worker Ayarı

`/etc/supervisor/conf.d/siber-crm-worker.conf` dosyasını oluşturun:

```ini
[program:siber-crm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/siber-crm/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/siber-crm/storage/logs/worker.log
stopwaitsecs=3600
```

Supervisor servislerini başlatın:

```bash
supervisorctl reread
supervisorctl update
supervisorctl start siber-crm-worker:*
```

### 4. Cron Job (Scheduler) Ayarı

`crontab -e -u www-data` komutu ile cron ekleyin:

```cron
* * * * * cd /var/www/siber-crm && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔒 Güvenlik Özellikleri

- **Argon2id Password Hashing:** Şifreler Argon2id ile hash'lenir.
- **Session Timeout:** 120 dakika işlem yapılmadığında otomatik oturum kapatma.
- **Operatör Not Düzenleme Kısıtı:** Operatörler kendi notlarını ekledikten sonra ilk **15 dakika** içinde düzenleyebilir, daha sonra kilitlenir. Admin her zaman görebilir/düzenleyebilir.
- **Tam Audit Logging:** Tüm kritik veritabanı işlemleri (oluşturma, güncelleme, silme, operatör atama, CSV içe aktarma) IP adresi ve eski/yeni değerleriyle `audit_logs` tablosuna yazılır.
- **CSRF & XSS Protection:** Tüm formlar CSRF token ile korunur ve tüm çıktılar Blade motoruyla escape edilir.
