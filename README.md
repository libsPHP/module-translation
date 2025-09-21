# NativeMind Translation Module for Magento 2

[![Latest Stable Version](https://poser.pugx.org/nativemind/module-translation/v/stable)](https://packagist.org/packages/nativemind/module-translation)
[![License](https://poser.pugx.org/nativemind/module-translation/license)](https://packagist.org/packages/nativemind/module-translation)
[![Total Downloads](https://poser.pugx.org/nativemind/module-translation/downloads)](https://packagist.org/packages/nativemind/module-translation)

Advanced translation module for Magento 2 with Google Translate and OpenAI GPT integration. Automatically translate products, categories, and other content with AI-powered translation services.

## Features

🔄 **Automatic Translation**
- Products (names, descriptions, short descriptions)
- Categories (names and descriptions) 
- Meta data (SEO titles and descriptions)
- Custom attributes

🌍 **Multi-language Support**
- 100+ languages supported
- Auto-detect source language
- Store-specific language configuration
- Fallback to original content

🤖 **AI Translation Services**
- **Google Translate API**: Fast and accurate translation
- **OpenAI GPT**: Contextual translation with semantic understanding
- **Custom prompts**: Control translation quality
- **Batch processing**: Bulk content translation

⚡ **Performance**
- Translation caching
- Asynchronous processing
- API rate limiting
- High-load optimization

🎛️ **Management**
- Complete admin panel
- Console commands for DevOps
- Statistics and monitoring
- Operation logging

## Installation

### Via Composer (Recommended)

```bash
composer require nativemind/module-translation
php bin/magento module:enable NativeMind_Translation
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

### Manual Installation

```bash
# 1. Download and extract module
mkdir -p app/code/NativeMind/Translation
# Extract module files to app/code/NativeMind/Translation

# 2. Enable module
php bin/magento module:enable NativeMind_Translation
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

## Configuration

1. Navigate to **Stores** → **Configuration** → **NativeLang** → **Translation Settings**
2. Choose translation service (Google Translate or OpenAI)
3. Add your API keys
4. Configure attributes for translation
5. Enable auto-translation

### Google Translate Setup

```bash
# Get your Google Cloud API key from:
# https://console.cloud.google.com/apis/credentials
```

### OpenAI Setup

```bash
# Get your OpenAI API key from:
# https://platform.openai.com/api-keys
```

## Usage

### Console Commands

```bash
# Translate all products
php bin/magento nativemind:translate:products

# Translate specific products
php bin/magento nativemind:translate:products --product-ids="1,2,3"

# Translate for specific stores
php bin/magento nativemind:translate:products --store-ids="2,3,4"

# Force translation (overwrite existing)
php bin/magento nativemind:translate:products --force

# Translate categories
php bin/magento nativemind:translate:categories
```

### PHP API

```php
use NativeMind\Translation\Helper\Data as TranslationHelper;

// Initialize
$translationHelper = $this->translationHelper;

// Simple translation
$translated = $translationHelper->translateText(
    'Hello World', 
    'ru_RU', 
    $storeId
);

// Translate array
$data = ['title' => 'Product Title', 'description' => 'Product Description'];
$translated = $translationHelper->translateArray($data, 'ru_RU');
```

### REST API Endpoints

```
GET    /rest/V1/nativelang/config          # Get configuration
POST   /rest/V1/nativelang/translate       # Translate text
GET    /rest/V1/nativelang/status/:id      # Translation status
POST   /rest/V1/nativelang/products/batch  # Bulk translate products
```

## Requirements

- PHP >= 7.4
- Magento >= 2.4.0
- ext-curl (for API calls)
- ext-json (for JSON handling)

## Compatibility

| Magento Version | Module Version |
|----------------|----------------|
| 2.4.x          | 1.0.x          |
| 2.3.x          | 1.0.x          |

## Support

- 📧 **Email**: contact@nativemind.net
- 🌐 **Website**: https://nativemind.net
- 📱 **Telegram**: @nativemind
- 🐛 **Issues**: [GitHub Issues](https://github.com/nativemind/module-translation/issues)

## Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Changelog

### v1.0.0
- Initial release
- Google Translate integration
- OpenAI GPT integration
- Product and category translation
- Admin panel interface
- Console commands
- REST API endpoints

---

⭐ **If this project helps you, please give it a star!** ⭐