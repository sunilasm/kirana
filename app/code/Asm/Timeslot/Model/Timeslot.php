<?php
namespace Asm\Timeslot\Model;

class Timeslot extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Asm\Timeslot\Model\ResourceModel\Timeslot');
    }
}
?>