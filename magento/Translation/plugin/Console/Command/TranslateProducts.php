<?php
namespace NativeMind\Translation\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Magento\Framework\App\State;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use NativeMind\Translation\Helper\Data as TranslationHelper;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\LocalizedException;

class TranslateProducts extends Command
{
    /**
     * @var State
     */
    protected $appState;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * TranslateProducts constructor.
     * @param State $appState
     * @param ProductCollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param TranslationHelper $translationHelper
     * @param ProductRepository $productRepository
     */
    public function __construct(
        State $appState,
        ProductCollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        TranslationHelper $translationHelper,
        ProductRepository $productRepository
    ) {
        $this->appState = $appState;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->translationHelper = $translationHelper;
        $this->productRepository = $productRepository;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('nativemind:translate:products')
            ->setDescription('Translate products for specified stores')
            ->addOption(
                'store-ids',
                's',
                InputOption::VALUE_OPTIONAL,
                'Comma-separated list of store IDs (default: all stores)'
            )
            ->addOption(
                'product-ids',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Comma-separated list of product IDs (default: all products)'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force re-translation of already translated products'
            )
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Limit number of products to process',
                100
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
        $productIds = $input->getOption('product-ids');
        $force = $input->getOption('force');
        $limit = (int)$input->getOption('limit');

        if ($storeIds) {
            $storeIds = array_map('intval', explode(',', $storeIds));
        } else {
            $storeIds = array_keys($this->storeManager->getStores());
        }

        $output->writeln('<info>Starting product translation process...</info>');
        $output->writeln('<info>Store IDs: ' . implode(', ', $storeIds) . '</info>');

        $totalTranslated = 0;
        $totalErrors = 0;

        foreach ($storeIds as $storeId) {
            if ($storeId == 0) continue; // Skip admin store

            $store = $this->storeManager->getStore($storeId);
            $locale = $this->translationHelper->getStoreLocale($storeId);
            
            $output->writeln("<info>Processing store: {$store->getName()} (ID: {$storeId}, Locale: {$locale})</info>");

            $productCollection = $this->productCollectionFactory->create()
                ->addStoreFilter($storeId)
                ->addAttributeToSelect(['name', 'description', 'short_description', 'name_translated', 'description_translated', 'short_description_translated']);

            if ($productIds) {
                $productIdsArray = array_map('intval', explode(',', $productIds));
                $productCollection->addFieldToFilter('entity_id', ['in' => $productIdsArray]);
            }

            if (!$force) {
                // Only translate products that haven't been translated yet
                $productCollection->addAttributeToFilter([
                    ['attribute' => 'name_translated', 'null' => true],
                    ['attribute' => 'name_translated', 'eq' => '']
                ]);
            }

            $productCollection->setPageSize($limit);

            foreach ($productCollection as $product) {
                $output->write("Translating product ID {$product->getId()}: {$product->getName()}... ");
                
                try {
                    $this->translateProduct($product, $storeId, $locale);
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
     * Translate a single product
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @param int $storeId
     * @param string $locale
     * @throws \Exception
     */
    private function translateProduct($product, $storeId, $locale)
    {
        $product->setStoreId($storeId);

        // Translate name
        if ($product->getName() && !$product->getNameTranslated()) {
            $translatedName = $this->translationHelper->translateText($product->getName(), $locale, $storeId);
            $product->setNameTranslated($translatedName);
        }

        // Translate description
        if ($product->getDescription() && !$product->getDescriptionTranslated()) {
            $translatedDescription = $this->translationHelper->translateText($product->getDescription(), $locale, $storeId);
            $product->setDescriptionTranslated($translatedDescription);
        }

        // Translate short description
        if ($product->getShortDescription() && !$product->getShortDescriptionTranslated()) {
            $translatedShortDescription = $this->translationHelper->translateText($product->getShortDescription(), $locale, $storeId);
            $product->setShortDescriptionTranslated($translatedShortDescription);
        }

        // Set translation status and date
        $product->setTranslationStatus('translated');
        $product->setLastTranslationDate(date('Y-m-d H:i:s'));

        $this->productRepository->save($product);
    }
}
