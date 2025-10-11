<?php
namespace NativeMind\Translation\Model\Data;

use NativeMind\Translation\Api\Data\TranslationMessageInterface;
use Magento\Framework\DataObject;

class TranslationMessage extends DataObject implements TranslationMessageInterface
{
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
    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
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

