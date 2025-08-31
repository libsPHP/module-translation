<?php
namespace NativeMind\Translation\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use NativeMind\Translation\Helper\Data as TranslationHelper;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\LocalizedException;

class TranslateCategories extends Command
{
    /**
     * @var State
     */
    protected $appState;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * TranslateCategories constructor.
     * @param State $appState
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param TranslationHelper $translationHelper
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        State $appState,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        TranslationHelper $translationHelper,
        CategoryRepository $categoryRepository
    ) {
        $this->appState = $appState;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->translationHelper = $translationHelper;
        $this->categoryRepository = $categoryRepository;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('nativemind:translate:categories')
            ->setDescription('Translate categories for specified stores')
            ->addOption(
                'store-ids',
                's',
                InputOption::VALUE_OPTIONAL,
                'Comma-separated list of store IDs (default: all stores)'
            )
            ->addOption(
                'category-ids',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Comma-separated list of category IDs (default: all categories)'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force re-translation of already translated categories'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        } catch (LocalizedException $e) {
            // Area code already set
        }

        $storeIds = $input->getOption('store-ids');
        $categoryIds = $input->getOption('category-ids');
        $force = $input->getOption('force');

        if ($storeIds) {
            $storeIds = array_map('intval', explode(',', $storeIds));
        } else {
            $storeIds = array_keys($this->storeManager->getStores());
        }

        $output->writeln('<info>Starting category translation process...</info>');
        $output->writeln('<info>Store IDs: ' . implode(', ', $storeIds) . '</info>');

        $totalTranslated = 0;
        $totalErrors = 0;

        foreach ($storeIds as $storeId) {
            if ($storeId == 0) continue; // Skip admin store

            $store = $this->storeManager->getStore($storeId);
            $locale = $this->translationHelper->getStoreLocale($storeId);
            
            $output->writeln("<info>Processing store: {$store->getName()} (ID: {$storeId}, Locale: {$locale})</info>");

            $categoryCollection = $this->categoryCollectionFactory->create()
                ->addAttributeToSelect(['name', 'description'])
                ->setStoreId($storeId)
                ->addIsActiveFilter();

            if ($categoryIds) {
                $categoryIdsArray = array_map('intval', explode(',', $categoryIds));
                $categoryCollection->addFieldToFilter('entity_id', ['in' => $categoryIdsArray]);
            }

            // Skip root categories
            $categoryCollection->addFieldToFilter('level', ['gt' => 1]);

            foreach ($categoryCollection as $category) {
                $output->write("Translating category ID {$category->getId()}: {$category->getName()}... ");
                
                try {
                    $this->translateCategory($category, $storeId, $locale);
                    $output->writeln('<info>✓</info>');
                    $totalTranslated++;
                } catch (\Exception $e) {
                    $output->writeln('<error>✗ Error: ' . $e->getMessage() . '</error>');
                    $totalErrors++;
                }

                // Small delay to avoid API rate limits
                usleep(100000); // 0.1 second
            }
        }

        $output->writeln("<info>Translation completed!</info>");
        $output->writeln("<info>Total translated: {$totalTranslated}</info>");
        $output->writeln("<error>Total errors: {$totalErrors}</error>");

        return 0;
    }

    /**
     * Translate a single category
     * 
     * @param \Magento\Catalog\Model\Category $category
     * @param int $storeId
     * @param string $locale
     * @throws \Exception
     */
    private function translateCategory($category, $storeId, $locale)
    {
        $category->setStoreId($storeId);

        // Get original values from default store
        $originalCategory = $this->categoryRepository->get($category->getId(), 0);

        // Translate name
        if ($originalCategory->getName()) {
            $translatedName = $this->translationHelper->translateText($originalCategory->getName(), $locale, $storeId);
            $category->setName($translatedName);
        }

        // Translate description
        if ($originalCategory->getDescription()) {
            $translatedDescription = $this->translationHelper->translateText($originalCategory->getDescription(), $locale, $storeId);
            $category->setDescription($translatedDescription);
        }

        $this->categoryRepository->save($category);
    }
}
