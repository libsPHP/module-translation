<?php
namespace NativeMind\Translation\Model\Consumer;

use NativeMind\Translation\Api\Data\TranslationMessageInterface;
use Magento\Catalog\Model\CategoryRepository;
use NativeMind\Translation\Helper\Data as TranslationHelper;
use NativeMind\Translation\Model\TranslationHistoryFactory;
use Psr\Log\LoggerInterface;

class CategoryTranslationConsumer
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var TranslationHistoryFactory
     */
    protected $translationHistoryFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CategoryTranslationConsumer constructor.
     *
     * @param CategoryRepository $categoryRepository
     * @param TranslationHelper $translationHelper
     * @param TranslationHistoryFactory $translationHistoryFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        TranslationHelper $translationHelper,
        TranslationHistoryFactory $translationHistoryFactory,
        LoggerInterface $logger
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->translationHelper = $translationHelper;
        $this->translationHistoryFactory = $translationHistoryFactory;
        $this->logger = $logger;
    }

    /**
     * Process translation message
     *
     * @param TranslationMessageInterface $message
     * @return void
     */
    public function process(TranslationMessageInterface $message)
    {
        $startTime = microtime(true);
        
        try {
            $this->logger->info('Processing category translation', [
                'entity_id' => $message->getEntityId(),
                'store_id' => $message->getStoreId()
            ]);

            // Load original category
            $originalCategory = $this->categoryRepository->get($message->getEntityId(), 0);
            
            // Load category for target store
            $category = $this->categoryRepository->get($message->getEntityId(), $message->getStoreId());
            $locale = $this->translationHelper->getStoreLocale($message->getStoreId());

            // Translate name
            if ($originalCategory->getName() && ($message->getForce() || !$category->getData('name'))) {
                $translatedName = $this->translationHelper->translateText($originalCategory->getName(), $locale, $message->getStoreId());
                $category->setName($translatedName);
            }

            // Translate description
            if ($originalCategory->getDescription() && ($message->getForce() || !$category->getData('description'))) {
                $translatedDescription = $this->translationHelper->translateText($originalCategory->getDescription(), $locale, $message->getStoreId());
                $category->setDescription($translatedDescription);
            }

            // Save category
            $this->categoryRepository->save($category);

            $processingTime = (microtime(true) - $startTime) * 1000;

            // Save to history
            /** @var \NativeMind\Translation\Model\TranslationHistory $history */
            $history = $this->translationHistoryFactory->create();
            $history->setData([
                'entity_type' => 'category',
                'entity_id' => $message->getEntityId(),
                'store_id' => $message->getStoreId(),
                'status' => 'completed',
                'processing_time' => $processingTime
            ]);
            $history->save();

            $this->logger->info('Category translation completed', [
                'entity_id' => $message->getEntityId(),
                'processing_time' => $processingTime
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Category translation failed', [
                'entity_id' => $message->getEntityId(),
                'error' => $e->getMessage()
            ]);

            // Save error to history
            /** @var \NativeMind\Translation\Model\TranslationHistory $history */
            $history = $this->translationHistoryFactory->create();
            $history->setData([
                'entity_type' => 'category',
                'entity_id' => $message->getEntityId(),
                'store_id' => $message->getStoreId(),
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            $history->save();

            throw $e;
        }
    }
}

