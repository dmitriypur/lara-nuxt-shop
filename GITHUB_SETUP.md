# 🔧 Настройка GitHub репозитория для развертывания

Пошаговые инструкции по настройке GitHub репозитория для автоматического развертывания Nuxt Shop.

## 📋 Подготовка репозитория

### 1. Создание репозитория

1. Создайте новый репозиторий на GitHub
2. Клонируйте его локально или добавьте remote к существующему проекту:

```bash
# Для нового репозитория
git clone https://github.com/your-username/nuxt-shop.git
cd nuxt-shop

# Для существующего проекта
git remote add origin https://github.com/your-username/nuxt-shop.git
```

### 2. Загрузка кода

```bash
# Добавление всех файлов
git add .

# Коммит
git commit -m "Initial commit: Complete Nuxt Shop application"

# Отправка в репозиторий
git push -u origin main
```

## 🔐 Настройка секретов GitHub

Для автоматического развертывания необходимо настроить секреты в GitHub.

### Переход к настройкам секретов

1. Откройте ваш репозиторий на GitHub
2. Перейдите в **Settings** → **Secrets and variables** → **Actions**
3. Нажмите **New repository secret**

### Обязательные секреты

#### `HOST`
- **Описание**: IP адрес или домен вашего сервера
- **Пример**: `192.168.1.100` или `your-server.com`

#### `USERNAME`
- **Описание**: Имя пользователя для SSH подключения
- **Пример**: `ubuntu`, `root`, `deploy`

#### `SSH_KEY`
- **Описание**: Приватный SSH ключ для подключения к серверу
- **Получение**:
  ```bash
  # Генерация SSH ключа (если нет)
  ssh-keygen -t rsa -b 4096 -C "deploy@nuxt-shop"
  
  # Копирование приватного ключа
  cat ~/.ssh/id_rsa
  ```
- **Важно**: Скопируйте весь ключ, включая `-----BEGIN OPENSSH PRIVATE KEY-----` и `-----END OPENSSH PRIVATE KEY-----`

#### `PORT`
- **Описание**: Порт SSH (обычно 22)
- **Значение**: `22`

#### `PROJECT_PATH`
- **Описание**: Путь к проекту на сервере
- **Пример**: `/var/www/nuxt-shop`

### Опциональные секреты

#### `SLACK_WEBHOOK`
- **Описание**: Webhook URL для уведомлений в Slack
- **Получение**: Создайте Incoming Webhook в настройках Slack

#### `CODECOV_TOKEN`
- **Описание**: Токен для загрузки покрытия кода в Codecov
- **Получение**: Зарегистрируйтесь на codecov.io и получите токен

## 🔑 Настройка SSH ключей на сервере

### 1. Добавление публичного ключа на сервер

```bash
# На локальной машине - копирование публичного ключа
cat ~/.ssh/id_rsa.pub

# На сервере - добавление ключа
mkdir -p ~/.ssh
echo "ваш_публичный_ключ" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

### 2. Проверка SSH подключения

```bash
# Тест подключения
ssh -i ~/.ssh/id_rsa username@your-server.com
```

## 🚀 Настройка GitHub Actions

### Файлы workflow уже созданы:

- `.github/workflows/deploy.yml` - Основной workflow для развертывания
- `.github/workflows/code-quality.yml` - Проверка качества кода

### Триггеры развертывания

Развертывание запускается при:
- Push в ветку `main`
- Создании релиза (тега)
- Ручном запуске через GitHub Actions UI

### Ручной запуск развертывания

1. Перейдите в **Actions** в вашем репозитории
2. Выберите **Deploy to Production**
3. Нажмите **Run workflow**
4. Выберите ветку и нажмите **Run workflow**

## 📦 Создание релизов

### Автоматическое создание релиза

```bash
# Создание тега
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

### Ручное создание релиза

1. Перейдите в **Releases** в вашем репозитории
2. Нажмите **Create a new release**
3. Введите тег версии (например, `v1.0.0`)
4. Добавьте описание изменений
5. Нажмите **Publish release**

## 🔍 Мониторинг развертывания

### Просмотр логов GitHub Actions

