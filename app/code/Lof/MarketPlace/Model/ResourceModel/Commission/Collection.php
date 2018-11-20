<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Model\ResourceModel\Commission;

use \Lof\MarketPlace\Model\ResourceModel\AbstractCollection;
/**
 * Seller collection
 */
class Collection extends AbstractCollection
{

	/**
     * @var string
     */
	protected $_idFieldName = 'commission_id';

	/**
     * Define resource model
     *
     * @return void
     */
	protected function _construct()
	{
		$this->_init('Lof\MarketPlace\Model\Commission', 'Lof\MarketPlace\Model\ResourceModel\Commission');
		$this->_map['fields']['commission_id'] = 'main_table.commission_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
         $this->_map['fields']['group'] = 'store_table.group_id';
	}
    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoadCommission('lof_marketplace_commission_store', 'commission_id');
        $this->performAfterLoadGroup('lof_marketplace_commission_group', 'commission_id');
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }
     /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('lof_marketplace_commission_store', 'commission_id');
    }
    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
}