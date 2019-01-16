<?php

namespace Asm\Addressattribute\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $setup->startSetup();

            $setup->getConnection()->addColumn(
                $setup->getTable('customer_address_entity'),
                'latitude',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '250',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Geo latitude'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('customer_address_entity'),
                'longitude',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '250',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Geo longitude'
                ]
            );

            $setup->endSetup();
        }
    }
}