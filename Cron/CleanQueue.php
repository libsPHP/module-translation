<?php
namespace NativeMind\Translation\Cron;

use NativeMind\Translation\Model\ResourceModel\TranslationQueue;
use Psr\Log\LoggerInterface;

class CleanQueue
{
    /**
     * @var TranslationQueue
     */
    protected $translationQueueResource;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CleanQueue constructor.
     *
     * @param TranslationQueue $translationQueueResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        TranslationQueue $translationQueueResource,
        LoggerInterface $logger
    ) {
        $this->translationQueueResource = $translationQueueResource;
        $this->logger = $logger;
    }

    /**
     * Execute queue cleaning
     *
     * @return void
     */
    public function execute()
    {
        try {
            $deletedCount = $this->translationQueueResource->cleanCompletedItems(30);
            $this->logger->info("Translation queue cleaned: {$deletedCount} completed items removed");
        } catch (\Exception $e) {
            $this->logger->error('Translation queue cleaning failed: ' . $e->getMessage());
        }
    }
}

