<?php
namespace NativeMind\Translation\Model;

use Magento\Framework\Model\AbstractModel;

class TranslationQueue extends AbstractModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_RETRY = 'retry';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\NativeMind\Translation\Model\ResourceModel\TranslationQueue::class);
    }

    /**
     * Check if can retry
     *
     * @return bool
     */
    public function canRetry()
    {
        return (int)$this->getData('retry_count') < (int)$this->getData('max_retries');
    }

    /**
     * Increment retry count
     *
     * @return $this
     */
    public function incrementRetryCount()
    {
        $this->setData('retry_count', (int)$this->getData('retry_count') + 1);
        return $this;
    }

    /**
     * Mark as processing
     *
     * @return $this
     */
    public function markAsProcessing()
    {
        $this->setData('status', self::STATUS_PROCESSING);
        $this->setData('started_at', date('Y-m-d H:i:s'));
        return $this;
    }

    /**
     * Mark as completed
     *
     * @return $this
     */
    public function markAsCompleted()
    {
        $this->setData('status', self::STATUS_COMPLETED);
        $this->setData('completed_at', date('Y-m-d H:i:s'));
        return $this;
    }

    /**
     * Mark as failed
     *
     * @param string $errorMessage
     * @return $this
     */
    public function markAsFailed($errorMessage)
    {
        $this->setData('error_message', $errorMessage);
        
        if ($this->canRetry()) {
            $this->setData('status', self::STATUS_RETRY);
            $this->incrementRetryCount();
            // Schedule retry with exponential backoff
            $retryDelay = pow(2, (int)$this->getData('retry_count')) * 60; // 2, 4, 8 minutes
            $this->setData('scheduled_at', date('Y-m-d H:i:s', time() + $retryDelay));
        } else {
            $this->setData('status', self::STATUS_FAILED);
            $this->setData('completed_at', date('Y-m-d H:i:s'));
        }
        
        return $this;
    }
}

