<?php
namespace NativeMind\Translation\Test\Unit\Cron;

use PHPUnit\Framework\TestCase;
use NativeMind\Translation\Cron\CleanQueue;
use NativeMind\Translation\Model\ResourceModel\TranslationQueue;
use Psr\Log\LoggerInterface;

/**
 * @covers \NativeMind\Translation\Cron\CleanQueue
 */
class CleanQueueTest extends TestCase
{
    /**
     * @var CleanQueue
     */
    private $cron;

    /**
     * @var TranslationQueue|\PHPUnit\Framework\MockObject\MockObject
     */
    private $queueResourceMock;

    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    protected function setUp(): void
    {
        $this->queueResourceMock = $this->createMock(TranslationQueue::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->cron = new CleanQueue(
            $this->queueResourceMock,
            $this->loggerMock
        );
    }

    public function testExecuteSuccess()
    {
        $deletedCount = 50;

        $this->queueResourceMock->expects($this->once())
            ->method('cleanCompletedItems')
            ->with(30)
            ->willReturn($deletedCount);

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with($this->stringContains("Translation queue cleaned: {$deletedCount} completed items removed"));

        $this->cron->execute();
    }

    public function testExecuteWithError()
    {
        $exception = new \Exception('Database error');

        $this->queueResourceMock->expects($this->once())
            ->method('cleanCompletedItems')
            ->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Translation queue cleaning failed'));

        $this->cron->execute();
    }
}




