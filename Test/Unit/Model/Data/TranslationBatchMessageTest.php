<?php
namespace NativeMind\Translation\Test\Unit\Model\Data;

use PHPUnit\Framework\TestCase;
use NativeMind\Translation\Model\Data\TranslationBatchMessage;

/**
 * @covers \NativeMind\Translation\Model\Data\TranslationBatchMessage
 */
class TranslationBatchMessageTest extends TestCase
{
    /**
     * @var TranslationBatchMessage
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = new TranslationBatchMessage();
    }

    public function testGetSetBatchId()
    {
        $batchId = 'batch_12345';
        $this->model->setBatchId($batchId);
        
        $this->assertEquals($batchId, $this->model->getBatchId());
    }

    public function testGetSetEntityType()
    {
        $entityType = 'product';
        $this->model->setEntityType($entityType);
        
        $this->assertEquals($entityType, $this->model->getEntityType());
    }

    public function testGetSetEntityIds()
    {
        $entityIds = [1, 2, 3, 4, 5];
        $this->model->setEntityIds($entityIds);
        
        $this->assertEquals($entityIds, $this->model->getEntityIds());
    }

    public function testGetSetStoreIds()
    {
        $storeIds = [1, 2, 3];
        $this->model->setStoreIds($storeIds);
        
        $this->assertEquals($storeIds, $this->model->getStoreIds());
    }

    public function testGetSetForce()
    {
        $this->model->setForce(true);
        $this->assertTrue($this->model->getForce());

        $this->model->setForce(false);
        $this->assertFalse($this->model->getForce());
    }

    public function testGetEntityIdsReturnsArray()
    {
        // Test when no data set
        $this->assertIsArray($this->model->getEntityIds());
        $this->assertEmpty($this->model->getEntityIds());
    }

    public function testGetStoreIdsReturnsArray()
    {
        // Test when no data set
        $this->assertIsArray($this->model->getStoreIds());
        $this->assertEmpty($this->model->getStoreIds());
    }

    public function testChainableSetter()
    {
        $result = $this->model->setBatchId('test_batch')
            ->setEntityType('category')
            ->setEntityIds([10, 20])
            ->setStoreIds([1, 2])
            ->setForce(true);

        $this->assertSame($this->model, $result);
        $this->assertEquals('test_batch', $this->model->getBatchId());
        $this->assertEquals('category', $this->model->getEntityType());
        $this->assertEquals([10, 20], $this->model->getEntityIds());
        $this->assertEquals([1, 2], $this->model->getStoreIds());
        $this->assertTrue($this->model->getForce());
    }
}

