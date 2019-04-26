<?php

namespace Asm\Customapi\Model\ResourceModel\Area;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Asm\Customapi\Model\Area', 'Asm\Customapi\Model\ResourceModel\Area');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>