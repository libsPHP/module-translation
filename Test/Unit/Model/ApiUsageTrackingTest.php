<?php
namespace NativeMind\Translation\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use NativeMind\Translation\Model\ApiUsageTracking;

/**
 * @covers \NativeMind\Translation\Model\ApiUsageTracking
 */
class ApiUsageTrackingTest extends TestCase
{
    /**
     * @var ApiUsageTracking|\PHPUnit\Framework\MockObject\MockObject
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = $this->getMockBuilder(ApiUsageTracking::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setData'])
            ->getMock();
    }

    public function testTrackWithAllData()
    {
        $service = 'google';
        $operationType = 'translate';
        $data = [
            'request_size' => 100,
            'response_size' => 150,
            'character_count' => 50,
            'response_time' => 200,
            'status_code' => 200,
            'success' => true,
            'error_message' => null,
            'cost_estimate' => 0.001,
            'metadata' => ['test' => 'value']
        ];

        $this->model->expects($this->exactly(11))
            ->method('setData')
            ->willReturnSelf();

        $result = $this->model->track($service, $operationType, $data);
        $this->assertSame($this->model, $result);
    }

    public function testTrackWithMinimalData()
    {
        $service = 'openai';
        $operationType = 'translate';
        $data = [];

        $this->model->expects($this->exactly(2))
            ->method('setData')
            ->withConsecutive(
                ['service', $service],
                ['operation_type', $operationType]
            )
            ->willReturnSelf();

        $result = $this->model->track($service, $operationType, $data);
        $this->assertSame($this->model, $result);
    }

    public function testCalculateCostEstimateForGoogle()
    {
        // Create real instance for this test
        $model = new ApiUsageTracking(
            $this->createMock(\Magento\Framework\Model\Context::class),
            $this->createMock(\Magento\Framework\Registry::class),
            null,
            null,
            []
        );

        $service = 'google';
        $characterCount = 1000000; // 1M characters
        
        $expectedCost = ($characterCount / 1000000) * 20; // $20 per 1M
        $actualCost = $model->calculateCostEstimate($service, $characterCount);

        $this->assertEquals($expectedCost, $actualCost);
    }

    public function testCalculateCostEstimateForOpenAI()
    {
        $model = new ApiUsageTracking(
            $this->createMock(\Magento\Framework\Model\Context::class),
            $this->createMock(\Magento\Framework\Registry::class),
            null,
            null,
            []
        );

        $service = 'openai';
        $characterCount = 4000; // 1K tokens (~4K chars)
        
        $expectedCost = ($characterCount / 4000) * 0.002;
        $actualCost = $model->calculateCostEstimate($service, $characterCount);

        $this->assertEquals($expectedCost, $actualCost);
    }

    public function testCalculateCostEstimateForUnknownService()
    {
        $model = new ApiUsageTracking(
            $this->createMock(\Magento\Framework\Model\Context::class),
            $this->createMock(\Magento\Framework\Registry::class),
            null,
            null,
            []
        );

        $service = 'unknown';
        $characterCount = 1000;
        
        $actualCost = $model->calculateCostEstimate($service, $characterCount);

        $this->assertEquals(0, $actualCost);
    }

    public function testCalculateCostEstimateGoogleSmallText()
    {
        $model = new ApiUsageTracking(
            $this->createMock(\Magento\Framework\Model\Context::class),
            $this->createMock(\Magento\Framework\Registry::class),
            null,
            null,
            []
        );

        $service = 'google';
        $characterCount = 100;
        
        $expectedCost = ($characterCount / 1000000) * 20;
        $actualCost = $model->calculateCostEstimate($service, $characterCount);

        $this->assertEquals($expectedCost, $actualCost);
    }
}




