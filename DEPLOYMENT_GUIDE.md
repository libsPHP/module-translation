# 🚀 Руководство по развертыванию NativeLang Magento

## 📋 Содержание

1. [Предварительные требования](#предварительные-требования)
2. [Автоматическая установка](#автоматическая-установка)
3. [Ручная установка](#ручная-установка)
4. [Конфигурация](#конфигурация)
5. [Тестирование](#тестирование)
6. [Устранение неполадок](#устранение-неполадок)
7. [Производственное развертывание](#производственное-развертывание)
8. [Мониторинг](#мониторинг)

## ⚙️ Предварительные требования

### Системные требования

- **PHP**: >= 7.4 (рекомендуется 8.1+)
- **Magento**: >= 2.4.0
- **MySQL**: >= 5.7 или MariaDB >= 10.2
- **Composer**: >= 2.0
- **Расширения PHP**: curl, json, mbstring, intl

### API ключи

Получите API ключи для выбранного сервиса перевода:

#### Google Translate API
1. Перейдите в [Google Cloud Console](https://console.cloud.google.com/)
2. Создайте новый проект или выберите существующий
3. Включите Google Translate API
4. Создайте учетные данные (API Key)
5. Ограничьте ключ для безопасности

#### OpenAI API
1. Зарегистрируйтесь на [OpenAI Platform](https://platform.openai.com/)
2. Перейдите в раздел API Keys
3. Создайте новый секретный ключ
4. Настройте лимиты использования

### Проверка совместимости

```bash
# Проверка версии PHP
php -v

# Проверка расширений PHP
php -m | grep -E "(curl|json|mbstring|intl)"

# Проверка Composer
composer --version

# Проверка Magento
php bin/magento --version
```

## 🔧 Автоматическая установка

### Шаг 1: Подготовка

```bash
# Перейдите в директорию проекта
cd /path/to/your/magento

# Создайте резервную копию (рекомендуется)
tar -czf magento_backup_$(date +%Y%m%d_%H%M%S).tar.gz .

# Убедитесь, что у вас есть права на запись
sudo chown -R www-data:www-data var/ pub/ generated/
chmod -R 755 var/ pub/ generated/
```

### Шаг 2: Запуск установки

```bash
# Скачайте и распакуйте модуль
cd /tmp
git clone https://github.com/nativemind/nativelang-magento.git
cd nativelang-magento/magento/Translation/plugin

# Запустите автоматическую установку
chmod +x install.sh
./install.sh /path/to/your/magento
```

### Шаг 3: Проверка установки

```bash
# Проверьте статус модуля
cd /path/to/your/magento
php bin/magento module:status NativeMind_Translation

# Проверьте консольные команды
php bin/magento list | grep nativemind
```

## 🛠️ Ручная установка

### Шаг 1: Копирование файлов

```bash
# Создайте директорию модуля
mkdir -p /path/to/magento/app/code/NativeMind/Translation

# Скопируйте файлы модуля
cp -r nativelang-magento/magento/Translation/plugin/* /path/to/magento/app/code/NativeMind/Translation/
```

### Шаг 2: Включение модуля

```bash
cd /path/to/magento

# Включите модуль
php bin/magento module:enable NativeMind_Translation

# Обновите схему базы данных
php bin/magento setup:upgrade

# Компилируйте DI
php bin/magento setup:di:compile

# Очистите кеш
php bin/magento cache:flush
```

### Шаг 3: Установка в режиме Production

```bash
# Если Magento в режиме production
php bin/magento deploy:mode:show

# Деплой статического контента
php bin/magento setup:static-content:deploy

# Перекомпиляция (если нужно)
php bin/magento setup:di:compile
```

## ⚙️ Конфигурация

### Шаг 1: Базовая настройка

1. Войдите в админ-панель Magento
2. Перейдите: **Stores** → **Configuration** → **NativeLang** → **Translation Settings**

### Шаг 2: Настройка Google Translate

```
General Settings:
✓ Enable Translation: Yes
✓ Translation Service: Google Translate
✓ Auto Translate on Save: Yes (по желанию)

Google Translate Settings:
✓ API Key: [ваш-google-api-ключ]
```

### Шаг 3: Настройка OpenAI

```
General Settings:
✓ Enable Translation: Yes
✓ Translation Service: OpenAI GPT
✓ Auto Translate on Save: Yes (по желанию)

OpenAI Settings:
✓ API Key: [ваш-openai-api-ключ]
✓ Model: gpt-3.5-turbo (или gpt-4)
```

### Шаг 4: Настройка атрибутов

```
Translation Attributes:
✓ Translate Product Names: Yes
✓ Translate Product Descriptions: Yes
✓ Translate Short Descriptions: Yes
```

### Шаг 5: Сохранение и проверка

1. Нажмите **Save Config**
2. Очистите кеш: `php bin/magento cache:flush`
3. Проверьте настройки в **NativeLang** → **Translation Dashboard**

## 🧪 Тестирование

### Тест 1: Консольные команды

```bash
# Тест перевода одного товара
php bin/magento nativemind:translate:products --product-ids="1" --store-ids="2" --limit=1

# Тест перевода категорий
php bin/magento nativemind:translate:categories --category-ids="3" --store-ids="2"
```

### Тест 2: API тестирование

```bash
# Тест REST API (замените на ваш домен)
curl -X POST "https://your-domain.com/rest/V1/nativelang/translate" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "text": "Hello World",
    "targetLanguage": "ru_RU"
  }'
```

### Тест 3: Фронтенд тестирование

1. Создайте тестовый товар
2. Переведите его через админ-панель
3. Проверьте отображение на фронтенде
4. Переключите языки магазина

### Тест 4: Автоматический перевод

1. Включите "Auto Translate on Save"
2. Создайте новый товар
3. Сохраните товар
4. Проверьте, что переводы созданы автоматически

## 🚨 Устранение неполадок

### Проблема: Модуль не появляется в админке

**Решение:**
```bash
php bin/magento module:enable NativeMind_Translation
php bin/magento setup:upgrade
php bin/magento cache:flush
```

### Проблема: Ошибки API ключей

**Проверка Google API:**
```bash
curl "https://translation.googleapis.com/language/translate/v2/languages?key=YOUR_API_KEY"
```

**Проверка OpenAI API:**
```bash
curl https://api.openai.com/v1/models \
  -H "Authorization: Bearer YOUR_OPENAI_KEY"
```

### Проблема: Переводы не отображаются

**Отладка:**
1. Проверьте логи: `tail -f var/log/system.log`
2. Включите режим разработчика: `php bin/magento deploy:mode:set developer`
3. Проверьте атрибуты товара в админке

### Проблема: Медленная работа

**Оптимизация:**
```bash
# Включите все кеши
php bin/magento cache:enable

# Оптимизируйте базу данных
php bin/magento indexer:reindex

# Компилируйте код
php bin/magento setup:di:compile
```

### Проблема: Ошибки прав доступа

```bash
# Установите правильные права
sudo chown -R www-data:www-data /path/to/magento/
find /path/to/magento/ -type f -exec chmod 644 {} \;
find /path/to/magento/ -type d -exec chmod 755 {} \;
chmod +x /path/to/magento/bin/magento
```

## 🏭 Производственное развертывание

### Настройка Production сервера

```bash
# Переключите Magento в режим production
php bin/magento deploy:mode:set production

# Деплой статических файлов
php bin/magento setup:static-content:deploy

# Настройте cron jobs
crontab -e
# Добавьте:
# * * * * * /usr/bin/php /path/to/magento/bin/magento cron:run 2>&1 | grep -v "Ran jobs by schedule" >> /path/to/magento/var/log/magento.cron.log
```

### Настройка веб-сервера

**Apache (.htaccess)**:
```apache
# Дополнительные настройки для API
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^rest/(.*)$ pub/index.php/rest/$1 [L]
</IfModule>
```

**Nginx**:
```nginx
# Дополнительная конфигурация для API
location /rest/ {
    try_files $uri $uri/ /index.php?$args;
}
```

### Настройка SSL

```bash
# Убедитесь, что SSL включен для API запросов
# Обновите конфигурацию Magento
php bin/magento setup:store-config:set --use-secure=1
php bin/magento setup:store-config:set --use-secure-admin=1
```

### Настройка производительности

**php.ini оптимизации**:
```ini
memory_limit = 2G
max_execution_time = 1800
max_input_vars = 10000
realpath_cache_size = 10M
realpath_cache_ttl = 7200
opcache.memory_consumption = 512
opcache.max_accelerated_files = 60000
opcache.consistency_checks = 0
opcache.validate_timestamps = 0
```

## 📊 Мониторинг

### Настройка логирования

**config/app.php**:
```php
'handlers' => [
    'nativelang' => [
        'type' => 'stream',
        'path' => BP . '/var/log/nativelang.log',
        'level' => 'info'
    ]
]
```

### Мониторинг API использования

```bash
# Создайте скрипт мониторинга
cat > /path/to/magento/monitor_translation.sh << 'EOF'
#!/bin/bash
LOG_FILE="/path/to/magento/var/log/nativelang.log"
YESTERDAY=$(date -d "yesterday" +%Y-%m-%d)

echo "=== Translation API Usage Report for $YESTERDAY ==="
echo "Google Translate calls:"
grep "$YESTERDAY" $LOG_FILE | grep "Google" | wc -l

echo "OpenAI calls:"
grep "$YESTERDAY" $LOG_FILE | grep "OpenAI" | wc -l

echo "Translation errors:"
grep "$YESTERDAY" $LOG_FILE | grep "ERROR" | wc -l
EOF

chmod +x /path/to/magento/monitor_translation.sh

# Добавьте в cron для ежедневных отчетов
# 0 9 * * * /path/to/magento/monitor_translation.sh | mail -s "Translation Report" admin@yoursite.com
```

### Настройка алертов

```bash
# Создайте скрипт проверки здоровья
cat > /path/to/magento/health_check.sh << 'EOF'
#!/bin/bash
MAGENTO_ROOT="/path/to/magento"
cd $MAGENTO_ROOT

# Проверка статуса модуля
MODULE_STATUS=$(php bin/magento module:status NativeMind_Translation | grep -c "enabled")
if [ $MODULE_STATUS -eq 0 ]; then
    echo "ALERT: NativeMind_Translation module is disabled"
    exit 1
fi

# Проверка API ключей
CONFIG_CHECK=$(php bin/magento config:show nativelang/general/enabled)
if [ "$CONFIG_CHECK" != "1" ]; then
    echo "ALERT: Translation is disabled in configuration"
    exit 1
fi

echo "OK: Translation module is healthy"
EOF

chmod +x /path/to/magento/health_check.sh
```

## 📈 Масштабирование

### Для высоких нагрузок

1. **Используйте очереди**:
   ```bash
   # Настройте RabbitMQ или Redis для очередей
   php bin/magento setup:config:set --amqp-host="localhost" --amqp-port="5672"
   ```

2. **Кеширование переводов**:
   - Включите Redis для кеширования
   - Настройте Varnish для статического контента

3. **Балансировка нагрузки**:
   - Настройте несколько серверов приложений
   - Используйте отдельные сервера для API

### Backup стратегия

```bash
# Ежедневный backup конфигурации
cat > /path/to/magento/backup_translation_config.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/backups/magento/translation"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup конфигурации
php bin/magento config:show | grep nativelang > $BACKUP_DIR/config_$DATE.txt

# Backup переводов из базы данных
mysqldump -u USER -p DATABASE catalog_product_entity_text | gzip > $BACKUP_DIR/translations_$DATE.sql.gz
EOF
```

---

## 📞 Поддержка

Если у вас возникли проблемы:

1. Проверьте [FAQ](https://nativemind.net/faq)
2. Изучите логи: `var/log/system.log`, `var/log/nativelang.log`
3. Обратитесь в поддержку: support@nativemind.net
4. Создайте issue на GitHub: [github.com/nativemind/nativelang-magento](https://github.com/nativemind/nativelang-magento)

**Telegram поддержка**: @nativemind_support  
**Discord**: NativeMind Community
