<?php
namespace NativeMind\Translation\Block\Adminhtml\Translation;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use NativeMind\Translation\Api\TranslationManagementInterface;
use NativeMind\Translation\Helper\Data as TranslationHelper;

class Stats extends Template
{
    /**
     * @var TranslationManagementInterface
     */
    protected $translationManagement;

    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * Stats constructor.
     * @param Context $context
     * @param TranslationManagementInterface $translationManagement
     * @param TranslationHelper $translationHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        TranslationManagementInterface $translationManagement,
        TranslationHelper $translationHelper,
        array $data = []
    ) {
        $this->translationManagement = $translationManagement;
        $this->translationHelper = $translationHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get quick stats
     *
     * @return array
     */
    public function getQuickStats()
    {
        $stats = $this->translationManagement->getTranslationStats();
        
        return [
            'total_products' => $stats->getTotalProducts(),
            'translated_products' => $stats->getTranslatedProducts(),
            'pending_products' => $stats->getPendingProducts(),
            'error_products' => $stats->getErrorProducts(),
            'translation_enabled' => $this->translationHelper->isTranslationEnabled(),
            'translation_service' => $this->translationHelper->getTranslationService()
        ];
    }

    /**
     * Check if translation is configured
     *
     * @return bool
     */
    public function isTranslationConfigured()
    {
        $service = $this->translationHelper->getTranslationService();
        
        switch ($service) {
            case 'google':
                return !empty($this->translationHelper->getGoogleApiKey());
            case 'openai':
                return !empty($this->translationHelper->getOpenAiApiKey());
            default:
                return false;
        }
    }

    /**
     * Get configuration issues
     *
     * @return array
     */
    public function getConfigurationIssues()
    {
        $issues = [];
        
        if (!$this->translationHelper->isTranslationEnabled()) {
            $issues[] = __('Translation is disabled. Enable it in settings.');
        }
        
        if (!$this->isTranslationConfigured()) {
            $service = $this->translationHelper->getTranslationService();
            $issues[] = __('API key for %1 is not configured.', ucfirst($service));
        }
        
        return $issues;
    }
}
