<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Product;

class InstallData implements InstallDataInterface {
    protected $groupFactory;
    private $categorySetupFactory;
    
    /**
     *
     * @param GroupFactory $groupFactory            
     */
    public function __construct(GroupFactory $groupFactory, CategorySetupFactory $categorySetupFactory) {
        $this->groupFactory = $groupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }
    /**
     * (non-PHPdoc)
     * 
     * @see \Lof\MarketPlace\Setup\InstallData::install()
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
         $installer = $setup;

        /** @var CustomerSetup $customerSetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $setup->startSetup();


        $categorySetup->addAttribute(
            Product::ENTITY,
            'seller_id',
            [
                'group' => 'Product Details',
                'label' => 'Seller Id',
                'type' => 'static',
                'input' => 'text',
                'position' => 145,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => true,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true
            ]
        );
        $categorySetup->addAttribute(
                Product::ENTITY,
                'approval',
                [
                    'group' => 'Product Details',
                    'label' => 'Approval',
                    'type' => 'static',
                    'input' => 'select',
                    'position' => 160,
                    'visible' => true,
                    'default' => '',
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'source' => 'Lof\MarketPlace\Model\Source\Approval',
                    'default' => '',
                    'visible_on_front' => false,
                    'unique' => false,
                    'is_configurable' => false,
                    'used_for_promo_rules' => false,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'is_filterable_in_grid' => true,
                    'used_in_product_listing' => true
                ]
            );
            
            $setup->endSetup();
        
    }
}
