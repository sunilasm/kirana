<?php
namespace Asm\Customapi\Model;

class Area extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Asm\Customapi\Model\ResourceModel\Area');
    }
}
?>