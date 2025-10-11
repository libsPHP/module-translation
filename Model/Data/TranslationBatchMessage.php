<?php
namespace NativeMind\Translation\Model\Data;

use NativeMind\Translation\Api\Data\TranslationBatchMessageInterface;
use Magento\Framework\DataObject;

class TranslationBatchMessage extends DataObject implements TranslationBatchMessageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBatchId()
    {
        return $this->getData('batch_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setBatchId($batchId)
    {
        return $this->setData('batch_id', $batchId);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType()
    {
        return $this->getData('entity_type');
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityType($entityType)
    {
        return $this->setData('entity_type', $entityType);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityIds()
    {
        return (array)$this->getData('entity_ids');
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityIds(array $entityIds)
    {
        return $this->setData('entity_ids', $entityIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return (array)$this->getData('store_ids');
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds(array $storeIds)
    {
        return $this->setData('store_ids', $storeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getForce()
    {
        return (bool)$this->getData('force');
    }

    /**
     * {@inheritdoc}
     */
    public function setForce($force)
    {
        return $this->setData('force', (bool)$force);
    }
}




