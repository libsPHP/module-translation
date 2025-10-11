<?php
namespace NativeMind\Translation\Cron;

use NativeMind\Translation\Model\ResourceModel\TranslationQueue;
use NativeMind\Translation\Model\TranslationQueueFactory;
use Magento\Framework\MessageQueue\PublisherInterface;
use NativeMind\Translation\Model\Data\TranslationMessage;
use Psr\Log\LoggerInterface;

class ProcessQueue
{
    /**
     * @var TranslationQueue
     */
    protected $translationQueueResource;

    /**
     * @var TranslationQueueFactory
     */
    protected $translationQueueFactory;

    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ProcessQueue constructor.
     *
     * @param TranslationQueue $translationQueueResource
     * @param TranslationQueueFactory $translationQueueFactory
     * @param PublisherInterface $publisher
     * @param LoggerInterface $logger
     */
    public function __construct(
        TranslationQueue $translationQueueResource,
        TranslationQueueFactory $translationQueueFactory,
        PublisherInterface $publisher,
        LoggerInterface $logger
    ) {
        $this->translationQueueResource = $translationQueueResource;
        $this->translationQueueFactory = $translationQueueFactory;
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    /**
     * Execute queue processing
     *
     * @return void
     */
    public function execute()
    {
        try {
            $items = $this->translationQueueResource->getNextPendingItems(50);
            
            if (empty($items)) {
                return;
            }

            $this->logger->info("Processing translation queue: " . count($items) . " items");

            foreach ($items as $item) {
                try {
                    /** @var \NativeMind\Translation\Model\TranslationQueue $queueItem */
                    $queueItem = $this->translationQueueFactory->create();
                    $queueItem->load($item['queue_id']);
                    
                    $queueItem->markAsProcessing()->save();

                    // Publish to message queue
                    $message = new TranslationMessage();
                    $message->setEntityType($queueItem->getData('entity_type'));
                    $message->setEntityId($queueItem->getData('entity_id'));
                    $message->setStoreId($queueItem->getData('store_id'));
                    $message->setForce(false);

                    $topic = 'nativemind.translation.' . $queueItem->getData('entity_type');
                    $this->publisher->publish($topic, $message);

                    $queueItem->markAsCompleted()->save();

                } catch (\Exception $e) {
                    $this->logger->error('Queue item processing failed: ' . $e->getMessage(), [
                        'queue_id' => $item['queue_id']
                    ]);
                    
                    if (isset($queueItem)) {
                        $queueItem->markAsFailed($e->getMessage())->save();
                    }
                }
            }

        } catch (\Exception $e) {
            $this->logger->error('Translation queue processing failed: ' . $e->getMessage());
        }
    }
}