1. Перейдите в **Actions** в вашем репозитории
2. Выберите нужный workflow run
3. Просмотрите логи каждого шага

### Статусы развертывания

- ✅ **Success** - Развертывание прошло успешно
- ❌ **Failure** - Ошибка в процессе развертывания
- 🟡 **In Progress** - Развертывание в процессе
- ⏸️ **Cancelled** - Развертывание отменено

## 🛠️ Настройка окружений

### Создание окружений в GitHub

1. Перейдите в **Settings** → **Environments**
2. Нажмите **New environment**
3. Создайте окружения:
   - `development`
   - `staging`
   - `production`

### Настройка правил защиты

Для продакшен окружения:
- ✅ **Required reviewers** - Обязательные ревьюеры
- ✅ **Wait timer** - Задержка перед развертыванием
- ✅ **Deployment branches** - Ограничение веток

## 📊 Настройка уведомлений

### Slack уведомления

1. Создайте Incoming Webhook в Slack:
   - Перейдите в настройки Slack
   - Apps → Incoming Webhooks
   - Add to Slack
   - Выберите канал
   - Скопируйте Webhook URL

2. Добавьте URL в секреты GitHub как `SLACK_WEBHOOK`

### Email уведомления

GitHub автоматически отправляет email уведомления:
- При неудачном развертывании
- При изменении статуса workflow

Настройка в **Settings** → **Notifications**

## 🔧 Отладка проблем

### Частые проблемы и решения

#### SSH подключение не работает
```bash
# Проверка SSH ключа
ssh -vvv -i ~/.ssh/id_rsa username@server

# Проверка прав доступа
ls -la ~/.ssh/
chmod 600 ~/.ssh/id_rsa
chmod 644 ~/.ssh/id_rsa.pub
```

#### Ошибки прав доступа на сервере
```bash
# Установка правильных прав
sudo chown -R www-data:www-data /var/www/nuxt-shop
sudo chmod -R 755 /var/www/nuxt-shop
sudo chmod -R 775 /var/www/nuxt-shop/backend/storage
sudo chmod -R 775 /var/www/nuxt-shop/backend/bootstrap/cache
```

#### Composer ошибки
```bash
# Очистка кэша Composer
composer clear-cache

# Обновление Composer
composer self-update

# Установка с игнорированием платформенных требований
composer install --ignore-platform-reqs
```

#### База данных недоступна
```bash
# Проверка статуса MySQL
sudo systemctl status mysql

# Перезапуск MySQL
sudo systemctl restart mysql

# Проверка подключения
mysql -u username -p -h localhost
```

### Логи для отладки

```bash
# Логи GitHub Actions
# Доступны в веб-интерфейсе GitHub

# Логи сервера
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# Логи приложения
tail -f /var/www/nuxt-shop/backend/storage/logs/laravel.log

# Системные логи
sudo journalctl -f
```

## 📋 Чек-лист настройки

### GitHub репозиторий
- [ ] Репозиторий создан
- [ ] Код загружен
- [ ] Секреты настроены
- [ ] SSH ключи добавлены
- [ ] Workflow файлы на месте

### Сервер
- [ ] SSH доступ настроен
- [ ] Публичный ключ добавлен
- [ ] Права доступа настроены
- [ ] Веб-сервер настроен
- [ ] База данных настроена

### Тестирование
- [ ] SSH подключение работает
- [ ] Workflow запускается
- [ ] Развертывание проходит успешно
- [ ] Сайт доступен
- [ ] Уведомления работают

## 🎯 Следующие шаги

1. **Настройте мониторинг** - добавьте системы мониторинга
2. **Настройте резервное копирование** - автоматические бэкапы
3. **Добавьте тесты** - расширьте покрытие тестами
4. **Настройте staging** - промежуточное окружение
5. **Документируйте процессы** - создайте runbook

## 📞 Поддержка

Если возникли проблемы:
1. Проверьте логи GitHub Actions
2. Проверьте логи сервера
3. Убедитесь в правильности секретов
4. Проверьте SSH подключение
5. Создайте issue в репозитории

---

**Готово! Ваше приложение настроено для автоматического развертывания с GitHub! 🚀**