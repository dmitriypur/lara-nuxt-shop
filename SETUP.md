# 🚀 Инструкция по запуску Nuxt Shop

## Требования

- **Docker Desktop** - для запуска сервисов (MySQL, Redis, Meilisearch, MinIO)
- **PHP 8.2+** - для Laravel backend
- **Composer** - для управления зависимостями PHP
- **Node.js 18+** - для Nuxt frontend
- **npm** - для управления зависимостей JavaScript

## Быстрый запуск

### 1. Клонирование и переход в проект
```bash
cd /Applications/MAMP/htdocs/nuxt-shop
```

### 2. Запуск Docker сервисов
```bash
# Запуск всех сервисов в фоне
docker compose up -d

# Проверка статуса контейнеров
docker ps
```

**Запущенные сервисы:**
- MySQL: `localhost:3306`
- Redis: `localhost:6379`
- Meilisearch: `localhost:7700`
- MinIO: `localhost:9001`

### 3. Настройка Laravel Backend

```bash
# Переход в папку backend
cd backend

# Установка зависимостей
composer install

# Копирование конфигурации
cp .env.example .env

# Генерация ключа приложения
php artisan key:generate

# Запуск миграций
php artisan migrate

# Создание админа для Filament
php artisan make:filament-user

# Настроить поиск
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
php artisan scout:import "App\Models\Product"
```

**Настройки .env для Docker:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nuxt_shop
DB_USERNAME=root
DB_PASSWORD=secret

SESSION_DRIVER=redis
CACHE_STORE=redis
REDIS_CLIENT=predis

# Настроить поиск через Meilisearch
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=
```

> **Примечание**: Используется `predis` клиент для Redis и `meilisearch` для поиска.

### 4. Запуск Laravel сервера
```bash
# В папке backend
php artisan serve
```
Сервер будет доступен на: `http://localhost:8000`

### 5. Настройка и запуск Nuxt Frontend

```bash
# Переход в папку frontend (новый терминал)
cd frontend

# Установка зависимостей
npm install

# Запуск dev сервера
npm run dev
```
Frontend будет доступен на: `http://localhost:3000`

## Доступ к приложению

| Сервис | URL | Описание |
|--------|-----|----------|
| **Frontend** | http://localhost:3000 | Nuxt.js приложение |
| **Backend API** | http://localhost:8000 | Laravel API |
| **Admin панель** | http://localhost:8000/admin | Filament админка |
| **Meilisearch** | http://localhost:7700 | Поисковый движок |
| **MinIO** | http://localhost:9001 | Файловое хранилище |

## Альтернативный запуск через Makefile

```bash
# Установка зависимостей
make install

# Запуск для разработки
make dev

# Просмотр всех команд
make help
```

## Остановка проекта

```bash
# Остановка Docker сервисов
docker compose down

# Остановка Laravel сервера: Ctrl+C в терминале
# Остановка Nuxt сервера: Ctrl+C в терминале
```

## Troubleshooting

### Проблема: Docker не запускается
```bash
# Проверить статус Docker Desktop
docker --version

# Перезапустить сервисы
docker compose down
docker compose up -d
```

### Проблема: Ошибка подключения к БД
```bash
# Проверить контейнеры
docker ps

# Проверить логи MySQL
docker logs mysql

# Пересоздать контейнеры
docker compose down -v
docker compose up -d
```

### Проблема: Порты заняты
```bash
# Найти процессы на портах
lsof -i :3000  # Nuxt
lsof -i :8000  # Laravel
lsof -i :3306  # MySQL

# Убить процесс
kill -9 <PID>
```

### Проблема: Composer зависимости
```bash
# Очистить кэш
composer clear-cache

# Переустановить зависимости
rm -rf vendor
composer install
```

### Проблема: NPM зависимости
```bash
# Очистить кэш
npm cache clean --force

# Переустановить зависимости
rm -rf node_modules package-lock.json
npm install
```

### Проблема: Class "Redis" not found
```bash
# Изменить клиент Redis в .env
REDIS_CLIENT=predis

# Очистить кэш
php artisan config:clear
php artisan cache:clear
```

**Причина**: Нужно использовать `predis` клиент вместо `phpredis` для подключения к Docker контейнеру.

## Полезные команды

### Laravel
```bash
# Очистка кэша
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Просмотр маршрутов
php artisan route:list

# Создание миграции
php artisan make:migration create_table_name

# Откат миграций
php artisan migrate:rollback
```

### Docker
```bash
# Просмотр логов
docker compose logs -f

# Подключение к контейнеру
docker exec -it mysql bash
docker exec -it redis redis-cli

# Очистка всех данных
docker compose down -v
```

### Nuxt
```bash
# Сборка для продакшена
npm run build

# Предпросмотр продакшен сборки
npm run preview

# Генерация статических файлов
npm run generate
```

## Структура проекта

```
nuxt-shop/
├── backend/          # Laravel API + Filament Admin
├── frontend/         # Nuxt.js приложение
├── docker/           # Docker конфигурации
├── docker-compose.yml # Docker сервисы
└── Makefile          # Команды автоматизации
```

## Разработка

1. **Backend**: Используй Filament для управления данными
2. **Frontend**: Nuxt.js с Tailwind CSS для UI
3. **API**: RESTful API между frontend и backend
4. **База данных**: MySQL с миграциями Laravel
5. **Кэширование**: Redis для сессий и кэша
6. **Поиск**: Meilisearch для быстрого поиска товаров

## Первые шаги после запуска

1. Зайди в админку: http://localhost:8000/admin
2. Создай категории товаров
3. Добавь несколько товаров
4. Проверь frontend: http://localhost:3000
5. Протестируй API: http://localhost:8000/api

Готово! Проект запущен и готов к разработке! 🎉