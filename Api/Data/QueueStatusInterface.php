<?php
/**
 * Copyright © NativeLang. All rights reserved.
 */
declare(strict_types=1);

namespace NativeLang\Translation\Api\Data;

/**
 * Queue status interface
 */
interface QueueStatusInterface
{
    /**
     * Get total items in queue
     *
     * @return int
     */
    public function getTotalItems(): int;

    /**
     * Get pending items
     *
     * @return int
     */
    public function getPendingItems(): int;

    /**
     * Get processing items
     *
     * @return int
     */
    public function getProcessingItems(): int;

    /**
     * Get completed items
     *
     * @return int
     */
    public function getCompletedItems(): int;

    /**
     * Get failed items
     *
     * @return int
     */
    public function getFailedItems(): int;

    /**
     * Get queue items
     *
     * @return array
     */
    public function getQueueItems(): array;
}

