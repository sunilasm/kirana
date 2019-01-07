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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityTypeModel;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $_catalogAttribute;
    
    /**
     * @var \Magento\Eav\Setup\EavSetupe
     */
    protected $_eavSetup;

    /**
     * @param \Magento\Eav\Setup\EavSetup         $eavSetup         
     * @param \Magento\Eav\Model\Entity\Type      $entityType       
     * @param \Magento\Eav\Model\Entity\Attribute $catalogAttribute 
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Eav\Model\Entity\Attribute $catalogAttribute
        ) {
        $this->_eavSetup = $eavSetup;
        $this->_entityTypeModel = $entityType;
        $this->_catalogAttribute = $catalogAttribute;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $entityTypeModel = $this->_entityTypeModel;
        $catalogAttributeModel = $this->_catalogAttribute;
        $installer =  $this->_eavSetup;

        $setup->startSetup();

        /**
         * Drop table if exists
         */
        // $setup->getConnection()->dropTable($setup->getTable('lof_marketplace_group'));
        // $setup->getConnection()->dropTable($setup->getTable('lof_marketplace_seller'));
        // $setup->getConnection()->dropTable($setup->getTable('lof_marketplace_store'));

        /**
         * Create table 'lof_marketplace_group'
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('lof_marketplace_group'))
        ->addColumn(
            'group_id',
            Table::TYPE_INTEGER,
            11,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Group ID'
            )
        ->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Group Name'
            )
        ->addColumn(
            'url_key',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Group Url Key'
            )
        ->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Position'
            )
        ->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
            )
        ->addColumn(
            'shown_in_sidebar',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Show In Sidebar'
            );
        $setup->getConnection()->createTable($table);
        /*
         * Create table 'lof_marketplace_product'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_product'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Magento Product ID'
            )
            ->addColumn(
                'adminassign',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                2,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Admin Assign ID'
            )
            ->addColumn(
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Seller ID'
            )
            ->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'product name'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )
            ->addColumn(
            'position',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Position'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Status'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
            ->addColumn(
                'customer_id',
                 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                'customer_id',
                 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                'commission',
                 \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Commission'
            )
            ->addForeignKey(
                $setup->getFkName('lof_marketplace_product','product_id','catalog_product_entity', 'entity_id'),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );
        $setup->getConnection()->createTable($table);    
        /**
         * Create table 'lof_marketplace_seller'
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('lof_marketplace_seller'))
        ->addColumn(
            'seller_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Seller ID'
            )
        ->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Seller Name'
            )
        ->addColumn(
            'url_key',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Seller Url Key'
            )
        ->addColumn(
            'description',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'Seller Description'
            )
        ->addColumn(
            'group_id',
            Table::TYPE_INTEGER,
            11,
            ['unsigned' => true, 'nullable' => false],
            'Group ID'
            )
         ->addColumn(
            'sale',
            Table::TYPE_INTEGER,
            11,
            ['unsigned' => true, 'nullable' => false],
            'Sales'
            )
         ->addColumn(
            'commission_id',
            Table::TYPE_INTEGER,
            11,
            ['unsigned' => true, 'nullable' => false],
            'Commission ID'
            )
        ->addColumn(
            'image',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Seller Image'
            )
        ->addColumn(
            'thumbnail',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Seller Thumbnail'
            )
        ->addColumn(
            'page_title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Seller Page Title'
            )
        ->addColumn(
            'meta_keywords',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'Meta Keywords'
            )
        ->addColumn(
            'meta_description',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'Meta Description'
            )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Seller Creation Time'
            )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Seller Modification Time'
            )->addColumn(
            'page_layout',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Page Layout'
            )->addColumn(
            'address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Address'
            )->addColumn(
            'layout_update_xml',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Page Layout Update Content'
            )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
            )
            ->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
            )
            ->addColumn(
                'customer_id',
                 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                'email',
                 \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Email'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Creation Time'
            )
            ->addIndex(
                $setup->getIdxName('lof_marketplace_seller', ['group_id']),
                ['group_id']
                )
             ->addColumn(
                'payment_source',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Seller Payment Source'
            )
            ->addColumn(
                'twitter_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Twitter ID'
            )
            ->addColumn(
                'facebook_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Facebookid ID'
            )
            ->addColumn(
                'gplus_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Google Plus ID'
            )
            ->addColumn(
                'youtube_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Youtube ID'
            )
            ->addColumn(
                'vimeo_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Vimeo ID'
            )
            ->addColumn(
                'instagram_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Instagram ID'
            )
            ->addColumn(
                'pinterest_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Pinterest ID'
            )
            ->addColumn(
                'linkedin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Linkedin ID'
            )
            ->addColumn(
                'tw_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Twitter Active Status'
            )
            ->addColumn(
                'fb_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Facebook Active Status'
            )
            ->addColumn(
                'gplus_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Google+ Active Status'
            )
            ->addColumn(
                'youtube_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Youtube Active Status'
            )
            ->addColumn(
                'vimeo_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Vimeo Active Status'
            )
            ->addColumn(
                'instagram_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Instagram Active Status'
            )
            ->addColumn(
                'pinterest_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Pinterest Active Status'
            )
            ->addColumn(
                'linkedin_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Linkedin Active Status'
            )
            ->addColumn(
                'others_info',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Others Info'
            )
            ->addColumn(
                'banner_pic',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Banner Image'
            )
            ->addColumn(
                'contact_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Contact Number'
            )
            ->addColumn(
                'shop_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Shop Url'
            )
            ->addColumn(
                'shop_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Shop Title'
            )
            ->addColumn(
                'logo_pic',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Logo Image'
            )
            ->addColumn(
                'company_locality',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Company Locality'
            )
            ->addColumn(
                'country_pic',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Country Flag Image'
            )
             ->addColumn(
                'country',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Country'
            )
            ->addColumn(
                'company_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Company Description'
            )
            ->addColumn(
                'meta_keyword',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Meta Keyword'
            )
            ->addColumn(
                'meta_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Meta Description'
            )
            ->addColumn(
                'background_width',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Background Width'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )
            ->addColumn(
                'contact_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => true, 'default' => null],
                'Contact Number'
            )
            ->addColumn(
                'return_policy',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Return Policy'
            )
            ->addColumn(
                'shipping_policy',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Shipping Policy'
            )
            ->addForeignKey(
                $setup->getFkName('lof_marketplace_seller', 'group_id', 'lof_marketplace_seller', 'group_id'),
                'group_id',
                $setup->getTable('lof_marketplace_group'),
                'group_id',
                Table::ACTION_CASCADE
                );
            $setup->getConnection()->createTable($table);
  
        /**
         * Create table 'lof_marketplace_store'
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('lof_marketplace_store'))
        ->addColumn(
            'seller_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Seller Id'
            )
        ->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store Id'
            )
        ->addIndex(
            $setup->getIdxName('lof_marketplace_store', ['store_id']),
            ['store_id']
            )
        ->addForeignKey(
            $setup->getFkName('lof_marketplace_store', 'seller_id', 'lof_marketplace_seller', 'seller_id'),
            'seller_id',
            $setup->getTable('lof_marketplace_seller'),
            'seller_id',
            Table::ACTION_CASCADE
            )
        ->addForeignKey(
            $setup->getFkName('lof_marketplace_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
            );
        $setup->getConnection()->createTable($table);
        /**
         * Create table 'lof_marketplace_commission'
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('lof_marketplace_commission'))
        ->addColumn(
            'commission_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'commission_id'
        ) 
        ->addColumn(
            'commission_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'commission_title'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store ID'
        )
        ->addColumn(
            'group_id',
            Table::TYPE_INTEGER,
            11,
            ['unsigned' => true, 'nullable' => false],
            'Group ID'
        )
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
            ['nullable' => false,],
            'Description'
        )
        ->addColumn(
            'from_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true],
            'From Date'
        )->addColumn(
            'to_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true],
            'To Date'
        )
        ->addColumn(
            'actions_serialized',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Actions'
        )->addColumn(
            'stop_rules_processing',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Stop further rules processing'
        )->addColumn(
            'priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Prioirity'
        )->addColumn(
            'commission_by',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => true],
            'Commission By'
        )->addColumn(
            'commission_action',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => true],
            'Commission Action'
        )->addColumn(
            'commission_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => true],
            'Commission Acmount'
        )
        ->addColumn(
            'create_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'create_at'
        )
        ->addColumn(
        'is_active',
        Table::TYPE_SMALLINT,
        1,
        ['nullable' => false, 'default' => '1'],
        'Active'
        );
        $setup->getConnection()->createTable($table);
         /**
         * Create table 'lof_marketplace_commission_store'
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('lof_marketplace_commission_store'))
        ->addColumn(
            'commission_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Seller Id'
            )
        ->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store Id'
            )
        ->addIndex(
            $setup->getIdxName('lof_marketplace_commission_store', ['store_id']),
            ['store_id']
            );
        $setup->getConnection()->createTable($table);
          /**
         * Create table 'lof_marketplace_commission_group'
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('lof_marketplace_commission_group'))
        ->addColumn(
            'commission_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Commission Id'
            )
        ->addColumn(
            'group_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Group Id'
            )
        ->addIndex(
            $setup->getIdxName('lof_marketplace_commission_group', ['group_id']),
            ['group_id']
            );
        $setup->getConnection()->createTable($table);
        /**
         * Create table 'lof_marketplace_sellerorder'
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('lof_marketplace_sellerorder'))
        ->addColumn ( 
            'id', 
            Table::TYPE_INTEGER, 
            null, 
            ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 
            'Id' )
        ->addColumn ( 
            'order_id', 
            Table::TYPE_INTEGER, 
            null, 
            [ 'unsigned' => true ], 
            'Order Id' )
        ->addColumn ( 
            'seller_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['unsigned' => true ], 'Seller Id' )
        ->addColumn ( 
            'commission', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Commission' )
        ->addColumn ( 
            'seller_product_total', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Seller Product Total' )
        ->addColumn ( 
            'seller_amount', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Seller Amount' )
         ->addColumn ( 
            'discount_amount', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Discount Amount' )
        ->addColumn ( 
            'is_invoiced', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true ], 
            'Is Invoiced' )
        ->addColumn ( 
            'is_shipped', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true ], 
            'Is Shipped' )
        ->addColumn ( 
            'is_refunded', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true], 
            'Is Refunded' )
        ->addColumn ( 
            'is_returned', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true],
             'Is Returned' )
        ->addColumn ( 
            'is_canceled', 
            Table::TYPE_SMALLINT, 
            null, ['unsigned' => true ],
            'Is Canceled' )
        ->addColumn ( 
            'status', 
            Table::TYPE_TEXT, 
            255, [ ], 
            'Status' )
        ->addColumn ( 
            'increment_id', 
            Table::TYPE_TEXT, 
            255, 
            [ ], 
            'Increment Id' )
        ->addColumn ( 
            'billing_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['unsigned' => true], 
            'Billing Id' )
        ->addColumn ( 
            'shipping_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['unsigned' => true ], 
            'Shipping Id' )
        ->addColumn ( 
            'quote_id', 
            Table::TYPE_INTEGER, 
            null, 
            [ 'unsigned' => true ], 
            'Quote Id' )
        ->addColumn ( 
            'shipping_amount', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Shipping Amount' )
        ->addColumn ( 
            'order_currency_code', 
            Table::TYPE_TEXT, 
            3, 
            [ ], 
            'Currency Code' )
        ->addColumn ( 
            'customer_id', 
            Table::TYPE_INTEGER, 
            null, 
            [ 'unsigned' => true], 'Customer Id' );
        $setup->getConnection()->createTable($table);
        /**
         * Create table 'lof_marketplace_sellerorderitems'
         */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('lof_marketplace_sellerorderitems'))
        ->addColumn ( 
            'id', 
            Table::TYPE_INTEGER, 
            null, 
            ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 
            'Id' )
        ->addColumn ( 'order_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['unsigned' => true], 
            'Order Id' )
        ->addColumn ( 'seller_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['unsigned' => true], 
            'Seller Id' )
        ->addColumn ( 
            'order_item_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['unsigned' => true], 
            'Order Item Id' )
        ->addColumn ( 
            'product_id',
            Table::TYPE_INTEGER, 
            null, 
            ['unsigned' => true], 
            'Product Id' )
        ->addColumn ( 
            'product_sku', 
            Table::TYPE_TEXT, 
            255, 
            [ ], 
            'Product Sku' )
        ->addColumn ( 
            'product_qty', 
            Table::TYPE_DECIMAL, '12,4', 
            [ ], 
            'Product Qty' )
        ->addColumn ( 'product_name', 
            Table::TYPE_TEXT, 
            255, 
            [ ], 
            'Product Name' )
        ->addColumn ( 
            'options', 
            Table::TYPE_TEXT,
             255, 
             [ ], 
             'Options' )
        ->addColumn ( 
            'is_canceled', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true], 
            'Is Canceled' )
        ->addColumn ( 
            'status', 
            Table::TYPE_TEXT, 
            255, 
            [ ], 
            'Status' )
        ->addColumn ( 
            'parent_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['unsigned' => true], 
            'Parent Id' )
        ->addColumn ( 
            'quote_item_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['unsigned' => true], 
            'Quote Item Id' )
        ->addColumn ( 'quote_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['unsigned' => true], 
            'Quote Id' )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )
        ->addColumn ( 
            'qty_canceled', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Qty Canceled' )
        ->addColumn ( 
            'qty_invoiced', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Qty Invoiced' )
        ->addColumn ( 
            'qty_shipped', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Qty Shipped' )
        ->addColumn ( 
            'qty_refunded', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Qty Refunded' )
        ->addColumn ( 
            'commission', 
            Table::TYPE_DECIMAL, 
            '12,4',
             [ ], 
             'Commission' )
        ->addColumn ( 
            'product_price', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Product Price' )
        ->addColumn ( 
            'base_product_price', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ],
             'Base Product Price' )
        ->addColumn ( 
            'is_buyer_canceled', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true], 
            'Is Buyer Canceled' )
        ->addColumn ( 
            'is_buyer_refunded', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true], 
            'Is Buyer Refunded' )
        ->addColumn ( 
            'is_buyer_returned', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true], 
            'Is Buyer Returned' )
        ->addColumn ( 
            'is_refunded', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true], 
            'Is Refunded' )
        ->addColumn ( 
            'is_returned', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true], 
            'Is Returned' )
        ->addColumn ( 'admin_commission', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Admin Commission' )
        ->addColumn ( 'seller_commission', 
             Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Admin Commission' )
        ->addColumn ( 'admin_commission_order', 
             Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Admin Commission' )
        ->addColumn ( 'seller_commission_order', 
             Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Seller Commission' )
        ->addColumn ( 'admin_commission_refund', 
             Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Admin Commission' )
        ->addColumn ( 'seller_commission_refund', 
             Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Seller Commission' );
        $setup->getConnection()->createTable($table);

          
        /**
         * Create table 'lof_marketplace_sellerinvoice'
         */
        $table = $setup->getConnection()->
        newTable($setup->getTable('lof_marketplace_sellerinvoice')
        )->addColumn(
            'sellerinvoice_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'identity' => true],
            'Invoice Id'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Seller Id'
        )->addColumn(
            'seller_order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Seller Order Id'
        )->addColumn(
            'invoice_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Base Invoice Id'
        )
        ->addColumn ( 
            'seller_amount', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Seller Amount' )
        ->addColumn(
            'base_grand_total',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Base Grand Total'
        )->addColumn(
            'shipping_tax_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Shipping Tax Amount'
        )->addColumn(
            'tax_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Tax Amount'
        )->addColumn(
            'base_tax_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Base Tax Amount'
        )->addColumn(
            'base_shipping_tax_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Base Shipping Tax Amount'
        )->addColumn(
            'base_discount_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Base Discount Amount'
        )->addColumn(
            'grand_total',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Grand Total'
        )->addColumn(
            'shipping_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Shipping Amount'
        )->addColumn(
            'subtotal_incl_tax',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Subtotal Incl Tax'
        )->addColumn(
            'base_subtotal_incl_tax',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Base Subtotal Incl Tax'
        )->addColumn(
            'base_shipping_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Base Shipping Amount'
        )->addColumn(
            'total_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Total Qty'
        )->addColumn(
            'subtotal',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Subtotal'
        )->addColumn(
            'base_subtotal',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Base Subtotal'
        )->addColumn(
            'discount_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Discount Amount'
        )->addColumn(
            'state',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'State'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addColumn(
            'shipping_incl_tax',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Shipping Incl Tax'
        )->addColumn(
            'base_shipping_incl_tax',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Base Shipping Incl Tax'
        )->addColumn(
            'base_total_refunded',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Base Total Refunded'
        )->addColumn(
            'discount_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Discount Description'
        )->addColumn(
            'customer_note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Customer Note'
        )->addIndex(
            $setup->getIdxName('lof_marketplace_sellerinvoice', ['grand_total']),
            ['grand_total']
        )->addIndex(
            $setup->getIdxName('lof_marketplace_sellerinvoice', ['state']),
            ['state']
        )->addIndex(
            $setup->getIdxName('lof_marketplace_sellerinvoice', ['created_at']),
            ['created_at']
        )->addIndex(
            $setup->getIdxName('lof_marketplace_sellerinvoice', ['updated_at']),
            ['updated_at']
        );
        $setup->getConnection()->createTable($table);


         /**
         * Create table 'lof_marketplace_sellerinvoice'
         */
        $table = $setup->getConnection()->
        newTable($setup->getTable('lof_marketplace_sellerpayment')
        )->addColumn ( 
            'id', 
            Table::TYPE_INTEGER, 
            null, ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 
            'ID' )
        ->addColumn ( 
            'paid_amount', 
            Table::TYPE_DECIMAL, 
            '12,4', 
            [ ], 
            'Fee' )
        ->addColumn ( 
            'seller_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['nullable' => false], 
            'Seller Id' )
        ->addColumn ( 'invoice', 
            Table::TYPE_TEXT, 
            255, 
            [ ], 
            'Invoice' )
        ->addColumn ( 
            'created_at', 
            Table::TYPE_DATETIME, 
            null, 
            ['nullable' => false], 
            'Created At' )
        ->addColumn ( 
            'ack_at', 
            Table::TYPE_DATETIME, 
            null, 
            ['nullable' => false], 
            'Acknowledge At' )
        ->addColumn ( 
            'is_ack', 
            Table::TYPE_SMALLINT, 
            null, 
            ['unsigned' => true], 
            'Is Acknowledged' )
        ->addColumn ( 
            'comment', 
            Table::TYPE_TEXT, 
            255, 
            [ ], 
            'Comment' )
        ->addColumn ( 
            'payment_type', 
            Table::TYPE_TEXT, 
            255, 
            [ ], 
            'Payment Type' );

        $setup->getConnection()->createTable($table);
         /**
         * Create table 'lof_marketplace_refund'
         */
        $table = $setup->getConnection()->
        newTable($setup->getTable('lof_marketplace_refund')
        )->addColumn ( 
            'refund_id', 
            Table::TYPE_INTEGER, 
            null, ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 
            'ID' )
        ->addColumn ( 
            'creditmemo_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['nullable' => false], 
            'Credit Memo Id' )
        ->addColumn ( 
            'seller_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['nullable' => false], 
            'Seller Id' )
         ->addColumn ( 
            'order_id', 
            Table::TYPE_INTEGER, 
            null, 
            ['nullable' => false], 
            'Order Id' )
         ->addColumn ( 
            'refunded', 
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12,4],
            [],
            'Refunded' )
        ->addColumn ( 'billing_name', 
            Table::TYPE_TEXT, 
            255, 
            [ ], 
            'Billing To Name' )
         ->addColumn ( 'status', 
            Table::TYPE_TEXT, 
            255, 
            [ ], 
            'Status' )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        );

        $setup->getConnection()->createTable($table);
          /**
         * Create table 'lof_marketplace_amount'
         */
          $table = $setup->getConnection()->newTable(
            $setup->getTable('lof_marketplace_amount')
        )->addColumn(
            'amount_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Amount Id'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Seller Id'
        )->addColumn(
            'relation_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Invoice Id'
        )->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12,4],
            [],
            'Amount'
        )->addColumn(
            'additional_info',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            [],
            'Additional Info'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addForeignKey(
            $setup->getFkName('lof_marketplace_amount', 'seller_id', 'lof_marketplace_seller', 'seller_id'),
            'seller_id',
            $setup->getTable('lof_marketplace_seller'),
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $setup->getConnection()->createTable($table);
         

        /**
         * Create table 'lof_marketplace_payment'
         */
          $table = $setup->getConnection()->newTable(
            $setup->getTable('lof_marketplace_payment')
        )->addColumn(
            'payment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Amount Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT, 
            255, 
            [ ], 
            'Name'
        )->addColumn(
            'fee',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Fee'
        )->addColumn(
            'min_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Min Amount'
        )->addColumn(
            'max_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Max Amount'
        )->addColumn(
            'order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Order'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['unsigned' => true],
            'Description'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['unsigned' => true],
            'Description'
        )->addColumn(
            'email_acount',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Show Email Acount'
        )->addColumn(
            'additional',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Additional'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'lof_marketplace_withdrawal
         */
          $table = $setup->getConnection()->newTable(
            $setup->getTable('lof_marketplace_withdrawal')
        )->addColumn(
            'withdrawal_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Amount Id'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Seller'
        )->addColumn(
            'payment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Payment'
        )
        ->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true],
            'Show Email Acount'
        )->addColumn(
            'fee',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Fee'
        )->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['unsigned' => true],
            'Amount'
        )->addColumn(
            'net_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['unsigned' => true],
            'Amount'
        )->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['unsigned' => true],
            'Comment'
        )->addColumn(
            'admin_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['unsigned' => true],
            'Admin Message'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        );
        $setup->getConnection()->createTable($table);

         /**
         * Create table 'lof_marketplace_amount_transaction'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('lof_marketplace_amount_transaction')
        )->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Transaction Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer Id'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer Id'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '32',
            [],
            'Transaction Type'
        )->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Transaction amount'
        )->addColumn(
            'balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Balance'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
            [],
            'Description'
        )->addColumn(
            'additional_info',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
            [],
            'Additional Info'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Created At'
        );
        $setup->getConnection()->createTable($table);

        $setup->getConnection()
            ->addColumn($setup->getTable('catalog_product_entity'), 
                'seller_id',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length'   => 10,
                'nullable' => false,
                'default'  => 0,
                'unsigned' => true,
                'comment'  => 'Master Seller Id'
                ]);

        $setup->getConnection()
                ->addColumn($setup->getTable('catalog_product_entity'), 
                    'approval',
                    [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length'   => 10,
                        'nullable' => false,
                        'default'  => 0,
                        'unsigned' => true,
                        'comment'  => 'Product approval'
                    ]);
        /**
         * Create table 'lof_marketplace_message_detail'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('lof_marketplace_message')
        )->addColumn(
            'message_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Identifier'
        )->addColumn(
            'owner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'unsigned' => true, 'nullable' => false,],
            'Owner Id'
        )->addColumn(
            'sender_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Sender Id'
        )->addColumn(
            'sender_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Sender Email'
        )->addColumn(
            'sender_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Sender Name'
        )->addColumn(
            'seller_send',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Seller Send'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
            [],
            'Description'
        )->addColumn(
            'receiver_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Receiver Id'
        )->addColumn(
            'subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Subject'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false,],
            'Status (0 => Draft, 1 => Unread, 2 => Read, 3 => Sent)'
        )->addColumn(
            'is_read',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false,],
            'Is read Message (0 => No, 1 => Yes)'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        );
        $setup->getConnection()->createTable($table);
        
        /**
         * Create table 'lof_marketplace_message_detail'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('lof_marketplace_message_detail')
        )->addColumn(
            'detail_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Detail Id'
        )->addColumn(
            'message_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Message Id'
        )->addColumn(
            'seller_send',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Seller Send'
        )->addColumn(
            'sender_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Sender Id'
        )->addColumn(
            'sender_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Sender Email'
        )->addColumn(
            'sender_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Sender Name'
        )->addColumn(
            'receiver_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Receiver Id'
        )->addColumn(
            'receiver_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Receiver Email'
        )->addColumn(
            'receiver_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Receiver Name'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            [],
            'Message Content'
        )->addColumn(
            'is_read',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false,],
            'Is read Message (0 => No, 1 => Yes)'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addForeignKey(
            $setup->getFkName('lof_marketplace_message_detail', 'message_id', 'lof_marketplace_message', 'message_id'),
            'message_id',
            $setup->getTable('lof_marketplace_message'),
            'message_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $setup->getConnection()->createTable($table);

         /**
         * Create table 'lof_marketpalce_review'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('lof_marketplace_review')
        )->addColumn(
            'reviewseller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false,],
            'Review Type (1 => Customer, 2 => Seller)'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Seller Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'customer id'
        )->addColumn(
            'review_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'review id'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Related Order Id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Related Product Id'
        )->addColumn(
            'is_public',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            ['unsigned' => true, 'nullable' => false,],
            'Is Public Review'
        )->addColumn(
            'rating',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            2,
            [],
            'Rating'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Title'
        )->addColumn(
            'detail',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            [],
            'Detail'
        )->addColumn(
            'nickname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '32',
            [],
            'Nickname'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false,],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Created At'
        );
        $setup->getConnection()->createTable($table);
        
        
        /**
         * Create table 'lof_marketplace_rating'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('lof_marketplace_rating')
        )->addColumn(
            'rating_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rating Id'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'seller_id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'customer_id'
        )->addColumn(
            'rate1',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'rate1'
        )->addColumn(
            'rate2',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'rate1'
        )->addColumn(
            'rate3',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'rate1'
        )->addColumn(
            'rating',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'rating'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Email'
        )->addColumn(
            'nickname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Nickname'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Title'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Status'
        )->addColumn(
            'detail',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            [],
            'Detail'
        )->addColumn(
            'nickname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '32',
            [],
            'Nickname'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Created At'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'lof_marketplace_vacation'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('lof_marketplace_vacation')
        )->addColumn(
            'vacation_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rating Id'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'seller_id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false,],
            'Status'
        )->addColumn(
            'vacation_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            [],
            'Vacation Message'
        )->addColumn(
            'from_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true],
            'From Date'
        )->addColumn(
            'to_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true],
            'To Date'
        )->addColumn(
            'text_add_cart',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            [],
            'Text Add Cart'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Update Time'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
 }
