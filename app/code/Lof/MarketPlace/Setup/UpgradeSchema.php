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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;


class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'lof_marketplace_product'
         */
        // $table = $installer->getConnection()->newTable(
        //     $installer->getTable('lof_marketplace_product')
        // )->addColumn(
        //     'seller_id',
        //     Table::TYPE_INTEGER,
        //     null,
        //     ['nullable' => false, 'primary' => true],
        //     'Seller ID'
        // )->addColumn(
        //     'product_id',
        //     Table::TYPE_SMALLINT,
        //     null,
        //     ['unsigned' => true, 'nullable' => false, 'primary' => true],
        //     'Product ID'
        // )->addColumn(
        //     'product_name',
        //     \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        //     255,
        //     ['nullable' => false],
        //     'product name'
        // )->addColumn(
        //     'position',
        //     Table::TYPE_INTEGER,
        //     11,
        //     ['nullable' => true],
        //     'Position'
        // )->setComment(
        //     'Lof Seller To Product Linkage Table'
        // );
        // $installer->getConnection()->createTable($table);
         /**
         *  setup for Seller Settings
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_marketplace_seller_settings')
        )->addColumn(
            'setting_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Setting Id'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Seller Id'
        )->addColumn(
            'group',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '32',
            ['nullable' => false],
            'Group'
        )->addColumn(
            'key',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64',
            ['nullable' => false],
            'Key'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '500',
            ['nullable' => false],
            'value'
        )->addColumn(
            'serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'serialized'
        )->setComment(
            'MarketPlace Seller Settings'
        );
        $installer->getConnection()->createTable($table);
        
       if (version_compare($context->getVersion(), '1.0.8', '<')) {
        
            $connection = $setup->getConnection();
            $gridSellerProduct = $setup->getTable('lof_marketplace_product');
            $gridSellerOrder = $setup->getTable('lof_marketplace_sellerorder');
            $affiliate = $setup->getTable('lof_marketplace_seller');
            $columns = [
                    'name' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Seller name',
                ],
            ];
            $connection = $setup->getConnection();

            foreach ($columns as $name => $definition) {
                $connection->addColumn($gridSellerOrder, $name, $definition);
                $connection->addColumn($gridSellerProduct, $name, $definition);
            }

            $connection->query(
                $connection->updateFromSelect(
                    $connection->select()
                        ->join(
                            $affiliate,
                            sprintf('%s.seller_id = %s.seller_id', $gridSellerOrder, $affiliate),
                            'name'
                        ),
                    $gridSellerOrder
                )
            );
            $connection->query(
                $connection->updateFromSelect(
                    $connection->select()
                        ->join(
                            $affiliate,
                            sprintf('%s.seller_id = %s.seller_id', $gridSellerProduct, $affiliate),
                            'name'
                        ),
                    $gridSellerProduct
                )
            );
        }
        /* table lof_marketplace_group */
        $table = $installer->getTable('lof_marketplace_group');
        
        $installer->getConnection()->addColumn(
            $table,
            'can_add_product',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'Can add product'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'can_cancel_order',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'Can cancel order'
            ]
        );
         $installer->getConnection()->addColumn(
            $table,
            'can_create_invoice',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_create_invoice'
            ]
        );
         $installer->getConnection()->addColumn(
            $table,
            'can_create_shipment',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_create_shipment'
            ]
        );
         $installer->getConnection()->addColumn(
            $table,
            'can_create_creditmemo',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_create_creditmemo'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'hide_payment_info',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 0,
                'comment'  => 'hide_payment_info'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'hide_customer_email',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 0,
                'comment'  => 'hide_customer_email'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'can_use_shipping',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_use_shipping'
            ]
        );
         $installer->getConnection()->addColumn(
            $table,
            'can_submit_order_comments',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_submit_order_comments'
            ]
        );
         $installer->getConnection()->addColumn(
            $table,
            'can_use_message',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_use_message'
            ]
        );
         $installer->getConnection()->addColumn(
            $table,
            'can_use_review',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_use_review'
            ]
        );
          $installer->getConnection()->addColumn(
            $table,
            'can_use_rating',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_use_rating'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'can_use_import_export',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_use_import_export'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'can_use_vacation',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_use_vacation'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'can_use_report',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_use_report'
            ]
        );
         $installer->getConnection()->addColumn(
            $table,
            'can_use_withdrawal',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 10,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'can_use_withdrawal'
            ]
        );
         /* table lof_marketplace_payment */
        $table = $installer->getTable('lof_marketplace_payment');
        $installer->getConnection()->addColumn(
            $table,
            'fee_by',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'Fee By'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'fee_percent',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                'length'   => '',
                'nullable' => true,
                'comment'  => 'Fixed Percent'
            ]
        );

        $table = $installer->getTable('lof_marketplace_seller');

        $installer->getConnection()->addColumn(
            $table,
            'company',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'Company'
            ]
        );
         $installer->getConnection()->addColumn(
            $table,
            'city',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'City'
            ]
        );
          $installer->getConnection()->addColumn(
            $table,
            'region',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'Region'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'street',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'street'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'product_count',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length'   => '15',
                'nullable' => true,
                'comment'  => 'Product count'
            ]
        );
         $installer->getConnection()->addColumn(
            $table,
            'duration_of_vendor',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length'   => '15',
                'nullable' => true,
                'comment'  => 'duration_of_vendor'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'region_id',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length'   => '15',
                'nullable' => true,
                'comment'  => 'region id'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'postcode',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length'   => '15',
                'nullable' => true,
                'comment'  => 'postcode'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'telephone',
            [
               'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length'   => '15',
                'nullable' => true,
                'comment'  => 'Tele Phone'
            ]
        );
          $installer->getConnection()->addColumn(
            $table,
            'total_sold',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                'length'   => '',
                'nullable' => true,
                'comment'  => 'total amount sold'
            ]
        );
            if (version_compare($context->getVersion(), '1.0.7') <= 0) {

            /* table lof_sellerr */
            $table = $installer->getTable('lof_marketplace_seller');

            $installer->getConnection()->addColumn(
                $table,
                'geo_lat',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Goe Latitude'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'geo_lng',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Goe Longitude'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'store_type',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Store Type'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                '24by7_shop',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => '24*7 Shop'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'opening_time',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Opening Time'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'closeing_time',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Closeing Time'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'non_working_days',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Days Not Working'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'lsn',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'LSN'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'cst',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'CST'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'pan',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'PAN'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'vat',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'VAT'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'tin',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'TIN'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'gst',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'gst'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'digital_verification',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Digital Verification'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'physical_verification',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Physical Verification'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'smart_phone',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Smart phone wih data'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'knows_english',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Knows English'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'parent_store',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Parent Store'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'parent_store_id',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'ParentSore ID'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'kirana_type',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Kirana Type'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'kirana_locality',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Kirana Locality'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'kirana_owner',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Kirana Owner'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'kirana_fixed_line',
                [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Kirans Fixed Line'
                ]
            );
        }


//Upgraded columns retail
        if (version_compare($context->getVersion(), '1.1.0') < 0)  {
            $installer->getConnection()->addColumn(
                $installer->getTable('lof_marketplace_product'),
                'doorstep_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'doorstep_price'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('lof_marketplace_product'),
                'pickup_from_store',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'pickup_from_store'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('lof_marketplace_product'),
                'pickup_from_nearby_store',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'pickup_from_nearby_store'
                ]
            );
            
        }


         $installer->endSetup();  
    }
}
