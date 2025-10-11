<?php
namespace NativeMind\Translation\Model\Consumer;

use NativeMind\Translation\Api\Data\TranslationBatchMessageInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use NativeMind\Translation\Model\Data\TranslationMessage;
use Psr\Log\LoggerInterface;

class BatchTranslationConsumer
{
    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * BatchTranslationConsumer constructor.
     *
     * @param PublisherInterface $publisher
     * @param LoggerInterface $logger
     */
    public function __construct(
        PublisherInterface $publisher,
        LoggerInterface $logger
    ) {
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    /**
     * Process batch translation message
     *
     * @param TranslationBatchMessageInterface $message
     * @return void
     */
    public function process(TranslationBatchMessageInterface $message)
    {
        try {
            $this->logger->info('Processing batch translation', [
                'batch_id' => $message->getBatchId(),
                'entity_type' => $message->getEntityType(),
                'entity_count' => count($message->getEntityIds()),
                'store_count' => count($message->getStoreIds())
            ]);

            $topic = 'nativemind.translation.' . $message->getEntityType();

            foreach ($message->getEntityIds() as $entityId) {
                foreach ($message->getStoreIds() as $storeId) {
                    $translationMessage = new TranslationMessage();
                    $translationMessage->setEntityType($message->getEntityType());
                    $translationMessage->setEntityId($entityId);
                    $translationMessage->setStoreId($storeId);
                    $translationMessage->setForce($message->getForce());

                    $this->publisher->publish($topic, $translationMessage);
                }
            }

            $this->logger->info('Batch translation messages published', [
                'batch_id' => $message->getBatchId(),
                'total_messages' => count($message->getEntityIds()) * count($message->getStoreIds())
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Batch translation failed', [
                'batch_id' => $message->getBatchId(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}

