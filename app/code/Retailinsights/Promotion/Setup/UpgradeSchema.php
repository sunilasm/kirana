<?php
namespace Retailinsights\Promotion\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;


class UpgradeSchema implements UpgradeSchemaInterface
{
     public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;

        $installer->startSetup();
    
        if (version_compare($context->getVersion(), '1.0.9') < 0)  {
            if (!$installer->tableExists('retailinsights_promostoremapp')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('retailinsights_promostoremapp')
                )
                ->addColumn(
                    'p_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
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
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    1,
                    ['nullable => false'],
                    'Store ID'
                )
                ->addColumn(
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    1,
                    ['nullable => false'],
                    'Rule ID'
                )
                ->addColumn(
                    'pstart_date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable => false'],
                    'Promotion Start Date'
                )
                ->addColumn(
                    'pend_date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable => false'],
                    'Promotion End Date'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    1,
                    ['nullable => false'],
                    'Promotion Status'
                )
                ->addColumn(
                    'description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    225,
                    ['nullable' => false],
                    'description of rule'
                )

                ->addColumn(
                    'store_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    225,
                    ['nullable' => false],
                    'store name'
                )
                ->addColumn(
                    'seller_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable => false'],
                    'seller type'
                )
                
                ->addColumn(
                    'conditions_serialized',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'conditions of rule'
                )
                ->addColumn(
                    'actions_serialized',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'actions of rule'
                )
                ->addColumn(
                    'simple_action',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'simple action of rule'
                )
                ->addColumn(
                    'discount_amount',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    225,
                    ['nullable' => false],
                    'Discount Amount'
                )
                ->addColumn(
                    'rule_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    1,
                    ['nullable' => false],
                    'Rule Type'
                )

                    ->setComment('Promotion Store Mapping');
                $installer->getConnection()->createTable($table);

                $installer->getConnection()->addIndex(
                    $installer->getTable('retailinsights_promostoremapp'),
                    $setup->getIdxName(
                        $installer->getTable('retailinsights_promostoremapp'),
                        ['store_id','rule_id','pstart_date','pend_date','status','description','seller_type','conditions_serialized','actions_serialized','simple_action','discount_amount','rule_type'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                    ),
                    ['store_id','rule_id','pstart_date','pend_date','status','description','seller_type','conditions_serialized','actions_serialized','simple_action','discount_amount','rule_type'],

                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                );
            }
        }

        
        $installer->endSetup();
    }
}


