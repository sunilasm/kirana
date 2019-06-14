<?php

namespace Retailinsights\Promotion\Model\ResourceModel\PostSeller;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'p_id';
	protected $_eventPrefix = 'lof_marketplace_seller_collection';
	protected $_eventObject = 'post_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\PostSeller', 'Retailinsights\Promotion\Model\ResourceModel\PostSeller');
	}
}
