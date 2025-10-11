<?php
namespace NativeMind\Translation\Test\Unit\Model\Consumer;

use PHPUnit\Framework\TestCase;
use NativeMind\Translation\Model\Consumer\ProductTranslationConsumer;
use NativeMind\Translation\Api\Data\TranslationMessageInterface;
use NativeMind\Translation\Api\TranslationManagementInterface;
use NativeMind\Translation\Model\TranslationHistoryFactory;
use NativeMind\Translation\Model\TranslationHistory;
use NativeMind\Translation\Api\Data\TranslationResultInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \NativeMind\Translation\Model\Consumer\ProductTranslationConsumer
 */
class ProductTranslationConsumerTest extends TestCase
{
    /**
     * @var ProductTranslationConsumer
     */
    private $consumer;

    /**
     * @var TranslationManagementInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $translationManagementMock;

    /**
     * @var TranslationHistoryFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $historyFactoryMock;

    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    protected function setUp(): void
    {
        $this->translationManagementMock = $this->createMock(TranslationManagementInterface::class);
        $this->historyFactoryMock = $this->createMock(TranslationHistoryFactory::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->consumer = new ProductTranslationConsumer(
            $this->translationManagementMock,
            $this->historyFactoryMock,
            $this->loggerMock
        );
    }

    public function testProcessSuccess()
    {
        $messageMock = $this->createMock(TranslationMessageInterface::class);
        $messageMock->method('getEntityId')->willReturn(1);
        $messageMock->method('getStoreId')->willReturn(2);
        $messageMock->method('getForce')->willReturn(false);

        $resultMock = $this->createMock(TranslationResultInterface::class);
        $resultMock->method('getStatus')->willReturn('completed');
        $resultMock->method('getErrorMessage')->willReturn(null);

        $this->translationManagementMock->expects($this->once())
            ->method('translateProduct')
            ->with(1, 2, false)
            ->willReturn($resultMock);

        $historyMock = $this->createMock(TranslationHistory::class);
        $historyMock->expects($this->once())->method('setData')->willReturnSelf();
        $historyMock->expects($this->once())->method('save')->willReturnSelf();

        $this->historyFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($historyMock);

        $this->loggerMock->expects($this->exactly(2))
            ->method('info');

        $this->consumer->process($messageMock);
    }

    public function testProcessFailure()
    {
        $messageMock = $this->createMock(TranslationMessageInterface::class);
        $messageMock->method('getEntityId')->willReturn(1);
        $messageMock->method('getStoreId')->willReturn(2);
        $messageMock->method('getForce')->willReturn(false);

        $exception = new \Exception('Translation failed');

        $this->translationManagementMock->expects($this->once())
            ->method('translateProduct')
            ->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('info');

        $this->loggerMock->expects($this->once())
            ->method('error');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Translation failed');

        $this->consumer->process($messageMock);
    }
}




