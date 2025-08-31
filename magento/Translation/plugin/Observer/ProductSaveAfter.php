<?php
namespace NativeMind\Translation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use NativeMind\Translation\Helper\Data as TranslationHelper;
use Magento\Catalog\Model\ProductRepository;
use Psr\Log\LoggerInterface;

class ProductSaveAfter implements ObserverInterface
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
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ProductSaveAfter constructor.
     * @param StoreManagerInterface $storeManager
     * @param TranslationHelper $translationHelper
     * @param ProductRepository $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TranslationHelper $translationHelper,
        ProductRepository $productRepository,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->translationHelper = $translationHelper;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * Auto-translate product after save
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        // Check if auto-translation is enabled
        if (!$this->translationHelper->isTranslationEnabled() || 
            !$this->translationHelper->scopeConfig->isSetFlag(
                'nativelang/general/auto_translate',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )) {
            return;
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();
        
        // Only translate if this is a save in the default store (admin)
        if ($product->getStoreId() != 0) {
            return;
        }

        try {
            $this->translateProductForAllStores($product);
        } catch (\Exception $e) {
            $this->logger->error('Auto-translation failed for product ' . $product->getId() . ': ' . $e->getMessage());
        }
    }

    /**
     * Translate product for all stores
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    private function translateProductForAllStores($product)
    {
        $stores = $this->storeManager->getStores();
        
        foreach ($stores as $store) {
            if ($store->getId() == 0) continue; // Skip admin store
            
            $locale = $this->translationHelper->getStoreLocale($store->getId());
            $this->translateProductForStore($product, $store->getId(), $locale);
        }
    }

    /**
     * Translate product for specific store
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $storeId
     * @param string $locale
     * @return void
     */
    private function translateProductForStore($product, $storeId, $locale)
    {
        try {
            // Load product for specific store
            $storeProduct = $this->productRepository->getById($product->getId(), false, $storeId);
            
            $needsSave = false;

            // Translate name if not already translated
            if ($product->getName() && !$storeProduct->getNameTranslated()) {
                $translatedName = $this->translationHelper->translateText($product->getName(), $locale, $storeId);
                $storeProduct->setNameTranslated($translatedName);
                $needsSave = true;
            }

            // Translate description if not already translated
            if ($product->getDescription() && !$storeProduct->getDescriptionTranslated()) {
                $translatedDescription = $this->translationHelper->translateText($product->getDescription(), $locale, $storeId);
                $storeProduct->setDescriptionTranslated($translatedDescription);
                $needsSave = true;
            }

            // Translate short description if not already translated
            if ($product->getShortDescription() && !$storeProduct->getShortDescriptionTranslated()) {
                $translatedShortDescription = $this->translationHelper->translateText($product->getShortDescription(), $locale, $storeId);
                $storeProduct->setShortDescriptionTranslated($translatedShortDescription);
                $needsSave = true;
            }

            if ($needsSave) {
                $storeProduct->setTranslationStatus('translated');
                $storeProduct->setLastTranslationDate(date('Y-m-d H:i:s'));
                $this->productRepository->save($storeProduct);
            }

        } catch (\Exception $e) {
            $this->logger->error('Translation failed for product ' . $product->getId() . ' in store ' . $storeId . ': ' . $e->getMessage());
            
            // Set error status
            try {
                $storeProduct = $this->productRepository->getById($product->getId(), false, $storeId);
                $storeProduct->setTranslationStatus('error');
                $this->productRepository->save($storeProduct);
            } catch (\Exception $saveException) {
                $this->logger->error('Failed to set error status: ' . $saveException->getMessage());
            }
        }
    }
}
