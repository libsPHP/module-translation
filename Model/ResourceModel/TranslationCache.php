<?php
namespace NativeMind\Translation\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TranslationCache extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('nativemind_translation_cache', 'cache_id');
    }

    /**
     * Clean expired cache entries
     *
     * @return int Number of deleted records
     */
    public function cleanExpiredCache()
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();
        
        return $connection->delete(
            $table,
            ['expires_at < ?' => date('Y-m-d H:i:s')]
        );
    }

    /**
     * Clean cache by service
     *
     * @param string $service
     * @return int Number of deleted records
     */
    public function cleanByService($service)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();
        
        return $connection->delete(
            $table,
            ['translation_service = ?' => $service]
        );
    }
}

