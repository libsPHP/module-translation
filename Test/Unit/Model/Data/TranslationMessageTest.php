<?php
namespace NativeMind\Translation\Test\Unit\Model\Data;

use PHPUnit\Framework\TestCase;
use NativeMind\Translation\Model\Data\TranslationMessage;

/**
 * @covers \NativeMind\Translation\Model\Data\TranslationMessage
 */
class TranslationMessageTest extends TestCase
{
    /**
     * @var TranslationMessage
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = new TranslationMessage();
    }

    public function testGetSetEntityType()
    {
        $entityType = 'product';
        $this->model->setEntityType($entityType);
        
        $this->assertEquals($entityType, $this->model->getEntityType());
    }

    public function testGetSetEntityId()
    {
        $entityId = 123;
        $this->model->setEntityId($entityId);
        
        $this->assertEquals($entityId, $this->model->getEntityId());
    }

    public function testGetSetStoreId()
    {
        $storeId = 2;
        $this->model->setStoreId($storeId);
        
        $this->assertEquals($storeId, $this->model->getStoreId());
    }

    public function testGetSetForce()
    {
        $this->model->setForce(true);
        $this->assertTrue($this->model->getForce());

        $this->model->setForce(false);
        $this->assertFalse($this->model->getForce());
    }

    public function testGetForceCastsToBool()
    {
        $this->model->setForce(1);
        $this->assertTrue($this->model->getForce());

        $this->model->setForce(0);
        $this->assertFalse($this->model->getForce());

        $this->model->setForce('yes');
        $this->assertTrue($this->model->getForce());
    }

    public function testChainableSetter()
    {
        $result = $this->model->setEntityType('product')
            ->setEntityId(100)
            ->setStoreId(1)
            ->setForce(true);

        $this->assertSame($this->model, $result);
        $this->assertEquals('product', $this->model->getEntityType());
        $this->assertEquals(100, $this->model->getEntityId());
        $this->assertEquals(1, $this->model->getStoreId());
        $this->assertTrue($this->model->getForce());
    }
}




