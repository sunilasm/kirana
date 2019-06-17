<?php

namespace Retailinsights\Promotion\Model\ResourceModel\PostTable;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'p_id';
	protected $_eventPrefix = 'retailinsights_promostoremapp_collection';
	protected $_eventObject = 'posttable_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\PostTable', 'Retailinsights\Promotion\Model\ResourceModel\PostTable');
	}
}
