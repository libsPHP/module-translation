<?php
namespace NativeMind\Translation\Model\ResourceModel\TranslationHistory;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'history_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \NativeMind\Translation\Model\TranslationHistory::class,
            \NativeMind\Translation\Model\ResourceModel\TranslationHistory::class
        );
    }
}

