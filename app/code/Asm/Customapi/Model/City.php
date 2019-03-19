<?php
namespace Asm\Customapi\Model;

class City extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Asm\Customapi\Model\ResourceModel\City');
    }
}
?>