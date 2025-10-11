<?php
namespace NativeMind\Translation\Test\Unit\Cron;

use PHPUnit\Framework\TestCase;
use NativeMind\Translation\Cron\CleanCache;
use NativeMind\Translation\Model\ResourceModel\TranslationCache;
use Psr\Log\LoggerInterface;

/**
 * @covers \NativeMind\Translation\Cron\CleanCache
 */
class CleanCacheTest extends TestCase
{
    /**
     * @var CleanCache
     */
    private $cron;

    /**
     * @var TranslationCache|\PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheResourceMock;

    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    protected function setUp(): void
    {
        $this->cacheResourceMock = $this->createMock(TranslationCache::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->cron = new CleanCache(
            $this->cacheResourceMock,
            $this->loggerMock
        );
    }

    public function testExecuteSuccess()
    {
        $deletedCount = 150;

        $this->cacheResourceMock->expects($this->once())
            ->method('cleanExpiredCache')
            ->willReturn($deletedCount);

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with($this->stringContains("Translation cache cleaned: {$deletedCount} expired entries removed"));

        $this->cron->execute();
    }

    public function testExecuteWithError()
    {
        $exception = new \Exception('Database error');

        $this->cacheResourceMock->expects($this->once())
            ->method('cleanExpiredCache')
            ->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Translation cache cleaning failed'));

        $this->cron->execute();
    }

    public function testExecuteWithZeroDeleted()
    {
        $this->cacheResourceMock->expects($this->once())
            ->method('cleanExpiredCache')
            ->willReturn(0);

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with($this->stringContains('0 expired entries removed'));

        $this->cron->execute();
    }
}

