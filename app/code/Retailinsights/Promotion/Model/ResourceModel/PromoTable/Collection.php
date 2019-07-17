<?php
namespace Retailinsights\Promotion\Model\ResourceModel\PromoTable;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'ap_id';
	protected $_eventPrefix = 'applicable_promotions_collection';
	protected $_eventObject = 'promotable_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\PromoTable', 'Retailinsights\Promotion\Model\ResourceModel\PromoTable');
	}

}
