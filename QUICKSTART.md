# ⚡ Быстрый старт

## 1. Запуск Docker сервисов
```bash
docker compose up -d
```

## 2. Backend (Laravel)
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan make:filament-user
php artisan serve
```

## 3. Frontend (Nuxt)
```bash
cd frontend
npm install
npm run dev
```

## Готово! 🎉

- **Frontend**: http://localhost:3000
- **Admin**: http://localhost:8000/admin
- **API**: http://localhost:8000

Подробная инструкция: [SETUP.md](./SETUP.md)