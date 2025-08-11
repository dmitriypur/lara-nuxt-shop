# 🛍️ Nuxt Shop

Современное e-commerce приложение, построенное на Laravel + Filament 3 для админ-панели и API.

## 🚀 Особенности

- **Backend**: Laravel 10+ с Filament 3 админ-панелью
- **База данных**: MySQL с оптимизированными индексами
- **Кэширование**: Redis для сессий и кэша
- **Поиск**: Meilisearch для быстрого поиска товаров
- **Файлы**: Локальное хранилище с поддержкой S3
- **Очереди**: Redis-based очереди для фоновых задач
- **API**: RESTful API с аутентификацией
- **Безопасность**: CSRF защита, валидация, санитизация
- **Мониторинг**: Логирование и метрики производительности

## 📋 Требования

### Минимальные требования
- PHP 8.2+
- MySQL 8.0+ или MariaDB 10.4+
- Redis 6.0+
- Composer 2.0+
- Node.js 18+ (для сборки фронтенда)
- Nginx или Apache

### Рекомендуемые требования
- 4+ GB RAM
- 2+ CPU cores
- SSD диск
- SSL сертификат

## 🛠️ Быстрый старт

### Локальная разработка

```bash
# Клонирование репозитория
git clone <repository-url>
cd nuxt-shop

# Установка зависимостей
make install

# Настройка окружения
cp backend/.env.example backend/.env
# Отредактируйте .env файл с вашими настройками

# Генерация ключа приложения
cd backend
php artisan key:generate

# Миграции и сиды
php artisan migrate --seed

# Создание символической ссылки для storage
php artisan storage:link

# Запуск в режиме разработки
make dev
```

### Docker разработка

```bash
# Копирование переменных окружения
cp .env.example .env.docker
# Отредактируйте .env.docker

# Запуск с Docker
make docker-dev

# Выполнение миграций в контейнере
make docker-migrate

# Заполнение базы данных
make docker-seed
```

## 🚀 Развертывание на продакшене

### Автоматическое развертывание

1. **Подготовьте сервер** согласно [SERVER_SETUP.md](SERVER_SETUP.md)

2. **Настройте GitHub Secrets**:
   - `HOST` - IP адрес сервера
   - `USERNAME` - пользователь SSH
   - `SSH_KEY` - приватный SSH ключ
   - `PROJECT_PATH` - путь к проекту на сервере

3. **Создайте релиз** - GitHub Actions автоматически развернет приложение

### Ручное развертывание

```bash
# На сервере
git clone <repository-url> /var/www/nuxt-shop
cd /var/www/nuxt-shop

# Запуск скрипта развертывания
./deploy.sh production
```

Подробные инструкции в [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)

## 📁 Структура проекта

```
nuxt-shop/
├── backend/                 # Laravel приложение
│   ├── app/
│   │   ├── Filament/       # Filament админ-панель
│   │   ├── Http/           # Контроллеры и middleware
│   │   ├── Models/         # Eloquent модели
│   │   └── Services/       # Бизнес-логика
│   ├── database/
│   │   ├── migrations/     # Миграции базы данных
│   │   └── seeders/        # Сиды для тестовых данных
│   ├── routes/             # Маршруты API и web
│   └── tests/              # Тесты
├── docker/                 # Docker конфигурации
│   ├── nginx/              # Nginx конфигурация
│   ├── php/                # PHP-FPM конфигурация
│   ├── mysql/              # MySQL конфигурация
│   └── redis/              # Redis конфигурация
├── scripts/                # Скрипты развертывания
├── .github/workflows/      # GitHub Actions
├── docker-compose.yml      # Docker Compose конфигурация
├── Makefile               # Команды для разработки
└── README.md              # Этот файл
```

## 🔧 Доступные команды

### Make команды

```bash
# Разработка
make install          # Установка зависимостей
make dev             # Запуск в режиме разработки
make test            # Запуск тестов
make lint            # Проверка кода

# Docker
make docker-dev      # Запуск с Docker
make docker-prod     # Запуск в продакшене
make docker-migrate  # Миграции в Docker
make docker-seed     # Сиды в Docker

# Продакшен
make optimize        # Оптимизация для продакшена
make backup          # Создание резервной копии
make restore         # Восстановление из копии

# Утилиты
make logs           # Просмотр логов
make shell          # Подключение к контейнеру
make clean          # Очистка кэша и временных файлов
```

### Artisan команды

```bash
# Пользователи
php artisan make:filament-user  # Создание админа

# Кэш
php artisan cache:clear         # Очистка кэша
php artisan config:cache        # Кэширование конфигурации
php artisan route:cache         # Кэширование маршрутов
php artisan view:cache          # Кэширование шаблонов

# Очереди
php artisan queue:work          # Запуск воркера очередей
php artisan queue:restart       # Перезапуск воркеров

# Планировщик
php artisan schedule:run        # Запуск планировщика
php artisan schedule:list       # Список задач
```

## 🗄️ База данных

### Основные таблицы

