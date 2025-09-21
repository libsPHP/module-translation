<?php
namespace NativeMind\Translation\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Dashboard extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'NativeMind_Translation::translation';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Dashboard constructor.
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
     * Dashboard action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('NativeMind_Translation::dashboard');
        $resultPage->getConfig()->getTitle()->prepend(__('Translation Dashboard'));
        
        return $resultPage;
    }
}
