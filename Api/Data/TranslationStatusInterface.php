<?php
namespace NativeMind\Translation\Api\Data;

/**
 * Translation status interface
 */
interface TranslationStatusInterface
{
    const TRANSLATION_ID = 'translation_id';
    const STATUS = 'status';
    const PROGRESS = 'progress';
    const TOTAL_ITEMS = 'total_items';
    const PROCESSED_ITEMS = 'processed_items';
    const ERROR_COUNT = 'error_count';
    const STARTED_AT = 'started_at';
    const COMPLETED_AT = 'completed_at';

    /**
     * Get translation ID
     *
     * @return string
     */
    public function getTranslationId();

    /**
     * Set translation ID
     *
     * @param string $translationId
     * @return $this
     */
    public function setTranslationId($translationId);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get progress percentage
     *
     * @return int
     */
    public function getProgress();

    /**
     * Set progress percentage
     *
     * @param int $progress
     * @return $this
     */
    public function setProgress($progress);

    /**
     * Get total items
     *
     * @return int
     */
    public function getTotalItems();

    /**
     * Set total items
     *
     * @param int $totalItems
     * @return $this
     */
    public function setTotalItems($totalItems);

    /**
     * Get processed items
     *
     * @return int
     */
    public function getProcessedItems();

    /**
     * Set processed items
     *
     * @param int $processedItems
     * @return $this
     */
    public function setProcessedItems($processedItems);

    /**
     * Get error count
     *
     * @return int
     */
    public function getErrorCount();

    /**
     * Set error count
     *
     * @param int $errorCount
     * @return $this
     */
    public function setErrorCount($errorCount);

    /**
     * Get started at
     *
     * @return string
     */
    public function getStartedAt();

    /**
     * Set started at
     *
     * @param string $startedAt
     * @return $this
     */
    public function setStartedAt($startedAt);

    /**
     * Get completed at
     *
     * @return string|null
     */
    public function getCompletedAt();

    /**
     * Set completed at
     *
     * @param string $completedAt
     * @return $this
     */
    public function setCompletedAt($completedAt);
}
