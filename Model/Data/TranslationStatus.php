<?php
namespace NativeMind\Translation\Model\Data;

use NativeMind\Translation\Api\Data\TranslationStatusInterface;
use Magento\Framework\DataObject;

class TranslationStatus extends DataObject implements TranslationStatusInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTranslationId()
    {
        return $this->getData(self::TRANSLATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslationId($translationId)
    {
        return $this->setData(self::TRANSLATION_ID, $translationId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getProgress()
    {
        return $this->getData(self::PROGRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProgress($progress)
    {
        return $this->setData(self::PROGRESS, $progress);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalItems()
    {
        return $this->getData(self::TOTAL_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalItems($totalItems)
    {
        return $this->setData(self::TOTAL_ITEMS, $totalItems);
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessedItems()
    {
        return $this->getData(self::PROCESSED_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProcessedItems($processedItems)
    {
        return $this->setData(self::PROCESSED_ITEMS, $processedItems);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCount()
    {
        return $this->getData(self::ERROR_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setErrorCount($errorCount)
    {
        return $this->setData(self::ERROR_COUNT, $errorCount);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartedAt()
    {
        return $this->getData(self::STARTED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartedAt($startedAt)
    {
        return $this->setData(self::STARTED_AT, $startedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletedAt()
    {
        return $this->getData(self::COMPLETED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompletedAt($completedAt)
    {
        return $this->setData(self::COMPLETED_AT, $completedAt);
    }
}
