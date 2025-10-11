<?php
namespace NativeMind\Translation\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TranslationLog extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('nativemind_translation_logs', 'log_id');
    }
}

