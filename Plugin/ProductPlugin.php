<?php
namespace NativeMind\Translation\Plugin;

use Magento\Store\Model\StoreManagerInterface;
use NativeMind\Translation\Helper\Data as TranslationHelper;

class ProductPlugin
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * ProductPlugin constructor.
     * @param StoreManagerInterface $storeManager
     * @param TranslationHelper $translationHelper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TranslationHelper $translationHelper
    ) {
        $this->storeManager = $storeManager;
        $this->translationHelper = $translationHelper;
    }

    /**
     * Plugin for product name translation
     */
    public function afterGetName(\Magento\Catalog\Model\Product $subject, $result)
    {
        return $this->getTranslatedAttribute($subject, $result, 'name_translated', 'name');
    }

    /**
     * Plugin for product description translation
     */
    public function afterGetDescription(\Magento\Catalog\Model\Product $subject, $result)
    {
        return $this->getTranslatedAttribute($subject, $result, 'description_translated', 'description');
    }

    /**
     * Plugin for product short description translation
     */
    public function afterGetShortDescription(\Magento\Catalog\Model\Product $subject, $result)
    {
        return $this->getTranslatedAttribute($subject, $result, 'short_description_translated', 'short_description');
    }

    /**
     * Get translated attribute value based on priority logic
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @param string $originalValue
     * @param string $translatedAttributeCode
     * @param string $originalAttributeCode
     * @return string
     */
    private function getTranslatedAttribute($product, $originalValue, $translatedAttributeCode, $originalAttributeCode)
    {
        // Check if translation is enabled for current store
        if (!$this->translationHelper->isTranslationEnabled()) {
            return $originalValue;
        }

        // Get current store ID
        $storeId = $this->storeManager->getStore()->getId();
        
        // Priority logic as per documentation:
        // 1. If custom value exists with use_default_value!=false, show it
        $customValue = $product->getData($originalAttributeCode);
        $useDefaultValue = $product->getData('use_default_value');
        
        if (!empty($customValue) && $useDefaultValue === false) {
            return $customValue;
        }
        
        // 2. If translation exists, show translation (always from original website, not custom store view)
        $translatedValue = $product->getData($translatedAttributeCode);
        if (!empty($translatedValue)) {
            return $translatedValue;
        }
        
        // 3. Show original website_view value
        return $originalValue;
    }
}
