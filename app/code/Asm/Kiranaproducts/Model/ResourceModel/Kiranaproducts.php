<?php
namespace Asm\Kiranaproducts\Model\ResourceModel;

class Kiranaproducts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mglof_marketplace_product', 'entity_id');
    }
}
?>