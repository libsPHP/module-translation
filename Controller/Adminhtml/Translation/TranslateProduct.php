<?php
namespace NativeMind\Translation\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use NativeMind\Translation\Api\TranslationManagementInterface;

class TranslateProduct extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'NativeMind_Translation::translate_products';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var TranslationManagementInterface
     */
    protected $translationManagement;

    /**
     * TranslateProduct constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param TranslationManagementInterface $translationManagement
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        TranslationManagementInterface $translationManagement
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->translationManagement = $translationManagement;
    }

    /**
     * Translate product via AJAX
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        
        try {
            $productId = (int)$this->getRequest()->getParam('product_id');
            $storeId = (int)$this->getRequest()->getParam('store_id');
            $force = (bool)$this->getRequest()->getParam('force', false);

            if (!$productId || !$storeId) {
                throw new \InvalidArgumentException(__('Product ID and Store ID are required.'));
            }

            $result = $this->translationManagement->translateProduct($productId, $storeId, $force);

            $resultJson->setData([
                'success' => true,
                'message' => __('Product translated successfully.'),
                'data' => [
                    'translation_id' => $result->getTranslationId(),
                    'status' => $result->getStatus(),
                    'translated_text' => $result->getTranslatedText(),
                    'confidence' => $result->getConfidence()
                ]
            ]);

        } catch (\Exception $e) {
            $resultJson->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        return $resultJson;
    }
}