- `users` - Пользователи системы
- `products` - Товары
- `categories` - Категории товаров
- `orders` - Заказы
- `order_items` - Позиции заказов
- `carts` - Корзины пользователей
- `reviews` - Отзывы на товары
- `coupons` - Купоны и скидки

### Миграции

```bash
# Выполнение миграций
php artisan migrate

# Откат миграций
php artisan migrate:rollback

# Статус миграций
php artisan migrate:status

# Создание новой миграции
php artisan make:migration create_table_name
```

## 🔐 Аутентификация и авторизация

### Роли пользователей

- **Super Admin** - Полный доступ ко всем функциям
- **Admin** - Управление товарами, заказами, пользователями
- **Manager** - Управление товарами и заказами
- **Customer** - Обычный покупатель

### API аутентификация

```bash
# Получение токена
POST /api/auth/login
{
    "email": "user@example.com",
    "password": "password"
}

# Использование токена
Authorization: Bearer {token}
```

## 📊 API документация

### Основные эндпоинты

```
GET    /api/products              # Список товаров
GET    /api/products/{id}         # Детали товара
GET    /api/categories            # Список категорий
POST   /api/cart/add              # Добавление в корзину
GET    /api/cart                  # Содержимое корзины
POST   /api/orders                # Создание заказа
GET    /api/orders                # История заказов
GET    /api/search                # Поиск товаров
```

### Фильтрация и сортировка

```
GET /api/products?category=electronics&sort=price&order=asc&page=1
GET /api/products?search=laptop&min_price=500&max_price=2000
```

## 🧪 Тестирование

### Запуск тестов

```bash
# Все тесты
php artisan test

# Конкретный тест
php artisan test --filter ProductTest

# С покрытием кода
php artisan test --coverage

# Параллельное выполнение
php artisan test --parallel
```

### Типы тестов

- **Unit тесты** - Тестирование отдельных классов и методов
- **Feature тесты** - Тестирование HTTP запросов и ответов
- **Browser тесты** - E2E тестирование с Laravel Dusk

## 📈 Мониторинг и логирование

### Логи

```bash
# Просмотр логов
tail -f storage/logs/laravel.log

# Логи через Artisan
php artisan log:clear
```

### Метрики

- Время ответа API
- Использование памяти
- Количество запросов
- Ошибки и исключения
- Производительность базы данных

## 🔧 Конфигурация

### Переменные окружения

```env
# Основные настройки
APP_NAME="Nuxt Shop"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# База данных
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nuxt_shop
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=

# Почта
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

### Кэширование

```php
// Кэширование данных
Cache::remember('products', 3600, function () {
    return Product::all();
});

// Тегированный кэш
Cache::tags(['products'])->flush();
```

## 🚀 Оптимизация производительности

### Laravel оптимизации

```bash
# Кэширование конфигурации
php artisan config:cache

# Кэширование маршрутов
php artisan route:cache

# Кэширование шаблонов
php artisan view:cache

# Кэширование событий
php artisan event:cache

# Оптимизация автозагрузчика
composer dump-autoload --optimize
```

### Настройки сервера

- **OPcache** - Кэширование скомпилированного PHP кода
- **Redis** - Кэширование данных и сессий
- **Nginx** - Кэширование статических файлов
- **Gzip** - Сжатие ответов

## 🔒 Безопасность

### Меры безопасности

- CSRF защита для всех форм
- XSS защита через экранирование
- SQL инъекции предотвращены через Eloquent ORM
- Валидация всех входящих данных
- Rate limiting для API
- HTTPS обязателен в продакшене
- Регулярные обновления зависимостей

### Настройки безопасности

```php
// Middleware для API
'throttle:api',
'auth:sanctum',

// CORS настройки
'allowed_origins' => ['https://your-frontend.com'],
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
```

## 🤝 Участие в разработке

### Процесс разработки

1. Форкните репозиторий
2. Создайте ветку для новой функции (`git checkout -b feature/amazing-feature`)
3. Зафиксируйте изменения (`git commit -m 'Add amazing feature'`)
4. Отправьте в ветку (`git push origin feature/amazing-feature`)
5. Создайте Pull Request

### Стандарты кода

- PSR-12 для PHP
- Laravel coding standards
- Обязательные тесты для новых функций
- Документация для публичных методов

## 📝 Лицензия

Этот проект лицензирован под MIT License - см. файл [LICENSE](LICENSE) для деталей.

## 📞 Поддержка

- **Документация**: [Ссылка на документацию]
- **Issues**: [GitHub Issues](https://github.com/your-username/nuxt-shop/issues)
- **Discussions**: [GitHub Discussions](https://github.com/your-username/nuxt-shop/discussions)
- **Email**: support@your-domain.com

## 🙏 Благодарности

- [Laravel](https://laravel.com) - PHP фреймворк
- [Filament](https://filamentphp.com) - Админ-панель
- [Meilisearch](https://meilisearch.com) - Поисковый движок
- [Redis](https://redis.io) - Кэширование и очереди

---

**Сделано с ❤️ для современной e-commerce разработки**