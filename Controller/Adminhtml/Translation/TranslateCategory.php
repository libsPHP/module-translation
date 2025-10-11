<?php
namespace NativeMind\Translation\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\CategoryRepository;
use NativeMind\Translation\Helper\Data as TranslationHelper;
use Psr\Log\LoggerInterface;

class TranslateCategory extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'NativeMind_Translation::translate_categories';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * TranslateCategory constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CategoryRepository $categoryRepository
     * @param TranslationHelper $translationHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CategoryRepository $categoryRepository,
        TranslationHelper $translationHelper,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->categoryRepository = $categoryRepository;
        $this->translationHelper = $translationHelper;
        $this->logger = $logger;
    }

    /**
     * Translate category via AJAX
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        
        try {
            $categoryId = (int)$this->getRequest()->getParam('category_id');
            $storeId = (int)$this->getRequest()->getParam('store_id');
            $force = (bool)$this->getRequest()->getParam('force', false);

            if (!$categoryId || !$storeId) {
                throw new \InvalidArgumentException(__('Category ID and Store ID are required.'));
            }

            // Load category for default store to get original values
            $originalCategory = $this->categoryRepository->get($categoryId, 0);
            
            // Load category for target store
            $category = $this->categoryRepository->get($categoryId, $storeId);
            $locale = $this->translationHelper->getStoreLocale($storeId);

            $translations = [];

            // Translate name
            if ($originalCategory->getName() && ($force || !$category->getData('name'))) {
                $translatedName = $this->translationHelper->translateText($originalCategory->getName(), $locale, $storeId);
                $category->setName($translatedName);
                $translations['name'] = $translatedName;
            }

            // Translate description
            if ($originalCategory->getDescription() && ($force || !$category->getData('description'))) {
                $translatedDescription = $this->translationHelper->translateText($originalCategory->getDescription(), $locale, $storeId);
                $category->setDescription($translatedDescription);
                $translations['description'] = $translatedDescription;
            }

            // Save category
            $this->categoryRepository->save($category);

            $resultJson->setData([
                'success' => true,
                'message' => __('Category translated successfully.'),
                'data' => [
                    'category_id' => $categoryId,
                    'store_id' => $storeId,
                    'translations' => $translations
                ]
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Category translation error: ' . $e->getMessage());
            $resultJson->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        return $resultJson;
    }
}

