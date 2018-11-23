<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Model\ResourceModel\SellerInvoice;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\MarketPlace\Model\SelerInvoice', 'Lof\MarketPlace\Model\ResourceModel\SelerInvoice');
    }
    
    /**
     * Set order Filter.
     * @param unknown $conditions
     */
    public function setOrderFilter($conditions){
        $this->addFieldToFilter('entity_id',$conditions);
        return $this;
    }

}
