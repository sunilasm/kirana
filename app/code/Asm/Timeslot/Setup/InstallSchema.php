<?php

namespace Asm\Timeslot\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0){

		$installer->run('create table mgorder_time_slot(id int not null auto_increment, order_id varchar(100), order_increment_id varchar(100), store_id varchar(100), time_slot_type varchar(100), date_slot DATE, time_slot TIME, primary key(id))');

		}

        $installer->endSetup();

    }
}