<?php
namespace NativeMind\Translation\Model\ResourceModel\TranslationLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'log_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \NativeMind\Translation\Model\TranslationLog::class,
            \NativeMind\Translation\Model\ResourceModel\TranslationLog::class
        );
    }
}




