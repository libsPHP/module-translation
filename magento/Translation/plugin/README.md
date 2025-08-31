# NativeMind Translation Module for Magento 2

A comprehensive translation module for Magento 2 that automatically translates product and category content using Google Translate or OpenAI APIs.

## Features

- **Automatic Product Translation**: Translates product names, descriptions, and short descriptions
- **Category Translation**: Translates category names and descriptions
- **Multiple Translation Services**: Support for Google Translate and OpenAI GPT models
- **Store-specific Translation**: Translations are created per store view
- **Bulk Translation Commands**: Console commands for bulk translation operations
- **Auto-translation on Save**: Automatically translate content when products/categories are saved
- **Translation Status Tracking**: Monitor translation status and last translation dates
- **Admin Configuration**: Full admin panel configuration for all settings

## Installation

### 1. Copy Module Files

Copy the module to your Magento installation:

```bash
cp -r nativelang-magento/magento/Translation/plugin /path/to/magento/app/code/NativeMind/Translation
```

### 2. Enable Module

Run the following commands from your Magento root directory:

```bash
php bin/magento module:enable NativeMind_Translation
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

### 3. Configure Module

1. Login to Magento Admin
2. Go to **Stores** > **Configuration** > **NativeLang** > **NativeLang Translation Settings**
3. Configure your settings:
   - Enable translation
   - Choose translation service (Google Translate or OpenAI)
   - Add your API keys
   - Configure which attributes to translate

## Configuration

### Google Translate Setup

1. Get API key from [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Google Translate API
3. Add the API key in admin configuration

### OpenAI Setup

1. Get API key from [OpenAI Platform](https://platform.openai.com/)
2. Choose your preferred model (GPT-3.5 Turbo, GPT-4, etc.)
3. Add the API key in admin configuration

## Usage

### Automatic Translation

When auto-translation is enabled, products and categories will be automatically translated when saved in the admin panel.

### Manual Bulk Translation

Use console commands for bulk translation:

#### Translate Products

```bash
# Translate all products for all stores
php bin/magento nativemind:translate:products

# Translate specific products
php bin/magento nativemind:translate:products --product-ids="1,2,3"

# Translate for specific stores
php bin/magento nativemind:translate:products --store-ids="2,3,4"

# Force re-translation
php bin/magento nativemind:translate:products --force

# Limit number of products
php bin/magento nativemind:translate:products --limit=50
```

#### Translate Categories

```bash
# Translate all categories for all stores
php bin/magento nativemind:translate:categories

# Translate specific categories
php bin/magento nativemind:translate:categories --category-ids="5,6,7"

# Translate for specific stores
php bin/magento nativemind:translate:categories --store-ids="2,3,4"

# Force re-translation
php bin/magento nativemind:translate:categories --force
```

## How It Works

### Translation Priority Logic

The module follows this priority order when displaying content:

1. **Custom Store Value**: If a custom value exists with `use_default_value` = false, it takes priority
2. **Translated Value**: If a translation exists, it's displayed
3. **Original Value**: Falls back to the original website value

### Translation Attributes

The module creates these product attributes:

- `name_translated`: Translated product name
- `description_translated`: Translated product description  
- `short_description_translated`: Translated short description
- `translation_status`: Translation status (pending, translated, manual, error)
- `last_translation_date`: Date of last translation

### Plugin System

The module uses Magento's plugin system to intercept product getter methods:

- `afterGetName()`: Returns translated name if available
- `afterGetDescription()`: Returns translated description if available
- `afterGetShortDescription()`: Returns translated short description if available

## Technical Details

### File Structure

```
NativeMind/Translation/
├── Console/Command/          # Console commands
├── Helper/                   # Helper classes
├── Model/Config/Source/      # Configuration options
├── Observer/                 # Event observers
├── Plugin/                   # Magento plugins
├── Setup/                    # Installation scripts
└── etc/                      # Module configuration
```

### Dependencies

- PHP >= 7.4
- Magento >= 2.4.0
- curl extension (for API calls)
- json extension

### API Rate Limits

The module includes built-in delays between API calls to respect rate limits:

- 0.1 second delay between translations
- Error handling for API failures
- Automatic retry logic (coming in future versions)

## Troubleshooting

### Common Issues

1. **Module not appearing in admin**: Check if module is enabled and caches are cleared
2. **Translations not working**: Verify API keys are correct and have sufficient credits
3. **Console commands failing**: Ensure proper file permissions and Magento CLI access

### Logs

Check these log files for debugging:

- `var/log/system.log`: General Magento logs
- `var/log/debug.log`: Detailed debugging information

### Debug Mode

Enable developer mode for detailed error messages:

```bash
php bin/magento deploy:mode:set developer
```

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions:

- Email: contact@nativemind.net
- Website: https://nativemind.net

## Changelog

### v1.0.2
- Added translation status tracking
- Added last translation date attribute
- Improved error handling

### v1.0.1
- Added OpenAI integration
- Added bulk translation commands
- Added auto-translation observers

### v1.0.0
- Initial release
- Google Translate integration
- Product translation plugins
- Admin configuration
