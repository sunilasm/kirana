<?php

namespace Asm\Timeslot\Model\ResourceModel\Timeslot;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Asm\Timeslot\Model\Timeslot', 'Asm\Timeslot\Model\ResourceModel\Timeslot');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>