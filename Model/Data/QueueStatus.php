<?php
/**
 * Copyright © NativeLang. All rights reserved.
 */
declare(strict_types=1);

namespace NativeLang\Translation\Model\Data;

use NativeLang\Translation\Api\Data\QueueStatusInterface;

/**
 * Queue status model
 */
class QueueStatus implements QueueStatusInterface
{
    private $totalItems = 0;
    private $pendingItems = 0;
    private $processingItems = 0;
    private $completedItems = 0;
    private $failedItems = 0;
    private $queueItems = [];

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function setTotalItems(int $count): self
    {
        $this->totalItems = $count;
        return $this;
    }

    public function getPendingItems(): int
    {
        return $this->pendingItems;
    }

    public function setPendingItems(int $count): self
    {
        $this->pendingItems = $count;
        return $this;
    }

    public function getProcessingItems(): int
    {
        return $this->processingItems;
    }

    public function setProcessingItems(int $count): self
    {
        $this->processingItems = $count;
        return $this;
    }

    public function getCompletedItems(): int
    {
        return $this->completedItems;
    }

    public function setCompletedItems(int $count): self
    {
        $this->completedItems = $count;
        return $this;
    }

    public function getFailedItems(): int
    {
        return $this->failedItems;
    }

    public function setFailedItems(int $count): self
    {
        $this->failedItems = $count;
        return $this;
    }

    public function getQueueItems(): array
    {
        return $this->queueItems;
    }

    public function setQueueItems(array $items): self
    {
        $this->queueItems = $items;
        return $this;
    }
}

