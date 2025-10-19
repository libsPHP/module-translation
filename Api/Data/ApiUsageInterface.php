<?php
/**
 * Copyright © NativeLang. All rights reserved.
 */
declare(strict_types=1);

namespace NativeLang\Translation\Api\Data;

/**
 * API Usage statistics interface
 */
interface ApiUsageInterface
{
    /**
     * Get total requests
     *
     * @return int
     */
    public function getTotalRequests(): int;

    /**
     * Get successful requests
     *
     * @return int
     */
    public function getSuccessfulRequests(): int;

    /**
     * Get failed requests
     *
     * @return int
     */
    public function getFailedRequests(): int;

    /**
     * Get total tokens used
     *
     * @return int
     */
    public function getTotalTokens(): int;

    /**
     * Get total cost
     *
     * @return float
     */
    public function getTotalCost(): float;

    /**
     * Get average response time (ms)
     *
     * @return float
     */
    public function getAverageResponseTime(): float;

    /**
     * Get daily usage data
     *
     * @return array
     */
    public function getDailyUsage(): array;
}

