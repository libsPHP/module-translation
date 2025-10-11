<?php
namespace NativeMind\Translation\Cron;

use NativeMind\Translation\Model\ResourceModel\TranslationCache;
use Psr\Log\LoggerInterface;

class CleanCache
{
    /**
     * @var TranslationCache
     */
    protected $translationCacheResource;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CleanCache constructor.
     *
     * @param TranslationCache $translationCacheResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        TranslationCache $translationCacheResource,
        LoggerInterface $logger
    ) {
        $this->translationCacheResource = $translationCacheResource;
        $this->logger = $logger;
    }

    /**
     * Execute cache cleaning
     *
     * @return void
     */
    public function execute()
    {
        try {
            $deletedCount = $this->translationCacheResource->cleanExpiredCache();
            $this->logger->info("Translation cache cleaned: {$deletedCount} expired entries removed");
        } catch (\Exception $e) {
            $this->logger->error('Translation cache cleaning failed: ' . $e->getMessage());
        }
    }
}

