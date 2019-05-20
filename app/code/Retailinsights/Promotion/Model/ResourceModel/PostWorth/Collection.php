<?php
namespace Retailinsights\Promotion\Model\ResourceModel\PostWorth;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'post_id';
	protected $_eventPrefix = 'retailinsights_pricerules_postworth_collection';
	protected $_eventObject = 'post_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\PostWorth', 'Retailinsights\Promotion\Model\ResourceModel\PostWorth');
	}

}
