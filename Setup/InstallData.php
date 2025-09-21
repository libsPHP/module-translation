<?php
namespace NativeMind\Translation\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        // Add translated name attribute
        $eavSetup->addAttribute(
            Product::ENTITY,
            'name_translated',
            [
                'type' => 'text',
                'label' => 'Translated Name',
                'input' => 'text',
                'required' => false,
                'sort_order' => 100,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Product Details',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_in_advanced_search' => false,
                'used_for_promo_rules' => false,
                'html_allowed_on_front' => false,
                'used_for_sort_by' => false
            ]
        );

        // Add translated description attribute
        $eavSetup->addAttribute(
            Product::ENTITY,
            'description_translated',
            [
                'type' => 'text',
                'label' => 'Translated Description',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 101,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Product Details',
                'wysiwyg_enabled' => true,
                'is_html_allowed_on_front' => true,
                'used_in_product_listing' => false,
                'visible_on_front' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_in_advanced_search' => false,
                'used_for_promo_rules' => false,
                'html_allowed_on_front' => true,
                'used_for_sort_by' => false
            ]
        );

        // Add translated short description attribute
        $eavSetup->addAttribute(
            Product::ENTITY,
            'short_description_translated',
            [
                'type' => 'text',
                'label' => 'Translated Short Description',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 102,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Product Details',
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => true,
                'used_in_product_listing' => false,
                'visible_on_front' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_in_advanced_search' => false,
                'used_for_promo_rules' => false,
                'html_allowed_on_front' => true,
                'used_for_sort_by' => false
            ]
        );

        $setup->endSetup();
    }
}
