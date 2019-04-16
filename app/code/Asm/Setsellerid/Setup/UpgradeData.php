<?php
namespace Asm\Setsellerid\Setup;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
class UpgradeData
    implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $eavSetupFactory;
    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
       $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), '1.0.1') < 0){

                                
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $salesSetup = $objectManager->create('Magento\Sales\Setup\SalesSetup');
                
                $salesSetup->addAttribute('order_item', 'price_type', ['type' =>'varchar']);
                $quoteSetup = $objectManager->create('Magento\Quote\Setup\QuoteSetup');
                
                $quoteSetup->addAttribute('quote_item', 'price_type', ['type' =>'varchar']);
                

        }
        if (version_compare($context->getVersion(), '1.0.2') < 0){
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'price_type',
            [
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Price Type',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );

    }
            if (version_compare($context->getVersion(), '1.0.3') < 0){

                                
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $salesSetup = $objectManager->create('Magento\Sales\Setup\SalesSetup');
                $quoteSetup = $objectManager->create('Magento\Quote\Setup\QuoteSetup');
                
                $salesSetup->addAttribute('order_item', 'seller_kirana_id', ['type' =>'varchar']);
                $quoteSetup->addAttribute('quote_item', 'seller_kirana_id', ['type' =>'varchar']);

                $salesSetup->addAttribute('order_item', 'kirana_qty', ['type' =>'varchar']);
                $quoteSetup->addAttribute('quote_item', 'kirana_qty', ['type' =>'varchar']);

                $salesSetup->addAttribute('order_item', 'seller_org_store_id', ['type' =>'varchar']);
                $quoteSetup->addAttribute('quote_item', 'seller_org_store_id', ['type' =>'varchar']);

                $salesSetup->addAttribute('order_item', 'org_store_qty', ['type' =>'varchar']);
                $quoteSetup->addAttribute('quote_item', 'org_store_qty', ['type' =>'varchar']);

        }

    }   
}