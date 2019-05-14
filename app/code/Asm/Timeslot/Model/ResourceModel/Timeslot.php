<?php
namespace Asm\Timeslot\Model\ResourceModel;

class Timeslot extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('order_time_slot', 'id');
    }
}
?>