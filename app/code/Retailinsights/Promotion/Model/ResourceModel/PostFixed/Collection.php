<?php
namespace Retailinsights\Promotion\Model\ResourceModel\PostFixed;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'post_id';
	protected $_eventPrefix = 'retailinsights_pricerules_postfixed_collection';
	protected $_eventObject = 'post_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\PostFixed', 'Retailinsights\Promotion\Model\ResourceModel\PostFixed');
	}

}
