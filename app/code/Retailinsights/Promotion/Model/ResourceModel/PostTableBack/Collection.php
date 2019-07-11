<?php

namespace Retailinsights\Promotion\Model\ResourceModel\PostTableBack;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'p_id';
	protected $_eventPrefix = 'retailinsights_promostoremapp_backup_collection';
	protected $_eventObject = 'posttableback_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\PostTableBack', 'Retailinsights\Promotion\Model\ResourceModel\PostTableBack');
	}
}
