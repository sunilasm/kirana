<?php



namespace Retailinsights\Cartrules\Setup;



use Magento\Framework\Setup\UpgradeDataInterface;

use Magento\Framework\Setup\ModuleContextInterface;

use Magento\Framework\Setup\ModuleDataSetupInterface;

use Magento\Catalog\Setup\CategorySetupFactory;



class UpgradeData

    implements UpgradeDataInterface

{

    /**

     * Category setup factory

     *

     * @var CategorySetupFactory

     */

    private $categorySetupFactory;



    /**

     * Init

     *

     * @param CategorySetupFactory $categorySetupFactory

     */

    public function __construct(

        CategorySetupFactory $categorySetupFactory

    ) {

        $this->categorySetupFactory = $categorySetupFactory;

    }



    public function upgrade(

        ModuleDataSetupInterface $setup,

        ModuleContextInterface $context

    ) {

        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.4', '<')) {

            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);



            $categorySetup->addAttribute(

                \Magento\Catalog\Model\Product::ENTITY,



                'unitm',

                

                [

                

                'group' => 'General',

                

                'type' => 'int',

                

                'backend' => '',

                

                'frontend' => '',

                

                'label' => 'UOM',



                'input' => 'select',

                

                'class' => '',

                

                'source' => 'Retailinsights\Cartrules\Model\Config\Source\Options',

                

                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,

                

                'visible' => true,

                

                'required' => true,

                

                'user_defined' => false,

                

                'default' => 0,

                

                'searchable' => false,

                

                'filterable' => false,

                

                'comparable' => false,

                

                'visible_on_front' => false,

                

                'used_in_product_listing' => true,

                

                'unique' => false

                

                ]

            

            );

        }

        if (version_compare( $context->getVersion(), '2.0.5' ) < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'uom_label',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'UOM Label',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'default' => 'kg',
                    'required' => false,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => true,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );

        }

    }   

}