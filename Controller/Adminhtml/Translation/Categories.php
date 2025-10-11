<?php
namespace NativeMind\Translation\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Categories extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'NativeMind_Translation::translate_categories';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Categories constructor.
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
     * Categories translation action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NativeMind_Translation::categories');
        $resultPage->getConfig()->getTitle()->prepend(__('Category Translation'));
        
        return $resultPage;
    }
}

