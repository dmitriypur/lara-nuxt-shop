# Настройка сервера для Nuxt Shop

Подробные инструкции по настройке сервера для развертывания приложения.

## Требования к серверу

### Минимальные требования
- **OS**: Ubuntu 20.04+ / CentOS 8+ / Debian 11+
- **RAM**: 2GB (рекомендуется 4GB+)
- **Storage**: 20GB SSD
- **CPU**: 2 cores
- **Network**: 100 Mbps

### Рекомендуемые требования
- **RAM**: 8GB+
- **Storage**: 50GB+ SSD
- **CPU**: 4+ cores
- **Network**: 1 Gbps

## Установка зависимостей

### Ubuntu/Debian

```bash
# Обновление системы
sudo apt update && sudo apt upgrade -y

# Установка основных пакетов
sudo apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release

# Добавление репозитория PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Установка PHP 8.2
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-redis php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath php8.2-soap php8.2-opcache

# Установка Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Установка Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Установка MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Установка Redis
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Установка Nginx
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### CentOS/RHEL

```bash
# Обновление системы
sudo dnf update -y

# Установка EPEL репозитория
sudo dnf install -y epel-release

# Установка Remi репозитория для PHP
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
sudo dnf module enable php:remi-8.2 -y

# Установка PHP 8.2
sudo dnf install -y php php-fpm php-mysqlnd php-redis php-mbstring php-xml php-curl php-zip php-gd php-intl php-bcmath php-soap php-opcache

# Установка Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Установка Node.js
sudo dnf module install nodejs:18 -y

# Установка MySQL
sudo dnf install -y mysql-server
sudo systemctl enable mysqld
sudo systemctl start mysqld
sudo mysql_secure_installation

# Установка Redis
sudo dnf install -y redis
sudo systemctl enable redis
sudo systemctl start redis

# Установка Nginx
sudo dnf install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

## Настройка MySQL

```bash
# Подключение к MySQL
sudo mysql -u root -p

# Создание базы данных и пользователя
CREATE DATABASE nuxt_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'nuxt_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON nuxt_shop.* TO 'nuxt_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Настройка Nginx

### Создание конфигурации сайта

```bash
sudo nano /etc/nginx/sites-available/nuxt-shop
```

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/nuxt-shop/backend/public;
    index index.php index.html;

    # Логи
    access_log /var/log/nginx/nuxt-shop.access.log;
    error_log /var/log/nginx/nuxt-shop.error.log;

    # Основной location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP обработка
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Статические файлы
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Безопасность
    location ~ /\. {
        deny all;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
}
```

### Активация сайта

```bash
sudo ln -s /etc/nginx/sites-available/nuxt-shop /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Настройка SSL (Let's Encrypt)

```bash
# Установка Certbot
sudo apt install -y certbot python3-certbot-nginx

# Получение сертификата
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Автоматическое обновление
sudo crontab -e
# Добавить строку:
0 12 * * * /usr/bin/certbot renew --quiet
```

## Настройка PHP-FPM

```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

Основные настройки:
```ini
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

```bash
sudo systemctl restart php8.2-fpm
```

## Настройка Firewall

```bash
# UFW (Ubuntu)
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable

# Firewalld (CentOS)
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

## Настройка Supervisor (для очередей)

```bash
# Установка
sudo apt install -y supervisor

# Создание конфигурации
sudo nano /etc/supervisor/conf.d/nuxt-shop-worker.conf
```

```ini
[program:nuxt-shop-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/nuxt-shop/backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/nuxt-shop/backend/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start nuxt-shop-worker:*
```

## Настройка Cron

```bash
sudo crontab -e
```

Добавить:
```bash
# Laravel Scheduler
* * * * * cd /var/www/nuxt-shop/backend && php artisan schedule:run >> /dev/null 2>&1

# Резервное копирование (каждый день в 2:00)
0 2 * * * cd /var/www/nuxt-shop && ./backup.sh >> /var/log/backup.log 2>&1
```

## Мониторинг и логи

### Установка логротации

```bash
sudo nano /etc/logrotate.d/nuxt-shop
```

```
/var/www/nuxt-shop/backend/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0644 www-data www-data
    postrotate
        systemctl reload php8.2-fpm
    endscript
}
```

### Мониторинг ресурсов

```bash
# Установка htop
sudo apt install -y htop

# Мониторинг дискового пространства
df -h

# Мониторинг памяти
free -h

# Мониторинг процессов
ps aux | grep php
```

## Оптимизация производительности

### Настройка OPcache

```bash
sudo nano /etc/php/8.2/fpm/conf.d/10-opcache.ini
```

```ini
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=0
opcache.fast_shutdown=1
```

### Настройка MySQL

```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Добавить в секцию [mysqld]:
```ini
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
query_cache_type = 1
query_cache_size = 256M
```

### Настройка Redis

```bash
sudo nano /etc/redis/redis.conf
```

```
maxmemory 512mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

## Безопасность

### Настройка fail2ban

```bash
sudo apt install -y fail2ban
sudo nano /etc/fail2ban/jail.local
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 3

[sshd]
enabled = true

[nginx-http-auth]
enabled = true

[nginx-limit-req]
enabled = true
```

### Обновление системы

```bash
# Автоматические обновления безопасности
sudo apt install -y unattended-upgrades
sudo dpkg-reconfigure -plow unattended-upgrades
```

## Резервное копирование

Создать скрипт `/var/www/nuxt-shop/backup.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/nuxt-shop"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Резервная копия базы данных
mysqldump -u nuxt_user -p'secure_password' nuxt_shop > $BACKUP_DIR/db_$DATE.sql

# Резервная копия файлов
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/nuxt-shop/backend/storage

# Удаление старых копий (старше 30 дней)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

```bash
chmod +x /var/www/nuxt-shop/backup.sh
```

## Проверка работоспособности

```bash
# Проверка статуса сервисов
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
sudo systemctl status redis

# Проверка логов
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/www/nuxt-shop/backend/storage/logs/laravel.log

# Проверка подключения к базе данных
mysql -u nuxt_user -p nuxt_shop -e "SELECT 1;"

# Проверка Redis
redis-cli ping
```

После выполнения всех настроек сервер будет готов для развертывания приложения Nuxt Shop.