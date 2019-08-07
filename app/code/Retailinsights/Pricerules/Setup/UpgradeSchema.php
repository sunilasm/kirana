<?php

namespace Retailinsights\Pricerules\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
		$installer = $setup;

		$installer->startSetup();
		
		if (version_compare($context->getVersion(), '1.0.1') < 0)  {
			if (!$installer->tableExists('custom_promotion_byX_getY')) {
				$table = $installer->getConnection()->newTable(
					$installer->getTable('custom_promotion_byX_getY')
				)
					->addColumn(
						'post_id',
						Table::TYPE_INTEGER,
						null,
						[
							'identity' => true,
							'nullable' => false,
							'primary'  => true,
							'unsigned' => true,
						],
						'Post ID'
					)
					->addColumn(
						'buy_product',
						Table::TYPE_TEXT,
						255,
						['nullable => false'],
						'Buy Product'
					)
					->addColumn(
						'buy_quantity',
						Table::TYPE_INTEGER,
						null,
						['nullable => false'],
						'Buy Product quantity'
					)
					->addColumn(
						'get_product',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
						],
						'Get Product'
					)
					->addColumn(
						'get_quantity',
						Table::TYPE_INTEGER,
						null,
						['nullable => false'],
						'Get Product quantity'
					)
					->addColumn(
						'name',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
						],
						'name'
					)
					->addColumn(
						'store_id',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
						],
						'Store Id'
					)
					->addColumn(
						'priority',
						Table::TYPE_INTEGER,
						null,
						['nullable => false'],
						'priority'
					)
					->addColumn(
						'offer_from',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
							'default' => ''	
						],
						'From Date'
					)
					->addColumn(
						'offer_to',
						Table::TYPE_TEXT,
						null,
						[
							'nullable' => false,
							'default' => ''	
						],
						'To Date'
					)
					->addColumn(
						'customer_group',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
							'default' => ''	
						],
						'To Date'
					)
					->addColumn(
						'status',
						Table::TYPE_INTEGER,
						1,
						[],
						'Post Status'
					)
					
					->addColumn(
						'featured_image',
						Table::TYPE_TEXT,
						255,
						[],
						'Post Featured Image'
					)
					->addColumn(
						'created_at',
						Table::TYPE_TIMESTAMP,
						null,
						['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
						'Created At'
					)->addColumn(
						'updated_at',
						Table::TYPE_TIMESTAMP,
						null,
						['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
						'Updated At')
					->setComment('Post Table');
				$installer->getConnection()->createTable($table);

				$installer->getConnection()->addIndex(
					$installer->getTable('custom_promotion_byX_getY'),
					$setup->getIdxName(
						$installer->getTable('custom_promotion_byX_getY'),
						['buy_product','buy_quantity','get_product','get_quantity','name','store_id','priority','offer_from','offer_to','status','featured_image'],
						\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
					),
					['buy_product','buy_quantity','get_product','get_quantity','name','store_id','priority','offer_from','offer_to','status','featured_image'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				);
			}
		}

		//**New table  */
		if (version_compare($context->getVersion(), '1.0.1') < 0) 
		{
			$tableName = $installer->getTable('custom_promotion_byX_getFixed');
			if ($installer->getConnection()->isTableExists($tableName) != true) {
			  $table = $installer->getConnection()
				  ->newTable($tableName)
				  ->addColumn(
					'post_id',
					Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Post ID'
				)
				->addColumn(
					'buy_product',
					Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'Buy Product'
				)
				->addColumn(
						'quantity',
						Table::TYPE_INTEGER,
						null,
						['nullable => false'],
						'Product quantity'
					)
				->addColumn(
					'fixed_price',
					Table::TYPE_DECIMAL,
					[12, 4],
					['nullable' => false, 'default' => '0.00'],
					'get fixed'
				)
				->addColumn(
						'name',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
						],
						'name'
					)
					->addColumn(
						'store_id',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
						],
						'Store Id'
					)
					->addColumn(
						'priority',
						Table::TYPE_INTEGER,
						null,
						['nullable => false'],
						'priority'
					)
				->addColumn(
					'offer_from',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
						'default' => ''	
					],
					'From Date'
				)
				->addColumn(
					'offer_to',
					Table::TYPE_TEXT,
					null,
					[
						'nullable' => false,
						'default' => ''	
					],
					'To Date'
				)
				->addColumn(
					'customer_group',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
						'default' => ''	
					],
					'To Date'
				)
				
				->addColumn(
					'status',
					Table::TYPE_INTEGER,
					1,
					[],
					'Post Status'
				)
				->addColumn(
					'featured_image',
					Table::TYPE_TEXT,
					255,
					[],
					'Post Featured Image'
				)
				->addColumn(
					'created_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
					'Created At'
				)->addColumn(
					'updated_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
					'Updated At')
				->setComment('Post Table');
				$installer->getConnection()->createTable($table);
				$installer->getConnection()->addIndex(
					$installer->getTable('custom_promotion_byX_getFixed'),
					$setup->getIdxName(
						$installer->getTable('custom_promotion_byX_getFixed'),
						['buy_product','fixed_price','quantity','name','store_id','priority','offer_from','offer_to','status','featured_image'],
						\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
					),
					['buy_product','fixed_price','quantity','name','store_id','priority','offer_from','offer_to','status','featured_image'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				);
			}
	
		}
	
		if (version_compare($context->getVersion(), '2.0.3') < 0) 
		{	
			$tableName = $installer->getTable('custom_promotion_bytwo_getfixed');
			if ($installer->getConnection()->isTableExists($tableName) != true) {
			  $table = $installer->getConnection()
				  ->newTable($tableName)
				  ->addColumn(
					'post_id',
					Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Post ID'
				)
				->addColumn(
					'buy_product_one',
					Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'Buy Product'
				)
				->addColumn(
					'buy_product_two',
					Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'Buy Product'
				)
				
				->addColumn(
					'fixed_price',
					Table::TYPE_DECIMAL,
					[12, 4],
					['nullable' => false, 'default' => '0.00'],
					'get fixed'
				)
				->addColumn(
						'name',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
						],
						'name'
					)
					->addColumn(
						'store_id',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
						],
						'Store Id'
					)
					->addColumn(
						'priority',
						Table::TYPE_INTEGER,
						null,
						['nullable => false'],
						'priority'
					)
				->addColumn(
					'offer_from',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
						'default' => ''	
					],
					'From Date'
				)
				->addColumn(
					'offer_to',
					Table::TYPE_TEXT,
					null,
					[
						'nullable' => false,
						'default' => ''	
					],
					'To Date'
				)
				->addColumn(
					'customer_group',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
						'default' => ''	
					],
					'To Date'
				)
				->addColumn(
					'status',
					Table::TYPE_INTEGER,
					1,
					[],
					'Post Status'
				)
				->addColumn(
					'featured_image',
					Table::TYPE_TEXT,
					255,
					[],
					'Post Featured Image'
				)
				->addColumn(
					'created_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
					'Created At'
				)->addColumn(
					'updated_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
					'Updated At')
				->setComment('Post Table');
				$installer->getConnection()->createTable($table);
				$installer->getConnection()->addIndex(
					$installer->getTable('custom_promotion_byTwo_getFixed'),
					$setup->getIdxName(
						$installer->getTable('custom_promotion_byTwo_getFixed'),
						['buy_product_one','fixed_price','buy_product_two','name','store_id','priority','offer_from','offer_to','status','featured_image'],
						\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
					),
					['buy_product_one','fixed_price','buy_product_two','name','store_id','priority','offer_from','offer_to','status','featured_image'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				);
			}
		}
			if (version_compare($context->getVersion(), '1.0.1') < 0)  {
				if (!$installer->tableExists('custom_promotion_byXXX_getY')) {
					$table = $installer->getConnection()->newTable(
						$installer->getTable('custom_promotion_byXXX_getY')
					)
						->addColumn(
							'post_id',
							Table::TYPE_INTEGER,
							null,
							[
								'identity' => true,
								'nullable' => false,
								'primary'  => true,
								'unsigned' => true,
							],
							'Post ID'
						)
						->addColumn(
							'subtotal',
							Table::TYPE_DECIMAL,
							[12, 4],
							['nullable' => false, 'default' => '0.00'],
							'subtotal'
						)
						->addColumn(
							'get_product',
							Table::TYPE_TEXT,
							255,
							[
								'nullable' => false,
							],
							'Get Product'
						)
						->addColumn(
						'quantity',
						Table::TYPE_INTEGER,
						null,
						['nullable => false'],
						'Product quantity'
					)
						->addColumn(
						'name',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
						],
						'name'
					)
					->addColumn(
						'store_id',
						Table::TYPE_TEXT,
						255,
						[
							'nullable' => false,
						],
						'Store Id'
					)
					->addColumn(
						'priority',
						Table::TYPE_INTEGER,
						null,
						['nullable => false'],
						'priority'
					)
						->addColumn(
							'offer_from',
							Table::TYPE_TEXT,
							255,
							[
								'nullable' => false,
								'default' => ''	
							],
							'From Date'
						)
						->addColumn(
							'offer_to',
							Table::TYPE_TEXT,
							null,
							[
								'nullable' => false,
								'default' => ''	
							],
							'To Date'
						)
						->addColumn(
							'customer_group',
							Table::TYPE_TEXT,
							255,
							[
								'nullable' => false,
								'default' => ''	
							],
							'To Date'
						)
						->addColumn(
							'status',
							Table::TYPE_INTEGER,
							1,
							[],
							'Post Status'
						)
						->addColumn(
							'featured_image',
							Table::TYPE_TEXT,
							255,
							[],
							'Post Featured Image'
						)
						->addColumn(
							'created_at',
							Table::TYPE_TIMESTAMP,
							null,
							['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
							'Created At'
						)->addColumn(
							'updated_at',
							Table::TYPE_TIMESTAMP,
							null,
							['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
							'Updated At')
						->setComment('Post Table');
					$installer->getConnection()->createTable($table);
	
					$installer->getConnection()->addIndex(
						$installer->getTable('custom_promotion_byXXX_getY'),
						$setup->getIdxName(
							$installer->getTable('custom_promotion_byXXX_getY'),
							['subtotal','get_product','quantity','name','store_id','priority','offer_from','offer_to','status','featured_image'],
							\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
						),
						['subtotal','get_product','quantity','name','store_id','priority','offer_from','offer_to','status','featured_image'],
						\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
					);
				}
			}

		if (version_compare($context->getVersion(), '1.0.6') < 0)  {
		if (!$installer->tableExists('custom_promotion_NXNYNZ')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('custom_promotion_NXNYNZ')
			)
				->addColumn(
					'post_id',
					Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Post ID'
				)
				->addColumn(
					'rule_condition',
					Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'Rule Condition'
				)

				->addColumn(
					'discount_product',
					Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'discount product'
				)
				->addColumn(
					'discount',
					Table::TYPE_DECIMAL,
					[12, 4],
					['nullable' => false, 'default' => '0.00'],
					'discount'
				)
				->addColumn(
					'name',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
					],
					'name'
				)
				->addColumn(
					'priority',
					Table::TYPE_INTEGER,
					null,
					['nullable => false'],
					'priority'
				)
				->addColumn(
					'offer_from',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
						'default' => ''	
					],
					'From Date'
				)
				->addColumn(
					'offer_to',
					Table::TYPE_TEXT,
					null,
					[
						'nullable' => false,
						'default' => ''	
					],
					'To Date'
				)
				->addColumn(
					'customer_group',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
						'default' => ''	
					],
					'To Date'
				)
				
				->addColumn(
					'status',
					Table::TYPE_INTEGER,
					1,
					[],
					'Post Status'
				)
				->addColumn(
					'store_id',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
					],
					'Store Id'
				)
				->addColumn(
					'featured_image',
					Table::TYPE_TEXT,
					255,
					[],
					'Post Featured Image'
				)
				->addColumn(
					'created_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
					'Created At'
				)->addColumn(
					'updated_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
					'Updated At')
				->setComment('Post Table');
			$installer->getConnection()->createTable($table);

			$installer->getConnection()->addIndex(
				$installer->getTable('custom_promotion_NXNYNZ'),
				$setup->getIdxName(
					$installer->getTable('custom_promotion_NXNYNZ'),
					['rule_condition','discount_product','discount','name','priority','offer_from','offer_to','status','store_id','featured_image'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				),
				['rule_condition','discount_product','discount','name','priority','offer_from','offer_to','status','store_id','featured_image'],
				\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
			);
		}
	}
	if (version_compare($context->getVersion(), '2.0.4') < 0)  {
		if (!$installer->tableExists('custom_promotion_NXNYNZoff')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('custom_promotion_NXNYNZoff')
			)
				->addColumn(
					'post_id',
					Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Post ID'
				)
				->addColumn(
					'rule_condition',
					Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'Rule Condition'
				)
				->addColumn(
					'fixed_price',
					Table::TYPE_DECIMAL,
					[12, 4],
					['nullable' => false, 'default' => '0.00'],
					'fixed_price'
				)
				->addColumn(
					'name',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
					],
					'name'
				)
				->addColumn(
					'priority',
					Table::TYPE_INTEGER,
					null,
					['nullable => false'],
					'priority'
				)
				->addColumn(
					'offer_from',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
						'default' => ''	
					],
					'From Date'
				)
				->addColumn(
					'offer_to',
					Table::TYPE_TEXT,
					null,
					[
						'nullable' => false,
						'default' => ''	
					],
					'To Date'
				)
				->addColumn(
					'customer_group',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
						'default' => ''	
					],
					'To Date'
				)
				
				->addColumn(
					'status',
					Table::TYPE_INTEGER,
					1,
					[],
					'Post Status'
				)
				->addColumn(
					'featured_image',
					Table::TYPE_TEXT,
					255,
					[],
					'Post Featured Image'
				)
				->addColumn(
					'created_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
					'Created At'
				)->addColumn(
					'updated_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
					'Updated At')
				->setComment('Post Table');
			$installer->getConnection()->createTable($table);

			$installer->getConnection()->addIndex(
				$installer->getTable('custom_promotion_NXNYNZoff'),
				$setup->getIdxName(
					$installer->getTable('custom_promotion_NXNYNZoff'),
					['rule_condition','fixed_price','name','priority','offer_from','offer_to','status','featured_image'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				),
				['rule_condition','fixed_price','name','priority','offer_from','offer_to','status','featured_image'],
				\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
			);
		}
	}

	if (version_compare($context->getVersion(), '2.0.5') < 0)  {
		if (!$installer->tableExists('custom_promotion_BWGY')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('custom_promotion_BWGY')
			)
				->addColumn(
					'post_id',
					Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Post ID'
				)
				->addColumn(
					'fixed_amount',
					Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'Fixed Amount'
				)
				
				->addColumn(
					'get_product',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
					],
					'Get Product'
				)
				->addColumn(
					'get_quantity',
					Table::TYPE_INTEGER,
					null,
					['nullable => false'],
					'Get Product quantity'
				)
				->addColumn(
					'condition',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
					],
					'condition'
				)

				->addColumn(
					'name',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
					],
					'name'
				)
				->addColumn(
					'store_id',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
					],
					'Store Id'
				)
				->addColumn(
					'priority',
					Table::TYPE_INTEGER,
					null,
					['nullable => false'],
					'priority'
				)
				->addColumn(
					'offer_from',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
						'default' => ''	
					],
					'From Date'
				)
				->addColumn(
					'offer_to',
					Table::TYPE_TEXT,
					null,
					[
						'nullable' => false,
						'default' => ''	
					],
					'To Date'
				)
				->addColumn(
					'customer_group',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => false,
						'default' => ''	
					],
					'To Date'
				)
				->addColumn(
					'status',
					Table::TYPE_INTEGER,
					1,
					[],
					'Post Status'
				)
				
				->addColumn(
					'featured_image',
					Table::TYPE_TEXT,
					255,
					[],
					'Post Featured Image'
				)
				->addColumn(
					'created_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
					'Created At'
				)->addColumn(
					'updated_at',
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
					'Updated At')
				->setComment('Post Table');
			$installer->getConnection()->createTable($table);

			$installer->getConnection()->addIndex(
				$installer->getTable('custom_promotion_BWGY'),
				$setup->getIdxName(
					$installer->getTable('custom_promotion_BWGY'),
					['fixed_amount','get_product','get_quantity','name','store_id','priority','offer_from','offer_to','status','featured_image'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				),
				['fixed_amount','get_product','get_quantity','name','store_id','priority','offer_from','offer_to','status','featured_image'],
				\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
			);
		}
	}

	$installer->endSetup();
	}
}
