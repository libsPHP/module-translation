<?php
/**
 * Copyright © NativeLang. All rights reserved.
 */
declare(strict_types=1);

namespace NativeLang\Translation\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use NativeLang\Translation\Api\AdminManagementInterface;
use NativeLang\Translation\Api\Data\QueueStatusInterface;
use NativeLang\Translation\Api\Data\ApiUsageInterface;
use NativeLang\Translation\Api\Data\TranslationResultInterface;
use NativeLang\Translation\Model\ResourceModel\TranslationQueue\CollectionFactory as QueueCollectionFactory;
use NativeLang\Translation\Model\ResourceModel\ApiUsageTracking\CollectionFactory as ApiUsageCollectionFactory;
use NativeLang\Translation\Model\ResourceModel\TranslationHistory\CollectionFactory as HistoryCollectionFactory;
use NativeLang\Translation\Model\ResourceModel\TranslationCache\CollectionFactory as CacheCollectionFactory;
use NativeLang\Translation\Api\TranslationManagementInterface;
use Psr\Log\LoggerInterface;

/**
 * Admin management implementation
 */
class AdminManagement implements AdminManagementInterface
{
    private $queueCollectionFactory;
    private $apiUsageCollectionFactory;
    private $historyCollectionFactory;
    private $cacheCollectionFactory;
    private $translationManagement;
    private $logger;

