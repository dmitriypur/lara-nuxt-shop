#!/bin/bash

# Скрипт автоматического развертывания Nuxt Shop
# Использование: ./deploy.sh [production|staging]

set -e

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Функции для вывода
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "$1"
}

# Проверка аргументов
ENV=${1:-production}

print_info "🚀 Начинаем развертывание Nuxt Shop (окружение: $ENV)"

# Проверка требований
print_info "\n📋 Проверка системных требований..."

# Проверка PHP
if ! command -v php &> /dev/null; then
    print_error "PHP не установлен"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
if [[ "$PHP_VERSION" < "8.2" ]]; then
    print_error "Требуется PHP 8.2+, установлена версия $PHP_VERSION"
    exit 1
fi
print_success "PHP $PHP_VERSION"

# Проверка Composer
if ! command -v composer &> /dev/null; then
    print_error "Composer не установлен"
    exit 1
fi
print_success "Composer $(composer --version --no-ansi | cut -d' ' -f3)"

# Проверка MySQL
if ! command -v mysql &> /dev/null; then
    print_warning "MySQL клиент не найден, убедитесь что MySQL сервер доступен"
else
    print_success "MySQL клиент найден"
fi

# Переход в директорию backend
cd backend

# Установка зависимостей
print_info "\n📦 Установка зависимостей..."
if [ "$ENV" = "production" ]; then
    composer install --optimize-autoloader --no-dev --no-interaction
else
    composer install --optimize-autoloader --no-interaction
fi
print_success "Зависимости установлены"

# Настройка окружения
print_info "\n⚙️ Настройка окружения..."
if [ ! -f .env ]; then
    cp .env.example .env
    print_warning "Создан .env файл из .env.example. Отредактируйте его перед продолжением!"
    print_info "Основные настройки для изменения:"
    print_info "- APP_URL (URL вашего сайта)"
    print_info "- DB_* (настройки базы данных)"
    print_info "- MAIL_* (настройки почты)"
    print_info "- Другие сервисы (Redis, Meilisearch)"
    
    read -p "Нажмите Enter после редактирования .env файла..."
fi

# Генерация ключа приложения
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate --force
    print_success "Ключ приложения сгенерирован"
else
    print_success "Ключ приложения уже существует"
fi

# Создание символической ссылки для storage
if [ ! -L public/storage ]; then
    php artisan storage:link
    print_success "Символическая ссылка для storage создана"
fi

# Миграции базы данных
print_info "\n🗄️ Настройка базы данных..."
if [ "$ENV" = "production" ]; then
    php artisan migrate --force
else
    php artisan migrate
fi
print_success "Миграции выполнены"

# Сидеры (только для staging)
if [ "$ENV" = "staging" ]; then
    php artisan db:seed
    print_success "Тестовые данные загружены"
fi

# Оптимизация для продакшена
if [ "$ENV" = "production" ]; then
    print_info "\n🚀 Оптимизация для продакшена..."
    
    php artisan config:cache
    print_success "Конфигурация кэширована"
    
    php artisan route:cache
    print_success "Маршруты кэшированы"
    
    php artisan view:cache
    print_success "Шаблоны кэшированы"
    
    php artisan event:cache
    print_success "События кэшированы"
else
    print_info "\n🧹 Очистка кэша для разработки..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    print_success "Кэш очищен"
fi

# Настройка прав доступа
print_info "\n🔐 Настройка прав доступа..."
chmod -R 775 storage bootstrap/cache
print_success "Права доступа настроены"

# Проверка конфигурации веб-сервера
print_info "\n🌐 Информация о веб-сервере:"
print_info "Document Root должен указывать на: $(pwd)/public"
print_info "Пример конфигурации Nginx:"
print_info ""
print_info "server {"
print_info "    listen 80;"
print_info "    server_name yourdomain.com;"
print_info "    root $(pwd)/public;"
print_info ""
print_info "    add_header X-Frame-Options \"SAMEORIGIN\";"
print_info "    add_header X-Content-Type-Options \"nosniff\";"
print_info ""
print_info "    index index.php;"
print_info ""
print_info "    charset utf-8;"
print_info ""
print_info "    location / {"
print_info "        try_files \$uri \$uri/ /index.php?\$query_string;"
print_info "    }"
print_info ""
print_info "    location = /favicon.ico { access_log off; log_not_found off; }"
print_info "    location = /robots.txt  { access_log off; log_not_found off; }"
print_info ""
print_info "    error_page 404 /index.php;"
print_info ""
print_info "    location ~ \\.php\$ {"
print_info "        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;"
print_info "        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;"
print_info "        include fastcgi_params;"
print_info "    }"
print_info ""
print_info "    location ~ /\\.(?!well-known).* {"
print_info "        deny all;"
print_info "    }"
print_info "}"

print_success "\n🎉 Развертывание завершено успешно!"
print_info "\n📝 Следующие шаги:"
print_info "1. Настройте веб-сервер (Nginx/Apache)"
print_info "2. Настройте SSL сертификат"
print_info "3. Создайте администратора: php artisan make:filament-user"
print_info "4. Настройте cron для планировщика задач:"
print_info "   * * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
print_info "5. Настройте supervisor для очередей (если используются)"
print_info "\nАдмин-панель будет доступна по адресу: {APP_URL}/admin"

print_success "Готово! 🚀"