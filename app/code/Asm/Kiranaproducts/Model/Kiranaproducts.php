<?php
namespace Asm\Kiranaproducts\Model;

class Kiranaproducts extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Asm\Kiranaproducts\Model\ResourceModel\Kiranaproducts');
    }
}
?>