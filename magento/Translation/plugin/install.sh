#!/bin/bash

# NativeMind Translation Module Installation Script
# Usage: ./install.sh [magento_root_path]

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_message() {
    echo -e "${2}${1}${NC}"
}

# Check if Magento root path is provided
if [ -z "$1" ]; then
    print_message "Error: Please provide Magento root path" $RED
    print_message "Usage: ./install.sh /path/to/magento" $YELLOW
    exit 1
fi

MAGENTO_ROOT="$1"
MODULE_PATH="$MAGENTO_ROOT/app/code/NativeMind/Translation"

print_message "🚀 Starting NativeMind Translation Module Installation..." $BLUE

# Check if Magento root exists
if [ ! -d "$MAGENTO_ROOT" ]; then
    print_message "Error: Magento root directory '$MAGENTO_ROOT' does not exist" $RED
    exit 1
fi

# Check if bin/magento exists
if [ ! -f "$MAGENTO_ROOT/bin/magento" ]; then
    print_message "Error: bin/magento not found in '$MAGENTO_ROOT'" $RED
    print_message "Please make sure you provided the correct Magento root path" $YELLOW
    exit 1
fi

# Create module directory
print_message "📁 Creating module directory..." $BLUE
mkdir -p "$MODULE_PATH"

# Copy module files
print_message "📋 Copying module files..." $BLUE
cp -r ./* "$MODULE_PATH/"

# Remove install script from destination
rm -f "$MODULE_PATH/install.sh"

print_message "✅ Module files copied successfully" $GREEN

# Change to Magento root directory
cd "$MAGENTO_ROOT"

# Enable the module
print_message "🔧 Enabling NativeMind_Translation module..." $BLUE
php bin/magento module:enable NativeMind_Translation

if [ $? -eq 0 ]; then
    print_message "✅ Module enabled successfully" $GREEN
else
    print_message "❌ Failed to enable module" $RED
    exit 1
fi

# Run setup upgrade
print_message "⬆️  Running setup:upgrade..." $BLUE
php bin/magento setup:upgrade

if [ $? -eq 0 ]; then
    print_message "✅ Setup upgrade completed successfully" $GREEN
else
    print_message "❌ Setup upgrade failed" $RED
    exit 1
fi

# Compile DI
print_message "🔨 Compiling dependency injection..." $BLUE
php bin/magento setup:di:compile

if [ $? -eq 0 ]; then
    print_message "✅ DI compilation completed successfully" $GREEN
else
    print_message "❌ DI compilation failed" $RED
    exit 1
fi

# Deploy static content (if in production mode)
MODE=$(php bin/magento deploy:mode:show)
if [[ $MODE == *"production"* ]]; then
    print_message "📦 Deploying static content..." $BLUE
    php bin/magento setup:static-content:deploy
    
    if [ $? -eq 0 ]; then
        print_message "✅ Static content deployment completed" $GREEN
    else
        print_message "❌ Static content deployment failed" $RED
        exit 1
    fi
fi

# Flush cache
print_message "🗑️  Flushing cache..." $BLUE
php bin/magento cache:flush

if [ $? -eq 0 ]; then
    print_message "✅ Cache flushed successfully" $GREEN
else
    print_message "❌ Cache flush failed" $RED
    exit 1
fi

# Check if module is installed
print_message "🔍 Verifying installation..." $BLUE
MODULE_STATUS=$(php bin/magento module:status NativeMind_Translation)

if [[ $MODULE_STATUS == *"NativeMind_Translation"* ]]; then
    print_message "✅ Module is properly installed and enabled" $GREEN
else
    print_message "❌ Module verification failed" $RED
    exit 1
fi

print_message "" $NC
print_message "🎉 Installation completed successfully!" $GREEN
print_message "" $NC
print_message "Next steps:" $BLUE
print_message "1. Login to Magento Admin" $NC
print_message "2. Go to Stores > Configuration > NativeLang > NativeLang Translation Settings" $NC
print_message "3. Configure your translation settings and API keys" $NC
print_message "4. Start translating your content!" $NC
print_message "" $NC
print_message "Console commands available:" $BLUE
print_message "• php bin/magento nativemind:translate:products" $NC
print_message "• php bin/magento nativemind:translate:categories" $NC
print_message "" $NC
print_message "For support and documentation: https://nativemind.net" $YELLOW
