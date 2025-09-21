<?php
namespace NativeMind\Translation\Block\Adminhtml\Translation;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use NativeMind\Translation\Api\TranslationManagementInterface;
use Magento\Store\Model\StoreManagerInterface;

class Dashboard extends Template
{
    /**
     * @var TranslationManagementInterface
     */
    protected $translationManagement;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Dashboard constructor.
     * @param Context $context
     * @param TranslationManagementInterface $translationManagement
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        TranslationManagementInterface $translationManagement,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->translationManagement = $translationManagement;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get translation statistics for all stores
     *
     * @return array
     */
    public function getTranslationStats()
    {
        $stats = [];
        $stores = $this->storeManager->getStores();
        
        foreach ($stores as $store) {
            if ($store->getId() == 0) continue; // Skip admin store
            
            try {
                $storeStats = $this->translationManagement->getTranslationStats($store->getId());
                $stats[] = [
                    'store' => $store,
                    'stats' => $storeStats
                ];
            } catch (\Exception $e) {
                $this->_logger->error('Failed to get stats for store ' . $store->getId() . ': ' . $e->getMessage());
            }
        }
        
        return $stats;
    }

    /**
     * Get overall translation statistics
     *
     * @return \NativeMind\Translation\Api\Data\TranslationStatsInterface
     */
    public function getOverallStats()
    {
        return $this->translationManagement->getTranslationStats();
    }

    /**
     * Get translate products URL
     *
     * @return string
     */
    public function getTranslateProductsUrl()
    {
        return $this->getUrl('nativelang/translation/products');
    }

    /**
     * Get translate categories URL
     *
     * @return string
     */
    public function getTranslateCategoriesUrl()
    {
        return $this->getUrl('nativelang/translation/categories');
    }

    /**
     * Get settings URL
     *
     * @return string
     */
    public function getSettingsUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit/section/nativelang');
    }

    /**
     * Format percentage
     *
     * @param int $part
     * @param int $total
     * @return string
     */
    public function formatPercentage($part, $total)
    {
        if ($total == 0) {
            return '0%';
        }
        
        $percentage = round(($part / $total) * 100, 1);
        return $percentage . '%';
    }
}
