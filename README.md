# 🌐 NativeLang Magento

Комплексное решение для автоматического перевода контента в Magento 2 с поддержкой Google Translate и OpenAI GPT.

## 📋 Содержание

- [Обзор](#обзор)
- [Возможности](#возможности)
- [Структура проекта](#структура-проекта)
- [Быстрая установка](#быстрая-установка)
- [Компоненты](#компоненты)
- [Конфигурация](#конфигурация)
- [Использование](#использование)
- [API документация](#api-документация)
- [Разработка](#разработка)
- [Поддержка](#поддержка)

## 🎯 Обзор

NativeLang Magento - это профессиональное решение для автоматизации переводов в интернет-магазинах на платформе Magento 2. Модуль интегрируется с ведущими сервисами перевода и обеспечивает бесшовную локализацию контента для международных магазинов.

## ✨ Возможности

### 🔄 Автоматический перевод
- **Товары**: названия, описания, краткие описания
- **Категории**: названия и описания категорий
- **Мета-данные**: SEO заголовки и описания
- **Атрибуты**: переводы пользовательских атрибутов

### 🌍 Мультиязычность
- Поддержка 100+ языков
- Автоопределение исходного языка
- Настройка языков по магазинам
- Fallback на оригинальный контент

### 🤖 AI сервисы
- **Google Translate API**: быстрый и точный перевод
- **OpenAI GPT**: контекстуальный перевод с пониманием смысла
- **Настраиваемые промпты**: контроль качества перевода
- **Batch обработка**: массовый перевод контента

### ⚡ Производительность
- Кэширование переводов
- Асинхронная обработка
- Rate limiting для API
- Оптимизация под высокие нагрузки

### 🎛️ Управление
- Полная админ-панель
- Console команды для DevOps
- Статистика и мониторинг
- Логирование операций

## 📁 Структура проекта

```
nativelang-magento/
├── 📦 magento/                    # Основной Magento модуль
│   ├── 🔌 Translation/plugin/     # Модуль перевода
│   │   ├── Console/Command/       # CLI команды
│   │   ├── Helper/                # Вспомогательные классы
│   │   ├── Model/                 # Модели данных
│   │   ├── Observer/              # События и наблюдатели
│   │   ├── Plugin/                # Плагины Magento
│   │   ├── Setup/                 # Скрипты установки
│   │   └── etc/                   # Конфигурация модуля
│   └── 🌐 Domains/                # Управление доменами
├── 🔧 functions/                  # PHP функции перевода
├── 🎨 design/                     # Ресурсы дизайна
├── 🌍 wordpress/                  # WordPress интеграция
└── 📊 res/                        # Ресурсы и примеры
```

## 🚀 Быстрая установка

### Автоматическая установка

```bash
cd nativelang-magento/magento/Translation/plugin
chmod +x install.sh
./install.sh /path/to/your/magento
```

### Ручная установка

```bash
# 1. Копируем модуль
cp -r magento/Translation/plugin /path/to/magento/app/code/NativeMind/Translation

# 2. Включаем модуль
cd /path/to/magento
php bin/magento module:enable NativeMind_Translation
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

## 🧩 Компоненты

### 1. 🔌 Magento Plugin
Основной модуль для Magento 2 с полной интеграцией в админ-панель.

**Расположение**: `magento/Translation/plugin/`

**Возможности**:
- Автоматический перевод при сохранении
- Плагины для перехвата getter методов
- Консольные команды для массового перевода
- Полная админ-конфигурация

### 2. 🔧 Translation Functions
Библиотека PHP функций для работы с переводами.

**Расположение**: `functions/`

**Файлы**:
- `translateText.php` - основные функции перевода
- `translateTextGoogle.php` - Google Translate API
- `translateTextGPT.php` - OpenAI GPT API
- `products.php` - функции для перевода товаров
- `categories.php` - функции для перевода категорий

### 3. 🌍 WordPress Integration
Интеграция с WordPress для единой системы переводов.

**Расположение**: `wordpress/`

**Возможности**:
- Синхронизация переводов
- Общие API ключи
- Единая админ-панель

### 4. 🌐 Domain Management
Управление доменами и многосайтовостью.

**Расположение**: `magento/Domains/`

**Возможности**:
- Автоматическое определение языка по домену
- Настройка переводов по доменам
- Редиректы и SEO оптимизация

## ⚙️ Конфигурация

### 1. Google Translate

```php
// Получение API ключа
$apiKey = "YOUR_GOOGLE_API_KEY";

// Базовая настройка
$config = [
    'service' => 'google',
    'api_key' => $apiKey,
    'auto_detect' => true,
    'cache_enabled' => true
];
```

### 2. OpenAI GPT

```php
// Настройка OpenAI
$config = [
    'service' => 'openai',
    'api_key' => 'YOUR_OPENAI_API_KEY',
    'model' => 'gpt-3.5-turbo',
    'temperature' => 0.3,
    'max_tokens' => 2048
];
```

### 3. Админ-панель

1. **Stores** → **Configuration** → **NativeLang** → **Translation Settings**
2. Выберите сервис перевода
3. Добавьте API ключи
4. Настройте атрибуты для перевода
5. Включите автоперевод

## 📖 Использование

### Console команды

```bash
# Перевод всех товаров
php bin/magento nativemind:translate:products

# Перевод конкретных товаров
php bin/magento nativemind:translate:products --product-ids="1,2,3"

# Перевод для конкретных магазинов
php bin/magento nativemind:translate:products --store-ids="2,3,4"

# Принудительный перевод
php bin/magento nativemind:translate:products --force

# Перевод категорий
php bin/magento nativemind:translate:categories
```

### PHP API

```php
use NativeMind\Translation\Helper\Data as TranslationHelper;

// Инициализация
$translationHelper = $this->translationHelper;

// Простой перевод
$translated = $translationHelper->translateText(
    'Hello World', 
    'ru_RU', 
    $storeId
);

// Перевод массива
$data = ['title' => 'Product Title', 'description' => 'Product Description'];
$translated = $translationHelper->translateArray($data, 'ru_RU');
```

### JavaScript API (для фронтенда)

```javascript
// Асинхронный перевод на фронтенде
async function translateContent(text, targetLang) {
    const response = await fetch('/nativelang/translate', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({text, targetLang})
    });
    return await response.json();
}
```

## 📚 API документация

### REST Endpoints

```
GET    /rest/V1/nativelang/config          # Получить конфигурацию
POST   /rest/V1/nativelang/translate       # Перевести текст
GET    /rest/V1/nativelang/status/:id      # Статус перевода
POST   /rest/V1/nativelang/products/batch  # Массовый перевод товаров
```

### GraphQL

```graphql
query getTranslation($text: String!, $targetLang: String!) {
    translation(text: $text, targetLang: $targetLang) {
        translatedText
        confidence
        detectedLanguage
    }
}
```

## 👨‍💻 Разработка

### Требования

- PHP >= 7.4
- Magento >= 2.4.0
- Composer
- ext-curl, ext-json

### Установка для разработки

```bash
git clone https://github.com/nativemind/nativelang-magento.git
cd nativelang-magento
composer install
```

### Тестирование

```bash
# Unit тесты
php bin/magento dev:tests:run unit NativeMind_Translation

# Integration тесты  
php bin/magento dev:tests:run integration NativeMind_Translation

# Функциональные тесты
vendor/bin/codecept run functional
```

### Debugging

```bash
# Включить режим разработчика
php bin/magento deploy:mode:set developer

# Посмотреть логи
tail -f var/log/nativelang.log
tail -f var/log/system.log
```

## 🔄 CI/CD

### GitHub Actions

```yaml
name: NativeLang CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
      - name: Install Magento
        run: composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition
      - name: Install Module
        run: ./install.sh magento/
      - name: Run Tests
        run: php bin/magento dev:tests:run unit NativeMind_Translation
```

## 📊 Мониторинг и статистика

### Metrics Dashboard

- Количество переведенных элементов
- Статистика по языкам
- Производительность API
- Ошибки и предупреждения

### Логирование

```php
// Настройка логирования
'handlers' => [
    'nativelang' => [
        'type' => 'stream',
        'path' => BP . '/var/log/nativelang.log',
        'level' => 'info'
    ]
]
```

## 🛠️ Расширения

### Custom Translation Providers

```php
namespace Custom\Translation\Model;

use NativeMind\Translation\Api\TranslationProviderInterface;

class CustomProvider implements TranslationProviderInterface
{
    public function translate($text, $targetLang, $sourceLang = null)
    {
        // Ваша логика перевода
        return $translatedText;
    }
}
```

### Event Observers

```php
namespace Custom\Translation\Observer;

class CustomTranslationObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        // Кастомная логика после перевода
    }
}
```

## 🔧 Troubleshooting

### Частые проблемы

1. **Модуль не появляется в админке**
   ```bash
   php bin/magento module:enable NativeMind_Translation
   php bin/magento cache:flush
   ```

2. **Переводы не работают**
   - Проверьте API ключи
   - Убедитесь что сервис включен
   - Проверьте логи в `var/log/nativelang.log`

3. **Ошибки консольных команд**
   ```bash
   php bin/magento setup:di:compile
   chmod -R 755 var/ pub/
   ```

### Debug режим

```bash
# Включить детальное логирование
php bin/magento config:set nativelang/debug/enabled 1
php bin/magento config:set nativelang/debug/level debug
php bin/magento cache:flush
```

## 📄 Лицензия

MIT License. См. [LICENSE](LICENSE) для деталей.

## 🤝 Контрибьюторы

- **Anton Dodonov** - Главный разработчик
- **NativeMind Team** - Архитектура и дизайн

## 📞 Поддержка

- 📧 Email: contact@nativemind.net
- 🌐 Website: https://nativemind.net
- 📱 Telegram: @nativemind
- 💬 Discord: NativeMind Community

## 🗺️ Roadmap

### v1.1.0
- [ ] Перевод CMS страниц и блоков
- [ ] Улучшенный UI в админке
- [ ] Batch API для массовых операций
- [ ] Интеграция с ElasticSearch

### v1.2.0
- [ ] Machine Learning оптимизации
- [ ] Кастомные словари и глоссарии
- [ ] A/B тестирование переводов
- [ ] Интеграция с CDN

### v2.0.0
- [ ] Magento 2.5+ поддержка
- [ ] PWA совместимость
- [ ] Real-time переводы
- [ ] Advanced Analytics

---

🌟 **Поставьте звезду если проект полезен!** 🌟