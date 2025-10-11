<?php
namespace NativeMind\Translation\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ApiUsageTracking extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('nativemind_api_usage_tracking', 'tracking_id');
    }

    /**
     * Get usage statistics for period
     *
     * @param string $service
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getUsageStats($service = null, $startDate = null, $endDate = null)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getMainTable(),
                [
                    'total_calls' => 'COUNT(*)',
                    'successful_calls' => 'SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END)',
                    'failed_calls' => 'SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END)',
                    'total_characters' => 'SUM(character_count)',
                    'total_cost' => 'SUM(cost_estimate)',
                    'avg_response_time' => 'AVG(response_time)'
                ]
            );

        if ($service) {
            $select->where('service = ?', $service);
        }

        if ($startDate) {
            $select->where('created_at >= ?', $startDate);
        }

        if ($endDate) {
            $select->where('created_at <= ?', $endDate);
        }

        return $connection->fetchRow($select);
    }

    /**
     * Get daily usage statistics
     *
     * @param string $service
     * @param int $days
     * @return array
     */
    public function getDailyUsageStats($service = null, $days = 30)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getMainTable(),
                [
                    'date' => 'DATE(created_at)',
                    'total_calls' => 'COUNT(*)',
                    'successful_calls' => 'SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END)',
                    'total_characters' => 'SUM(character_count)',
                    'total_cost' => 'SUM(cost_estimate)'
                ]
            )
            ->where('created_at >= ?', date('Y-m-d H:i:s', strtotime("-{$days} days")))
            ->group('DATE(created_at)')
            ->order('date DESC');

        if ($service) {
            $select->where('service = ?', $service);
        }

        return $connection->fetchAll($select);
    }
}

