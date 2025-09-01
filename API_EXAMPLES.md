# 🔌 API Examples - NativeLang Magento

Примеры использования REST API для автоматизации переводов.

## 📋 Содержание

1. [Аутентификация](#аутентификация)
2. [Базовые операции](#базовые-операции)
3. [Перевод товаров](#перевод-товаров)
4. [Массовые операции](#массовые-операции)
5. [Мониторинг](#мониторинг)
6. [Примеры интеграции](#примеры-интеграции)

## 🔐 Аутентификация

### Получение токена администратора

```bash
# Получите токен для API
curl -X POST "https://your-domain.com/rest/V1/integration/admin/token" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123"
  }'
```

**Ответ:**
```json
"abcdef123456789token"
```

### Использование токена

```bash
# Используйте токен в заголовке Authorization
curl -X GET "https://your-domain.com/rest/V1/nativelang/stats" \
  -H "Authorization: Bearer abcdef123456789token"
```

## 🔄 Базовые операции

### 1. Перевод текста

```bash
curl -X POST "https://your-domain.com/rest/V1/nativelang/translate" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "text": "Hello World",
    "targetLanguage": "ru_RU",
    "sourceLanguage": "en_US",
    "storeId": 2
  }'
```

**Ответ:**
```json
{
  "translation_id": "trans_abc123",
  "original_text": "Hello World",
  "translated_text": "Привет, мир",
  "source_language": "en_US",
  "target_language": "ru_RU",
  "confidence": 0.95,
  "status": "completed",
  "created_at": "2023-12-01 10:30:00"
}
```

### 2. Массовый перевод текстов

```bash
curl -X POST "https://your-domain.com/rest/V1/nativelang/translate-batch" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "texts": [
      "Product Name",
      "Product Description",
      "Short Description"
    ],
    "targetLanguage": "fr_FR",
    "storeId": 3
  }'
```

**Ответ:**
```json
[
  {
    "translation_id": "trans_001",
    "original_text": "Product Name",
    "translated_text": "Nom du produit",
    "status": "completed"
  },
  {
    "translation_id": "trans_002", 
    "original_text": "Product Description",
    "translated_text": "Description du produit",
    "status": "completed"
  }
]
```

## 🛍️ Перевод товаров

### 1. Перевод одного товара

```bash
curl -X POST "https://your-domain.com/rest/V1/nativelang/product/123/translate" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "storeId": 2,
    "force": false
  }'
```

### 2. Принудительный перевод

```bash
curl -X POST "https://your-domain.com/rest/V1/nativelang/product/123/translate" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "storeId": 2,
    "force": true
  }'
```

### 3. Перевод нескольких товаров

```bash
curl -X POST "https://your-domain.com/rest/V1/nativelang/products/translate" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "productIds": [123, 124, 125],
    "storeId": 2,
    "force": false
  }'
```

## 📊 Массовые операции

### 1. Получение статистики

```bash
# Общая статистика
curl -X GET "https://your-domain.com/rest/V1/nativelang/stats" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Статистика для конкретного магазина
curl -X GET "https://your-domain.com/rest/V1/nativelang/stats/2" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Ответ:**
```json
{
  "store_id": 2,
  "total_products": 1000,
  "translated_products": 750,
  "pending_products": 200,
  "error_products": 50,
  "total_categories": 50,
  "translated_categories": 45,
  "last_translation_date": "2023-12-01 15:30:00",
  "api_usage": {
    "daily_calls": 150,
    "monthly_limit": 10000
  }
}
```

### 2. Проверка статуса перевода

```bash
curl -X GET "https://your-domain.com/rest/V1/nativelang/status/trans_abc123" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Ответ:**
```json
{
  "translation_id": "trans_abc123",
  "status": "processing",
  "progress": 75,
  "total_items": 100,
  "processed_items": 75,
  "error_count": 5,
  "started_at": "2023-12-01 10:00:00",
  "completed_at": null
}
```

## 📈 Мониторинг

### 1. PHP скрипт для мониторинга

```php
<?php
/**
 * Translation Monitor Script
 */
class TranslationMonitor 
{
    private $baseUrl;
    private $token;

    public function __construct($baseUrl, $adminUsername, $adminPassword) 
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $this->getAdminToken($adminUsername, $adminPassword);
    }

    private function getAdminToken($username, $password) 
    {
        $url = $this->baseUrl . '/rest/V1/integration/admin/token';
        $data = json_encode(['username' => $username, 'password' => $password]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function getTranslationStats($storeId = null) 
    {
        $url = $this->baseUrl . '/rest/V1/nativelang/stats';
        if ($storeId) {
            $url .= '/' . $storeId;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function translateProduct($productId, $storeId, $force = false) 
    {
        $url = $this->baseUrl . '/rest/V1/nativelang/product/' . $productId . '/translate';
        $data = json_encode(['storeId' => $storeId, 'force' => $force]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function bulkTranslateProducts($productIds, $storeId, $force = false) 
    {
        $url = $this->baseUrl . '/rest/V1/nativelang/products/translate';
        $data = json_encode([
            'productIds' => $productIds,
            'storeId' => $storeId, 
            'force' => $force
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}

// Пример использования
$monitor = new TranslationMonitor(
    'https://your-domain.com',
    'admin',
    'admin123'
);

// Получить статистику
$stats = $monitor->getTranslationStats();
echo "Total products: " . $stats['total_products'] . "\n";
echo "Translated: " . $stats['translated_products'] . "\n";

// Перевести товар
$result = $monitor->translateProduct(123, 2);
echo "Translation status: " . $result['status'] . "\n";
```

### 2. JavaScript интеграция

```javascript
/**
 * NativeLang Translation API Client
 */
class NativeLangAPI {
    constructor(baseUrl, token) {
        this.baseUrl = baseUrl.replace(/\/$/, '');
        this.token = token;
        this.headers = {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        };
    }

    async translateText(text, targetLanguage, sourceLanguage = null, storeId = null) {
        const response = await fetch(`${this.baseUrl}/rest/V1/nativelang/translate`, {
            method: 'POST',
            headers: this.headers,
            body: JSON.stringify({
                text,
                targetLanguage,
                sourceLanguage,
                storeId
            })
        });
        return await response.json();
    }

    async translateProduct(productId, storeId, force = false) {
        const response = await fetch(`${this.baseUrl}/rest/V1/nativelang/product/${productId}/translate`, {
            method: 'POST',
            headers: this.headers,
            body: JSON.stringify({ storeId, force })
        });
        return await response.json();
    }

    async getTranslationStats(storeId = null) {
        const url = storeId 
            ? `${this.baseUrl}/rest/V1/nativelang/stats/${storeId}`
            : `${this.baseUrl}/rest/V1/nativelang/stats`;
        
        const response = await fetch(url, {
            headers: this.headers
        });
        return await response.json();
    }

    async checkTranslationStatus(translationId) {
        const response = await fetch(`${this.baseUrl}/rest/V1/nativelang/status/${translationId}`, {
            headers: this.headers
        });
        return await response.json();
    }
}

// Пример использования
const api = new NativeLangAPI('https://your-domain.com', 'your-token');

// Перевод текста
api.translateText('Hello World', 'ru_RU')
    .then(result => console.log('Translated:', result.translated_text));

// Перевод товара
api.translateProduct(123, 2)
    .then(result => console.log('Product translation:', result.status));

// Получение статистики
api.getTranslationStats()
    .then(stats => console.log('Stats:', stats));
```

### 3. Python интеграция

```python
import requests
import json

class NativeLangAPI:
    def __init__(self, base_url, username, password):
        self.base_url = base_url.rstrip('/')
        self.token = self._get_admin_token(username, password)
        self.headers = {
            'Authorization': f'Bearer {self.token}',
            'Content-Type': 'application/json'
        }

    def _get_admin_token(self, username, password):
        url = f'{self.base_url}/rest/V1/integration/admin/token'
        data = {'username': username, 'password': password}
        response = requests.post(url, json=data)
        return response.json()

    def translate_text(self, text, target_language, source_language=None, store_id=None):
        url = f'{self.base_url}/rest/V1/nativelang/translate'
        data = {
            'text': text,
            'targetLanguage': target_language,
            'sourceLanguage': source_language,
            'storeId': store_id
        }
        response = requests.post(url, json=data, headers=self.headers)
        return response.json()

    def translate_product(self, product_id, store_id, force=False):
        url = f'{self.base_url}/rest/V1/nativelang/product/{product_id}/translate'
        data = {'storeId': store_id, 'force': force}
        response = requests.post(url, json=data, headers=self.headers)
        return response.json()

    def get_translation_stats(self, store_id=None):
        if store_id:
            url = f'{self.base_url}/rest/V1/nativelang/stats/{store_id}'
        else:
            url = f'{self.base_url}/rest/V1/nativelang/stats'
        
        response = requests.get(url, headers=self.headers)
        return response.json()

    def bulk_translate_products(self, product_ids, store_id, force=False):
        url = f'{self.base_url}/rest/V1/nativelang/products/translate'
        data = {
            'productIds': product_ids,
            'storeId': store_id,
            'force': force
        }
        response = requests.post(url, json=data, headers=self.headers)
        return response.json()

# Пример использования
api = NativeLangAPI('https://your-domain.com', 'admin', 'admin123')

# Перевод текста
result = api.translate_text('Hello World', 'ru_RU')
print(f"Translated: {result['translated_text']}")

# Массовый перевод товаров
products = [123, 124, 125]
results = api.bulk_translate_products(products, 2)
for result in results:
    print(f"Product {result.get('product_id', 'N/A')}: {result['status']}")

# Получение статистики
stats = api.get_translation_stats()
print(f"Total products: {stats['total_products']}")
print(f"Translated: {stats['translated_products']}")
```

## 🔄 Автоматизация workflows

### 1. Bash скрипт для ежедневного перевода

```bash
#!/bin/bash
# daily_translation.sh

BASE_URL="https://your-domain.com"
USERNAME="admin"
PASSWORD="admin123"

# Получение токена
TOKEN=$(curl -s -X POST "$BASE_URL/rest/V1/integration/admin/token" \
  -H "Content-Type: application/json" \
  -d "{\"username\":\"$USERNAME\",\"password\":\"$PASSWORD\"}" | tr -d '"')

# Функция для перевода товаров магазина
translate_store_products() {
    local store_id=$1
    local store_name=$2
    
    echo "Processing store: $store_name (ID: $store_id)"
    
    # Получение статистики
    stats=$(curl -s -X GET "$BASE_URL/rest/V1/nativelang/stats/$store_id" \
      -H "Authorization: Bearer $TOKEN")
    
    pending=$(echo $stats | jq '.pending_products')
    
    if [ "$pending" -gt 0 ]; then
        echo "Found $pending pending products for translation"
        
        # Запуск массового перевода (только новые товары)
        result=$(curl -s -X POST "$BASE_URL/rest/V1/nativelang/products/translate" \
          -H "Authorization: Bearer $TOKEN" \
          -H "Content-Type: application/json" \
          -d "{\"productIds\":[], \"storeId\":$store_id, \"force\":false}")
        
        echo "Translation job started for store $store_name"
    else
        echo "No pending translations for store $store_name"
    fi
}

# Перевод для всех магазинов
translate_store_products 2 "French Store"
translate_store_products 3 "German Store"
translate_store_products 4 "Spanish Store"

echo "Daily translation job completed"
```

### 2. Cron задачи

```bash
# Добавьте в crontab
crontab -e

# Ежедневный перевод в 2:00
0 2 * * * /path/to/daily_translation.sh >> /var/log/magento_translation.log 2>&1

# Еженедельная проверка статистики
0 9 * * 1 /path/to/weekly_report.sh | mail -s "Weekly Translation Report" admin@yoursite.com

# Ежечасная проверка ошибок
0 * * * * grep "ERROR" /path/to/magento/var/log/nativelang.log | tail -10 | mail -s "Translation Errors" admin@yoursite.com
```

## 🛡️ Безопасность и лимиты

### Rate Limiting

```bash
# Пример с задержками для избежания rate limits
translate_with_delay() {
    local product_id=$1
    local store_id=$2
    
    curl -X POST "$BASE_URL/rest/V1/nativelang/product/$product_id/translate" \
      -H "Authorization: Bearer $TOKEN" \
      -H "Content-Type: application/json" \
      -d "{\"storeId\":$store_id, \"force\":false}"
    
    # Задержка 100ms между запросами
    sleep 0.1
}
```

### Error Handling

```python
import time
import logging

def translate_with_retry(api, product_id, store_id, max_retries=3):
    for attempt in range(max_retries):
        try:
            result = api.translate_product(product_id, store_id)
            if result.get('status') == 'completed':
                return result
            elif result.get('status') == 'error':
                logging.error(f"Translation failed for product {product_id}: {result.get('error_message')}")
                return None
        except Exception as e:
            logging.warning(f"Attempt {attempt + 1} failed for product {product_id}: {str(e)}")
            if attempt < max_retries - 1:
                time.sleep(2 ** attempt)  # Exponential backoff
    
    logging.error(f"All retries failed for product {product_id}")
    return None
```

---

Эти примеры покрывают основные сценарии использования API для автоматизации переводов в Magento. Адаптируйте их под ваши конкретные потребности.
