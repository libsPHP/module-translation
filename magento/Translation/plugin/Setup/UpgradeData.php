<?php
namespace NativeMind\Translation\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            // Add translation status attribute
            $eavSetup->addAttribute(
                Product::ENTITY,
                'translation_status',
                [
                    'type' => 'varchar',
                    'label' => 'Translation Status',
                    'input' => 'select',
                    'source' => 'NativeMind\Translation\Model\Config\Source\TranslationStatus',
                    'required' => false,
                    'sort_order' => 103,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Product Details',
                    'used_in_product_listing' => false,
                    'visible_on_front' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => true,
                    'comparable' => false,
                    'visible_in_advanced_search' => false,
                    'used_for_promo_rules' => false,
                    'html_allowed_on_front' => false,
                    'used_for_sort_by' => false,
                    'default' => 'pending'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            // Add last translation date attribute
            $eavSetup->addAttribute(
                Product::ENTITY,
                'last_translation_date',
                [
                    'type' => 'datetime',
                    'label' => 'Last Translation Date',
                    'input' => 'date',
                    'required' => false,
                    'sort_order' => 104,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Product Details',
                    'used_in_product_listing' => false,
                    'visible_on_front' => false,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_in_advanced_search' => false,
                    'used_for_promo_rules' => false,
                    'html_allowed_on_front' => false,
                    'used_for_sort_by' => false
                ]
            );
        }

        $setup->endSetup();
    }
}
