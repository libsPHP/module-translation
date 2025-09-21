<?php
namespace NativeMind\Translation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use NativeMind\Translation\Helper\Data as TranslationHelper;
use Magento\Catalog\Model\CategoryRepository;
use Psr\Log\LoggerInterface;

class CategorySaveAfter implements ObserverInterface
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
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CategorySaveAfter constructor.
     * @param StoreManagerInterface $storeManager
     * @param TranslationHelper $translationHelper
     * @param CategoryRepository $categoryRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TranslationHelper $translationHelper,
        CategoryRepository $categoryRepository,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->translationHelper = $translationHelper;
        $this->categoryRepository = $categoryRepository;
        $this->logger = $logger;
    }

    /**
     * Auto-translate category after save
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

        /** @var \Magento\Catalog\Model\Category $category */
        $category = $observer->getEvent()->getCategory();
        
        // Only translate if this is a save in the default store (admin)
        if ($category->getStoreId() != 0) {
            return;
        }

        // Skip root categories
        if ($category->getLevel() <= 1) {
            return;
        }

        try {
            $this->translateCategoryForAllStores($category);
        } catch (\Exception $e) {
            $this->logger->error('Auto-translation failed for category ' . $category->getId() . ': ' . $e->getMessage());
        }
    }

    /**
     * Translate category for all stores
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return void
     */
    private function translateCategoryForAllStores($category)
    {
        $stores = $this->storeManager->getStores();
        
        foreach ($stores as $store) {
            if ($store->getId() == 0) continue; // Skip admin store
            
            $locale = $this->translationHelper->getStoreLocale($store->getId());
            $this->translateCategoryForStore($category, $store->getId(), $locale);
        }
    }

    /**
     * Translate category for specific store
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param int $storeId
     * @param string $locale
     * @return void
     */
    private function translateCategoryForStore($category, $storeId, $locale)
    {
        try {
            // Load category for specific store
            $storeCategory = $this->categoryRepository->get($category->getId(), $storeId);
            
            $needsSave = false;

            // Translate name
            if ($category->getName()) {
                $translatedName = $this->translationHelper->translateText($category->getName(), $locale, $storeId);
                $storeCategory->setName($translatedName);
                $needsSave = true;
            }

            // Translate description
            if ($category->getDescription()) {
                $translatedDescription = $this->translationHelper->translateText($category->getDescription(), $locale, $storeId);
                $storeCategory->setDescription($translatedDescription);
                $needsSave = true;
            }

            if ($needsSave) {
                $this->categoryRepository->save($storeCategory);
            }

        } catch (\Exception $e) {
            $this->logger->error('Translation failed for category ' . $category->getId() . ' in store ' . $storeId . ': ' . $e->getMessage());
        }
    }
}
