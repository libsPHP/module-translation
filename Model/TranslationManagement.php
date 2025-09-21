<?php
namespace NativeMind\Translation\Model;

use NativeMind\Translation\Api\TranslationManagementInterface;
use NativeMind\Translation\Api\Data\TranslationResultInterfaceFactory;
use NativeMind\Translation\Api\Data\TranslationStatusInterfaceFactory;
use NativeMind\Translation\Api\Data\TranslationStatsInterfaceFactory;
use NativeMind\Translation\Helper\Data as TranslationHelper;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class TranslationManagement implements TranslationManagementInterface
{
    /**
     * @var TranslationResultInterfaceFactory
     */
    protected $translationResultFactory;

    /**
     * @var TranslationStatusInterfaceFactory
     */
    protected $translationStatusFactory;

    /**
     * @var TranslationStatsInterfaceFactory
     */
    protected $translationStatsFactory;

    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $translationJobs = [];

    /**
     * TranslationManagement constructor.
     * @param TranslationResultInterfaceFactory $translationResultFactory
     * @param TranslationStatusInterfaceFactory $translationStatusFactory
     * @param TranslationStatsInterfaceFactory $translationStatsFactory
     * @param TranslationHelper $translationHelper
     * @param ProductRepository $productRepository
     * @param ProductCollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        TranslationResultInterfaceFactory $translationResultFactory,
        TranslationStatusInterfaceFactory $translationStatusFactory,
        TranslationStatsInterfaceFactory $translationStatsFactory,
        TranslationHelper $translationHelper,
        ProductRepository $productRepository,
        ProductCollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->translationResultFactory = $translationResultFactory;
        $this->translationStatusFactory = $translationStatusFactory;
        $this->translationStatsFactory = $translationStatsFactory;
        $this->translationHelper = $translationHelper;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function translateText($text, $targetLanguage, $sourceLanguage = null, $storeId = null)
    {
        $translationId = uniqid('trans_');
        $result = $this->translationResultFactory->create();
        
        try {
            $result->setTranslationId($translationId)
                   ->setOriginalText($text)
                   ->setTargetLanguage($targetLanguage)
                   ->setSourceLanguage($sourceLanguage)
                   ->setStatus('processing')
                   ->setCreatedAt(date('Y-m-d H:i:s'));

            $translatedText = $this->translationHelper->translateText($text, $targetLanguage, $storeId);
            
            $result->setTranslatedText($translatedText)
                   ->setStatus('completed')
                   ->setConfidence(0.95); // Mock confidence score

        } catch (\Exception $e) {
            $this->logger->error('Translation failed: ' . $e->getMessage());
            $result->setStatus('error')
                   ->setErrorMessage($e->getMessage());
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function translateTexts($texts, $targetLanguage, $sourceLanguage = null, $storeId = null)
    {
        $results = [];
        foreach ($texts as $text) {
            $results[] = $this->translateText($text, $targetLanguage, $sourceLanguage, $storeId);
        }
        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function translateProduct($productId, $storeId, $force = false)
    {
        $translationId = uniqid('prod_');
        $result = $this->translationResultFactory->create();
        
        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
            $locale = $this->translationHelper->getStoreLocale($storeId);

            $result->setTranslationId($translationId)
                   ->setStatus('processing')
                   ->setCreatedAt(date('Y-m-d H:i:s'));

            $translations = [];
            
            // Translate name
            if ($product->getName() && ($force || !$product->getNameTranslated())) {
                $translatedName = $this->translationHelper->translateText($product->getName(), $locale, $storeId);
                $product->setNameTranslated($translatedName);
                $translations['name'] = $translatedName;
            }

            // Translate description
            if ($product->getDescription() && ($force || !$product->getDescriptionTranslated())) {
                $translatedDescription = $this->translationHelper->translateText($product->getDescription(), $locale, $storeId);
                $product->setDescriptionTranslated($translatedDescription);
                $translations['description'] = $translatedDescription;
            }

            // Translate short description
            if ($product->getShortDescription() && ($force || !$product->getShortDescriptionTranslated())) {
                $translatedShortDescription = $this->translationHelper->translateText($product->getShortDescription(), $locale, $storeId);
                $product->setShortDescriptionTranslated($translatedShortDescription);
                $translations['short_description'] = $translatedShortDescription;
            }

            $product->setTranslationStatus('translated');
            $product->setLastTranslationDate(date('Y-m-d H:i:s'));
            $this->productRepository->save($product);

            $result->setStatus('completed')
                   ->setTranslatedText(json_encode($translations))
                   ->setConfidence(0.95);

        } catch (\Exception $e) {
            $this->logger->error('Product translation failed: ' . $e->getMessage());
            $result->setStatus('error')
                   ->setErrorMessage($e->getMessage());
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function translateProducts($productIds, $storeId, $force = false)
    {
        $results = [];
        foreach ($productIds as $productId) {
            $results[] = $this->translateProduct($productId, $storeId, $force);
        }
        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationStatus($translationId)
    {
        $status = $this->translationStatusFactory->create();
        
        // In a real implementation, this would fetch from database
        if (isset($this->translationJobs[$translationId])) {
            $job = $this->translationJobs[$translationId];
            $status->setTranslationId($translationId)
                   ->setStatus($job['status'])
                   ->setProgress($job['progress'])
                   ->setTotalItems($job['total_items'])
                   ->setProcessedItems($job['processed_items'])
                   ->setErrorCount($job['error_count'])
                   ->setStartedAt($job['started_at'])
                   ->setCompletedAt($job['completed_at'] ?? null);
        } else {
            $status->setTranslationId($translationId)
                   ->setStatus('not_found')
                   ->setProgress(0)
                   ->setTotalItems(0)
                   ->setProcessedItems(0)
                   ->setErrorCount(0);
        }

        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationStats($storeId = null)
    {
        $stats = $this->translationStatsFactory->create();
        
        try {
            $productCollection = $this->productCollectionFactory->create();
            if ($storeId) {
                $productCollection->addStoreFilter($storeId);
                $stats->setStoreId($storeId);
            }

            $totalProducts = $productCollection->getSize();
            
            // Count translated products
            $translatedCollection = clone $productCollection;
            $translatedCollection->addAttributeToFilter('translation_status', 'translated');
            $translatedProducts = $translatedCollection->getSize();

            // Count pending products
            $pendingCollection = clone $productCollection;
            $pendingCollection->addAttributeToFilter([
                ['attribute' => 'translation_status', 'eq' => 'pending'],
                ['attribute' => 'translation_status', 'null' => true]
            ]);
            $pendingProducts = $pendingCollection->getSize();

            // Count error products
            $errorCollection = clone $productCollection;
            $errorCollection->addAttributeToFilter('translation_status', 'error');
            $errorProducts = $errorCollection->getSize();

            $stats->setTotalProducts($totalProducts)
                  ->setTranslatedProducts($translatedProducts)
                  ->setPendingProducts($pendingProducts)
                  ->setErrorProducts($errorProducts)
                  ->setTotalCategories(0) // TODO: Implement category stats
                  ->setTranslatedCategories(0)
                  ->setApiUsage(['daily_calls' => 150, 'monthly_limit' => 10000]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to get translation stats: ' . $e->getMessage());
        }

        return $stats;
    }
}
