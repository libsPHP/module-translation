<?php
namespace NativeMind\Translation\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Products extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'NativeMind_Translation::translate_products';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Products constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Products translation action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NativeMind_Translation::products');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Translation'));
        
        return $resultPage;
    }
}
