<?php
namespace NativeMind\Translation\Model\ResourceModel\TranslationQueue;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'queue_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \NativeMind\Translation\Model\TranslationQueue::class,
            \NativeMind\Translation\Model\ResourceModel\TranslationQueue::class
        );
    }
}




