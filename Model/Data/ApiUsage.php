<?php
/**
 * Copyright © NativeLang. All rights reserved.
 */
declare(strict_types=1);

namespace NativeLang\Translation\Model\Data;

use NativeLang\Translation\Api\Data\ApiUsageInterface;

/**
 * API Usage model
 */
class ApiUsage implements ApiUsageInterface
{
    private $totalRequests = 0;
    private $successfulRequests = 0;
    private $failedRequests = 0;
    private $totalTokens = 0;
    private $totalCost = 0.0;
    private $averageResponseTime = 0.0;
    private $dailyUsage = [];

    public function getTotalRequests(): int
    {
        return $this->totalRequests;
    }

    public function setTotalRequests(int $count): self
    {
        $this->totalRequests = $count;
        return $this;
    }

    public function getSuccessfulRequests(): int
    {
        return $this->successfulRequests;
    }

    public function setSuccessfulRequests(int $count): self
    {
        $this->successfulRequests = $count;
        return $this;
    }

    public function getFailedRequests(): int
    {
        return $this->failedRequests;
    }

    public function setFailedRequests(int $count): self
    {
        $this->failedRequests = $count;
        return $this;
    }

    public function getTotalTokens(): int
    {
        return $this->totalTokens;
    }

    public function setTotalTokens(int $tokens): self
    {
        $this->totalTokens = $tokens;
        return $this;
    }

    public function getTotalCost(): float
    {
        return $this->totalCost;
    }

    public function setTotalCost(float $cost): self
    {
        $this->totalCost = $cost;
        return $this;
    }

    public function getAverageResponseTime(): float
    {
        return $this->averageResponseTime;
    }

    public function setAverageResponseTime(float $time): self
    {
        $this->averageResponseTime = $time;
        return $this;
    }

    public function getDailyUsage(): array
    {
        return $this->dailyUsage;
    }

    public function setDailyUsage(array $usage): self
    {
        $this->dailyUsage = $usage;
        return $this;
    }
}

