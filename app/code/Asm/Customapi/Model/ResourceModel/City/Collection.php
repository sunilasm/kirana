<?php

namespace Asm\Customapi\Model\ResourceModel\City;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Asm\Customapi\Model\City', 'Asm\Customapi\Model\ResourceModel\City');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>