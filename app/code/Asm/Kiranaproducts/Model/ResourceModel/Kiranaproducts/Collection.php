<?php

namespace Asm\Kiranaproducts\Model\ResourceModel\Kiranaproducts;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Asm\Kiranaproducts\Model\Kiranaproducts', 'Asm\Kiranaproducts\Model\ResourceModel\Kiranaproducts');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>