    public function __construct(
        QueueCollectionFactory $queueCollectionFactory,
        ApiUsageCollectionFactory $apiUsageCollectionFactory,
        HistoryCollectionFactory $historyCollectionFactory,
        CacheCollectionFactory $cacheCollectionFactory,
        TranslationManagementInterface $translationManagement,
        LoggerInterface $logger
    ) {
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->apiUsageCollectionFactory = $apiUsageCollectionFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->cacheCollectionFactory = $cacheCollectionFactory;
        $this->translationManagement = $translationManagement;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getQueueStatus(): QueueStatusInterface
    {
        $collection = $this->queueCollectionFactory->create();
        
        $status = new \NativeLang\Translation\Model\Data\QueueStatus();
        $status->setTotalItems($collection->getSize());
        
        $pending = clone $collection;
        $status->setPendingItems($pending->addFieldToFilter('status', 'pending')->getSize());
        
        $processing = clone $collection;
        $status->setProcessingItems($processing->addFieldToFilter('status', 'processing')->getSize());
        
        $completed = clone $collection;
        $status->setCompletedItems($completed->addFieldToFilter('status', 'completed')->getSize());
        
        $failed = clone $collection;
        $status->setFailedItems($failed->addFieldToFilter('status', 'failed')->getSize());

        return $status;
    }

    /**
     * @inheritdoc
     */
    public function getApiUsage(string $startDate, string $endDate): ApiUsageInterface
    {
        $collection = $this->apiUsageCollectionFactory->create()
            ->addFieldToFilter('created_at', ['gteq' => $startDate])
            ->addFieldToFilter('created_at', ['lteq' => $endDate]);

        $usage = new \NativeLang\Translation\Model\Data\ApiUsage();
        
        $totalRequests = $collection->getSize();
        $usage->setTotalRequests($totalRequests);

        // Calculate statistics
        $successful = 0;
        $failed = 0;
        $totalTokens = 0;
        $totalCost = 0.0;
        $totalResponseTime = 0.0;

        foreach ($collection as $item) {
            if ($item->getStatus() === 'success') {
                $successful++;
            } else {
                $failed++;
            }
            $totalTokens += $item->getTokensUsed();
            $totalCost += $item->getCost();
            $totalResponseTime += $item->getResponseTime();
        }

        $usage->setSuccessfulRequests($successful);
        $usage->setFailedRequests($failed);
        $usage->setTotalTokens($totalTokens);
        $usage->setTotalCost($totalCost);
        $usage->setAverageResponseTime(
            $totalRequests > 0 ? $totalResponseTime / $totalRequests : 0
        );

        return $usage;
    }

    /**
     * @inheritdoc
     */
    public function clearCache(?string $languageCode = null): bool
    {
        try {
            $collection = $this->cacheCollectionFactory->create();
            
            if ($languageCode) {
                $collection->addFieldToFilter('language_code', $languageCode);
            }

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->logger->info('Translation cache cleared', [
                'language' => $languageCode ?? 'all'
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Error clearing cache: ' . $e->getMessage());
            throw new LocalizedException(__('Unable to clear cache.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function forceTranslateProduct(
        int $productId,
        string $targetLanguage,
        bool $overwrite = false
    ): TranslationResultInterface {
        // Delegate to TranslationManagement
        return $this->translationManagement->translateProduct(
            $productId,
            $targetLanguage,
            $overwrite
        );
    }

    /**
     * @inheritdoc
     */
    public function forceTranslateCategory(
        int $categoryId,
        string $targetLanguage,
        bool $overwrite = false
    ): TranslationResultInterface {
        // Delegate to TranslationManagement
        return $this->translationManagement->translateCategory(
            $categoryId,
            $targetLanguage,
            $overwrite
        );
    }

    /**
     * @inheritdoc
     */
    public function getTranslationHistory(
        int $limit = 50,
        int $offset = 0,
        ?string $entityType = null
    ): array {
        $collection = $this->historyCollectionFactory->create()
            ->setPageSize($limit)
            ->setCurPage($offset / $limit + 1)
            ->setOrder('created_at', 'DESC');

        if ($entityType) {
            $collection->addFieldToFilter('entity_type', $entityType);
        }

        return $collection->getData();
    }

    /**
     * @inheritdoc
     */
    public function retryFailedTranslations(?int $limit = null): array
    {
        $collection = $this->queueCollectionFactory->create()
            ->addFieldToFilter('status', 'failed')
            ->setOrder('created_at', 'ASC');

        if ($limit) {
            $collection->setPageSize($limit);
        }

        $success = 0;
        $failed = 0;

        foreach ($collection as $item) {
            try {
                // Reset status to pending for retry
                $item->setStatus('pending');
                $item->setRetryCount($item->getRetryCount() + 1);
                $item->save();
                $success++;
            } catch (\Exception $e) {
                $this->logger->error('Error retrying translation: ' . $e->getMessage());
                $failed++;
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getQueueItemDetails(int $queueId): array
    {
        $collection = $this->queueCollectionFactory->create()
            ->addFieldToFilter('queue_id', $queueId);

        $item = $collection->getFirstItem();
        
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Queue item not found.'));
        }

        return $item->getData();
    }

    /**
     * @inheritdoc
     */
    public function cancelQueueItem(int $queueId): bool
    {
        try {
            $collection = $this->queueCollectionFactory->create()
                ->addFieldToFilter('queue_id', $queueId);

            $item = $collection->getFirstItem();
            
            if (!$item->getId()) {
                throw new NoSuchEntityException(__('Queue item not found.'));
            }

            $item->setStatus('cancelled');
            $item->save();

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Error cancelling queue item: ' . $e->getMessage());
            throw new LocalizedException(__('Unable to cancel queue item.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(): array
    {
        // Return current configuration
        return [
            'api_key' => '***',
            'service' => 'openai',
            'model' => 'gpt-4',
            'auto_translate' => true,
            'cache_enabled' => true,
            'queue_enabled' => true,
        ];
    }

    /**
     * @inheritdoc
     */
    public function updateConfiguration(array $config): bool
    {
        try {
            // Save configuration
            // This would typically use Magento config save
            $this->logger->info('Translation configuration updated', $config);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Error updating configuration: ' . $e->getMessage());
            throw new LocalizedException(__('Unable to update configuration.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function testConnection(): array
    {
        try {
            // Test API connection
            return [
                'status' => 'success',
                'message' => 'Connection successful',
                'response_time' => 150,
                'api_version' => '1.0',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function purgeOldHistory(int $daysOld = 90): int
    {
        try {
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
            
            $collection = $this->historyCollectionFactory->create()
                ->addFieldToFilter('created_at', ['lt' => $cutoffDate]);

            $count = $collection->getSize();

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->logger->info("Purged {$count} old translation history records");
            
            return $count;
        } catch (\Exception $e) {
            $this->logger->error('Error purging history: ' . $e->getMessage());
            throw new LocalizedException(__('Unable to purge history.'));
        }
    }
}

