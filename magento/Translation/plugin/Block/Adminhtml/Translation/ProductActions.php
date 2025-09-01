<?php
namespace NativeMind\Translation\Block\Adminhtml\Translation;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class ProductActions extends Template
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * ProductActions constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get available stores
     *
     * @return array
     */
    public function getStores()
    {
        $stores = [];
        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getId() == 0) continue; // Skip admin store
            
            $stores[] = [
                'value' => $store->getId(),
                'label' => $store->getName() . ' (' . $store->getCode() . ')'
            ];
        }
        return $stores;
    }

    /**
     * Get bulk translate URL
     *
     * @return string
     */
    public function getBulkTranslateUrl()
    {
        return $this->getUrl('nativelang/translation/bulkTranslate');
    }

    /**
     * Get translate single product URL
     *
     * @return string
     */
    public function getTranslateProductUrl()
    {
        return $this->getUrl('nativelang/translation/translateProduct');
    }

    /**
     * Get check status URL
     *
     * @return string
     */
    public function getCheckStatusUrl()
    {
        return $this->getUrl('nativelang/translation/checkStatus');
    }
}
