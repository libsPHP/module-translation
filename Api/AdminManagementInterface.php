<?php
/**
 * Copyright © NativeLang. All rights reserved.
 */
declare(strict_types=1);

namespace NativeLang\Translation\Api;

/**
 * Admin management interface for translation module
 */
interface AdminManagementInterface
{
    /**
     * Get translation queue status
     *
     * @return \NativeLang\Translation\Api\Data\QueueStatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getQueueStatus(): \NativeLang\Translation\Api\Data\QueueStatusInterface;

    /**
     * Get API usage statistics
     *
     * @param string $startDate
     * @param string $endDate
     * @return \NativeLang\Translation\Api\Data\ApiUsageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getApiUsage(string $startDate, string $endDate): \NativeLang\Translation\Api\Data\ApiUsageInterface;

    /**
     * Clear translation cache
     *
     * @param string|null $languageCode
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearCache(?string $languageCode = null): bool;

    /**
     * Force translate product
     *
     * @param int $productId
     * @param string $targetLanguage
     * @param bool $overwrite
     * @return \NativeLang\Translation\Api\Data\TranslationResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function forceTranslateProduct(
        int $productId,
        string $targetLanguage,
        bool $overwrite = false
    ): \NativeLang\Translation\Api\Data\TranslationResultInterface;

    /**
     * Force translate category
     *
     * @param int $categoryId
     * @param string $targetLanguage
     * @param bool $overwrite
     * @return \NativeLang\Translation\Api\Data\TranslationResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function forceTranslateCategory(
        int $categoryId,
        string $targetLanguage,
        bool $overwrite = false
    ): \NativeLang\Translation\Api\Data\TranslationResultInterface;

    /**
     * Get translation history
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $entityType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTranslationHistory(
        int $limit = 50,
        int $offset = 0,
        ?string $entityType = null
    ): array;

    /**
     * Retry failed translations
     *
     * @param int|null $limit
     * @return array Array with 'success' and 'failed' counts
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function retryFailedTranslations(?int $limit = null): array;

    /**
     * Get queue item details
     *
     * @param int $queueId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQueueItemDetails(int $queueId): array;

    /**
     * Cancel queue item
     *
     * @param int $queueId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cancelQueueItem(int $queueId): bool;

    /**
     * Get translation configuration
     *
     * @return array
     */
    public function getConfiguration(): array;

    /**
     * Update translation configuration
     *
     * @param array $config
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateConfiguration(array $config): bool;

    /**
     * Test translation service connection
     *
     * @return array Test results
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testConnection(): array;

    /**
     * Purge old translation history
     *
     * @param int $daysOld
     * @return int Number of records deleted
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function purgeOldHistory(int $daysOld = 90): int;
}

