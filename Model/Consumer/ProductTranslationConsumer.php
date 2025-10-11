<?php
namespace NativeMind\Translation\Model\Consumer;

use NativeMind\Translation\Api\Data\TranslationMessageInterface;
use NativeMind\Translation\Api\TranslationManagementInterface;
use NativeMind\Translation\Model\TranslationHistoryFactory;
use Psr\Log\LoggerInterface;

class ProductTranslationConsumer
{
    /**
     * @var TranslationManagementInterface
     */
    protected $translationManagement;

    /**
     * @var TranslationHistoryFactory
     */
    protected $translationHistoryFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ProductTranslationConsumer constructor.
     *
     * @param TranslationManagementInterface $translationManagement
     * @param TranslationHistoryFactory $translationHistoryFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        TranslationManagementInterface $translationManagement,
        TranslationHistoryFactory $translationHistoryFactory,
        LoggerInterface $logger
    ) {
        $this->translationManagement = $translationManagement;
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
            $this->logger->info('Processing product translation', [
                'entity_id' => $message->getEntityId(),
                'store_id' => $message->getStoreId()
            ]);

            $result = $this->translationManagement->translateProduct(
                $message->getEntityId(),
                $message->getStoreId(),
                $message->getForce()
            );

            $processingTime = (microtime(true) - $startTime) * 1000;

            // Save to history
            /** @var \NativeMind\Translation\Model\TranslationHistory $history */
            $history = $this->translationHistoryFactory->create();
            $history->setData([
                'entity_type' => 'product',
                'entity_id' => $message->getEntityId(),
                'store_id' => $message->getStoreId(),
                'status' => $result->getStatus(),
                'processing_time' => $processingTime,
                'error_message' => $result->getErrorMessage()
            ]);
            $history->save();

            $this->logger->info('Product translation completed', [
                'entity_id' => $message->getEntityId(),
                'status' => $result->getStatus(),
                'processing_time' => $processingTime
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Product translation failed', [
                'entity_id' => $message->getEntityId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}




