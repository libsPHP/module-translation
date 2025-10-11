<?php
namespace NativeMind\Translation\Model\ResourceModel\ApiUsageTracking;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'tracking_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \NativeMind\Translation\Model\ApiUsageTracking::class,
            \NativeMind\Translation\Model\ResourceModel\ApiUsageTracking::class
        );
    }
}




