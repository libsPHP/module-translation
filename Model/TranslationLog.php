<?php
namespace NativeMind\Translation\Model;

use Magento\Framework\Model\AbstractModel;

class TranslationLog extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\NativeMind\Translation\Model\ResourceModel\TranslationLog::class);
    }

    /**
     * Get log ID
     *
     * @return int|null
     */
    public function getLogId()
    {
        return $this->getData('log_id');
    }

    /**
     * Get log level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->getData('level');
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->getData('message');
    }

    /**
     * Get context
     *
     * @return string|null
     */
    public function getContext()
    {
        return $this->getData('context');
    }

    /**
     * Get entity type
     *
     * @return string|null
     */
    public function getEntityType()
    {
        return $this->getData('entity_type');
    }

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    /**
     * Get operation
     *
     * @return string|null
     */
    public function getOperation()
    {
        return $this->getData('operation');
    }
}

