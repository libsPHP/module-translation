<?php
namespace NativeMind\Translation\Model\ResourceModel\TranslationCache;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'cache_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \NativeMind\Translation\Model\TranslationCache::class,
            \NativeMind\Translation\Model\ResourceModel\TranslationCache::class
        );
    }
}

