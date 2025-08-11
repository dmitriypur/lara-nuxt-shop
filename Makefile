# Makefile для Nuxt Shop
# Упрощает команды развертывания и управления

.PHONY: help install dev prod test clean backup restore

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

help: ## Показать справку
	@echo "$(BLUE)Nuxt Shop - Команды управления$(NC)"
	@echo ""
	@echo "$(YELLOW)Разработка:$(NC)"
	@echo "  make install     - Установка зависимостей"
	@echo "  make dev         - Запуск для разработки"
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
	@echo "$(GREEN)Зависимости установлены!$(NC)"

dev: ## Запуск для разработки
	@echo "$(GREEN)Запуск в режиме разработки...$(NC)"
	docker-compose -f $(DOCKER_COMPOSE_DEV) up -d
	@echo "$(GREEN)Сервисы запущены!$(NC)"
	@echo "$(BLUE)Приложение доступно по адресу: http://localhost:8000$(NC)"

prod: ## Запуск в продакшене
	@echo "$(GREEN)Запуск в продакшене...$(NC)"
	docker-compose -f $(DOCKER_COMPOSE_PROD) up -d
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
	docker-compose exec mysql mysqldump -u root -p$(DB_ROOT_PASSWORD) $(DB_DATABASE) > backups/backup_$(shell date +%Y%m%d_%H%M%S).sql
	tar -czf backups/storage_$(shell date +%Y%m%d_%H%M%S).tar.gz $(BACKEND_DIR)/storage
	@echo "$(GREEN)Резервная копия создана!$(NC)"

restore: ## Восстановление из резервной копии
	@echo "$(YELLOW)Восстановление из резервной копии...$(NC)"
	@echo "$(RED)Внимание! Это перезапишет текущие данные!$(NC)"
	@read -p "Введите имя файла резервной копии: " backup_file; \
	docker-compose exec -T mysql mysql -u root -p$(DB_ROOT_PASSWORD) $(DB_DATABASE) < backups/$$backup_file
	@echo "$(GREEN)Восстановление завершено!$(NC)"

logs: ## Просмотр логов
	@echo "$(BLUE)Просмотр логов...$(NC)"
	docker-compose logs -f

shell: ## Подключение к контейнеру приложения
	@echo "$(BLUE)Подключение к контейнеру...$(NC)"
	docker-compose exec app bash

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
	docker-compose down
	@echo "$(GREEN)Сервисы остановлены!$(NC)"

restart: stop dev ## Перезапуск сервисов
	@echo "$(GREEN)Сервисы перезапущены!$(NC)"

status: ## Статус сервисов
	@echo "$(BLUE)Статус сервисов:$(NC)"
	docker-compose ps

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