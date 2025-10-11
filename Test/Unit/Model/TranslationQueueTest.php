<?php
namespace NativeMind\Translation\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use NativeMind\Translation\Model\TranslationQueue;

/**
 * @covers \NativeMind\Translation\Model\TranslationQueue
 */
class TranslationQueueTest extends TestCase
{
    /**
     * @var TranslationQueue|\PHPUnit\Framework\MockObject\MockObject
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = $this->getMockBuilder(TranslationQueue::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData', 'setData'])
            ->getMock();
    }

    public function testCanRetryReturnsTrueWhenUnderLimit()
    {
        $this->model->method('getData')
            ->willReturnMap([
                ['retry_count', null, 1],
                ['max_retries', null, 3]
            ]);

        $result = $this->model->canRetry();
        $this->assertTrue($result);
    }

    public function testCanRetryReturnsFalseWhenAtLimit()
    {
        $this->model->method('getData')
            ->willReturnMap([
                ['retry_count', null, 3],
                ['max_retries', null, 3]
            ]);

        $result = $this->model->canRetry();
        $this->assertFalse($result);
    }

    public function testCanRetryReturnsFalseWhenOverLimit()
    {
        $this->model->method('getData')
            ->willReturnMap([
                ['retry_count', null, 5],
                ['max_retries', null, 3]
            ]);

        $result = $this->model->canRetry();
        $this->assertFalse($result);
    }

    public function testIncrementRetryCount()
    {
        $currentRetryCount = 2;
        
        $this->model->expects($this->once())
            ->method('getData')
            ->with('retry_count')
            ->willReturn($currentRetryCount);

        $this->model->expects($this->once())
            ->method('setData')
            ->with('retry_count', $currentRetryCount + 1)
            ->willReturnSelf();

        $result = $this->model->incrementRetryCount();
        $this->assertSame($this->model, $result);
    }

    public function testMarkAsProcessing()
    {
        $this->model->expects($this->exactly(2))
            ->method('setData')
            ->withConsecutive(
                ['status', TranslationQueue::STATUS_PROCESSING],
                [$this->matchesRegularExpression('/started_at/'), $this->anything()]
            )
            ->willReturnSelf();

        $result = $this->model->markAsProcessing();
        $this->assertSame($this->model, $result);
    }

    public function testMarkAsCompleted()
    {
        $this->model->expects($this->exactly(2))
            ->method('setData')
            ->withConsecutive(
                ['status', TranslationQueue::STATUS_COMPLETED],
                [$this->matchesRegularExpression('/completed_at/'), $this->anything()]
            )
            ->willReturnSelf();

        $result = $this->model->markAsCompleted();
        $this->assertSame($this->model, $result);
    }

    public function testMarkAsFailedWithRetry()
    {
        $errorMessage = 'Test error';
        
        $this->model->method('getData')
            ->willReturnMap([
                ['retry_count', null, 1],
                ['max_retries', null, 3]
            ]);

        $this->model->expects($this->exactly(4))
            ->method('setData')
            ->willReturnSelf();

        $result = $this->model->markAsFailed($errorMessage);
        $this->assertSame($this->model, $result);
    }

    public function testMarkAsFailedWithoutRetry()
    {
        $errorMessage = 'Test error';
        
        $this->model->method('getData')
            ->willReturnMap([
                ['retry_count', null, 3],
                ['max_retries', null, 3]
            ]);

        $this->model->expects($this->exactly(3))
            ->method('setData')
            ->willReturnSelf();

        $result = $this->model->markAsFailed($errorMessage);
        $this->assertSame($this->model, $result);
    }
}




