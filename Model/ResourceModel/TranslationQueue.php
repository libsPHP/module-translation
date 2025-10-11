<?php
namespace NativeMind\Translation\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TranslationQueue extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('nativemind_translation_queue', 'queue_id');
    }

    /**
     * Get next pending items
     *
     * @param int $limit
     * @return array
     */
    public function getNextPendingItems($limit = 10)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('status IN (?)', ['pending', 'retry'])
            ->where('scheduled_at IS NULL OR scheduled_at <= ?', date('Y-m-d H:i:s'))
            ->order(['priority DESC', 'created_at ASC'])
            ->limit($limit);

        return $connection->fetchAll($select);
    }

    /**
     * Clean completed items older than N days
     *
     * @param int $days
     * @return int Number of deleted records
     */
    public function cleanCompletedItems($days = 30)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();
        
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $connection->delete(
            $table,
            [
                'status = ?' => 'completed',
                'completed_at < ?' => $date
            ]
        );
    }
}

