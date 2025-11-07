# Makefile для Nuxt Shop
# Упрощает команды развертывания и управления

.PHONY: help install dev prod test clean backup restore \
        backend-serve backend-vite frontend-dev dev-all stop-all status-all

# Цвета для вывода
RED=\033[0;31m
GREEN=\033[0;32m
YELLOW=\033[1;33m
BLUE=\033[0;34m
NC=\033[0m # No Color

# Переменные
DOCKER_COMPOSE_DEV=docker-compose.yml
DOCKER_COMPOSE_PROD=docker-compose.prod.yml
BACKEND_DIR=backend
FRONTEND_DIR=frontend
DOCKER_CLI := $(shell if command -v docker-compose >/dev/null 2>&1; then echo docker-compose; else echo docker compose; fi)

help: ## Показать справку
	@echo "$(BLUE)Nuxt Shop - Команды управления$(NC)"
	@echo ""
	@echo "$(YELLOW)Разработка:$(NC)"
	@echo "  make install     - Установка зависимостей"
	@echo "  make dev         - Запуск для разработки"
	@echo "  make dev-all     - Запуск Docker + бэкенд + фронт из корня"
	@echo "  make test        - Запуск тестов"
	@echo "  make lint        - Проверка кода"
	@echo ""
	@echo "$(YELLOW)Продакшен:$(NC)"
	@echo "  make prod        - Запуск в продакшене"
	@echo "  make deploy      - Развертывание"
	@echo "  make optimize    - Оптимизация для продакшена"
	@echo ""
	@echo "$(YELLOW)Утилиты:$(NC)"
	@echo "  make clean       - Очистка"
	@echo "  make backup      - Резервное копирование"
	@echo "  make restore     - Восстановление из резервной копии"
	@echo "  make logs        - Просмотр логов"
	@echo "  make shell       - Подключение к контейнеру"

install: ## Установка зависимостей
	@echo "$(GREEN)Установка зависимостей...$(NC)"
	cd $(BACKEND_DIR) && composer install
	cd $(BACKEND_DIR) && npm install
	cd $(FRONTEND_DIR) && npm install
	@echo "$(GREEN)Зависимости установлены!$(NC)"

dev: ## Запуск для разработки
	@echo "$(GREEN)Запуск в режиме разработки...$(NC)"
	$(DOCKER_CLI) -f $(DOCKER_COMPOSE_DEV) up -d
	@echo "$(GREEN)Сервисы запущены!$(NC)"
	@echo "$(BLUE)Инфраструктура запущена (MySQL/Redis/Meili/Minio).$(NC)"
	@echo "$(BLUE)Запустите бэкенд и фронт: make dev-all$(NC)"

dev-all: ## Запуск Docker + бэкенд (PHP + Vite) + фронт (Nuxt)
	@echo "$(GREEN)Запуск всех сервисов из корня...$(NC)"
	mkdir -p .pids logs
	$(DOCKER_CLI) -f $(DOCKER_COMPOSE_DEV) up -d
	$(MAKE) backend-serve
	$(MAKE) backend-vite
	$(MAKE) frontend-dev
	@echo "$(GREEN)Готово!$(NC)"
	@echo "$(BLUE)Backend (Laravel):    http://127.0.0.1:8000$(NC)"
	@echo "$(BLUE)Frontend (Nuxt 3):     http://127.0.0.1:3000$(NC)"
	@echo "$(BLUE)Vite (Laravel assets): как правило на http://127.0.0.1:5173$(NC)"

backend-serve: ## PHP сервер Laravel (artisan serve)
	@echo "$(GREEN)Старт PHP сервера бэкенда...$(NC)"
	mkdir -p .pids logs
	cd $(BACKEND_DIR) && nohup php artisan serve --host=127.0.0.1 --port=8000 > $(CURDIR)/logs/backend-php.log 2>&1 & echo $$! > $(CURDIR)/.pids/backend-php.pid
	@echo "$(BLUE)Laravel PHP: http://127.0.0.1:8000 (PID: $$(cat .pids/backend-php.pid))$(NC)"

backend-vite: ## Vite для Laravel (assets, hot reload)
	@echo "$(GREEN)Старт Vite для бэкенда...$(NC)"
	mkdir -p .pids logs
	cd $(BACKEND_DIR) && nohup npm run dev > $(CURDIR)/logs/backend-vite.log 2>&1 & echo $$! > $(CURDIR)/.pids/backend-vite.pid
	@echo "$(BLUE)Laravel Vite PID: $$(cat .pids/backend-vite.pid) (лог: logs/backend-vite.log)$(NC)"

frontend-dev: ## Nuxt 3 dev сервер фронта
	@echo "$(GREEN)Старт Nuxt фронта...$(NC)"
	mkdir -p .pids logs
	cd $(FRONTEND_DIR) && nohup npm run dev > $(CURDIR)/logs/frontend-dev.log 2>&1 & echo $$! > $(CURDIR)/.pids/frontend-dev.pid
	@echo "$(BLUE)Nuxt: http://127.0.0.1:3000 (PID: $$(cat .pids/frontend-dev.pid))$(NC)"

stop-all: ## Остановить фронт/бэк и Docker
	@echo "$(YELLOW)Остановка всех локальных процессов...$(NC)"
	@if [ -f .pids/frontend-dev.pid ]; then kill $$(cat .pids/frontend-dev.pid) || true; rm -f .pids/frontend-dev.pid; echo "Stopped Nuxt"; fi
	@if [ -f .pids/backend-vite.pid ]; then kill $$(cat .pids/backend-vite.pid) || true; rm -f .pids/backend-vite.pid; echo "Stopped Laravel Vite"; fi
	@if [ -f .pids/backend-php.pid ]; then kill $$(cat .pids/backend-php.pid) || true; rm -f .pids/backend-php.pid; echo "Stopped Laravel PHP"; fi
	$(DOCKER_CLI) down
	@echo "$(GREEN)Все процессы остановлены.$(NC)"

status-all: ## Проверить статусы
	@echo "$(BLUE)Docker:$(NC)"; $(DOCKER_CLI) ps
	@echo "$(BLUE)Процессы:$(NC)"
	@if [ -f .pids/backend-php.pid ]; then ps -p $$(cat .pids/backend-php.pid) || true; else echo "backend-php: not running"; fi
	@if [ -f .pids/backend-vite.pid ]; then ps -p $$(cat .pids/backend-vite.pid) || true; else echo "backend-vite: not running"; fi
	@if [ -f .pids/frontend-dev.pid ]; then ps -p $$(cat .pids/frontend-dev.pid) || true; else echo "frontend-dev: not running"; fi

prod: ## Запуск в продакшене
	@echo "$(GREEN)Запуск в продакшене...$(NC)"
	$(DOCKER_CLI) -f $(DOCKER_COMPOSE_PROD) up -d
	@echo "$(GREEN)Продакшен запущен!$(NC)"

deploy: ## Развертывание
	@echo "$(GREEN)Развертывание приложения...$(NC)"
	./deploy.sh production
	@echo "$(GREEN)Развертывание завершено!$(NC)"

test: ## Запуск тестов
	@echo "$(GREEN)Запуск тестов...$(NC)"
	cd $(BACKEND_DIR) && vendor/bin/phpunit
	@echo "$(GREEN)Тесты завершены!$(NC)"

lint: ## Проверка кода
	@echo "$(GREEN)Проверка стиля кода...$(NC)"
	cd $(BACKEND_DIR) && vendor/bin/pint
	@echo "$(GREEN)Проверка завершена!$(NC)"

optimize: ## Оптимизация для продакшена
	@echo "$(GREEN)Оптимизация приложения...$(NC)"
	cd $(BACKEND_DIR) && php artisan config:cache
	cd $(BACKEND_DIR) && php artisan route:cache
	cd $(BACKEND_DIR) && php artisan view:cache
	cd $(BACKEND_DIR) && php artisan event:cache
	@echo "$(GREEN)Оптимизация завершена!$(NC)"

clean: ## Очистка
	@echo "$(YELLOW)Очистка кэша и временных файлов...$(NC)"
	cd $(BACKEND_DIR) && php artisan cache:clear
	cd $(BACKEND_DIR) && php artisan config:clear
	cd $(BACKEND_DIR) && php artisan route:clear
	cd $(BACKEND_DIR) && php artisan view:clear
	docker system prune -f
	@echo "$(GREEN)Очистка завершена!$(NC)"

backup: ## Резервное копирование
	@echo "$(GREEN)Создание резервной копии...$(NC)"
	mkdir -p backups
	$(DOCKER_CLI) exec mysql mysqldump -u root -p$(DB_ROOT_PASSWORD) $(DB_DATABASE) > backups/backup_$(shell date +%Y%m%d_%H%M%S).sql
	tar -czf backups/storage_$(shell date +%Y%m%d_%H%M%S).tar.gz $(BACKEND_DIR)/storage
	@echo "$(GREEN)Резервная копия создана!$(NC)"

restore: ## Восстановление из резервной копии
	@echo "$(YELLOW)Восстановление из резервной копии...$(NC)"
	@echo "$(RED)Внимание! Это перезапишет текущие данные!$(NC)"
	@read -p "Введите имя файла резервной копии: " backup_file; \
	$(DOCKER_CLI) exec -T mysql mysql -u root -p$(DB_ROOT_PASSWORD) $(DB_DATABASE) < backups/$$backup_file
	@echo "$(GREEN)Восстановление завершено!$(NC)"

logs: ## Просмотр логов
	@echo "$(BLUE)Просмотр логов...$(NC)"
	$(DOCKER_CLI) logs -f

shell: ## Подключение к контейнеру приложения
	@echo "$(BLUE)Подключение к контейнеру...$(NC)"
	$(DOCKER_CLI) exec app bash

migrate: ## Выполнение миграций
	@echo "$(GREEN)Выполнение миграций...$(NC)"
	cd $(BACKEND_DIR) && php artisan migrate
	@echo "$(GREEN)Миграции выполнены!$(NC)"

seed: ## Заполнение тестовыми данными
	@echo "$(GREEN)Заполнение тестовыми данными...$(NC)"
	cd $(BACKEND_DIR) && php artisan db:seed
	@echo "$(GREEN)Данные загружены!$(NC)"

fresh: ## Пересоздание базы данных
	@echo "$(YELLOW)Пересоздание базы данных...$(NC)"
	cd $(BACKEND_DIR) && php artisan migrate:fresh --seed
	@echo "$(GREEN)База данных пересоздана!$(NC)"

stop: ## Остановка всех сервисов
	@echo "$(YELLOW)Остановка сервисов...$(NC)"
	$(DOCKER_CLI) down
	@echo "$(GREEN)Сервисы остановлены!$(NC)"

restart: stop dev ## Перезапуск сервисов
	@echo "$(GREEN)Сервисы перезапущены!$(NC)"

status: ## Статус сервисов
	@echo "$(BLUE)Статус сервисов:$(NC)"
	$(DOCKER_CLI) ps

update: ## Обновление зависимостей
	@echo "$(GREEN)Обновление зависимостей...$(NC)"
	cd $(BACKEND_DIR) && composer update
	@echo "$(GREEN)Зависимости обновлены!$(NC)"

key: ## Генерация ключа приложения
	@echo "$(GREEN)Генерация ключа приложения...$(NC)"
	cd $(BACKEND_DIR) && php artisan key:generate
	@echo "$(GREEN)Ключ сгенерирован!$(NC)"

link: ## Создание символической ссылки для storage
	@echo "$(GREEN)Создание символической ссылки...$(NC)"
	cd $(BACKEND_DIR) && php artisan storage:link
	@echo "$(GREEN)Ссылка создана!$(NC)"