<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Model\ResourceModel\Refund;

use \Lof\MarketPlace\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'refund_id';
    /**
     * Define resource model
     *
     * @return void
     */
    

    protected function _construct()
    {
        $this->_init('Lof\MarketPlace\Model\Refund', 'Lof\MarketPlace\Model\ResourceModel\Refund');
    }

    /**
     * Add link attribute to filter.
     *
     * @param string $code
     * @param array $condition
     * @return $this
     */
        public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    } 
}