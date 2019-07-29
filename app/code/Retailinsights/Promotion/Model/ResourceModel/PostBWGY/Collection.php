<?php
namespace Retailinsights\Promotion\Model\ResourceModel\PostBWGY;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'post_id';
	protected $_eventPrefix = 'retailinsights_promotion_postbwgy_collection';
	protected $_eventObject = 'post_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\PostBWGY', 'Retailinsights\Promotion\Model\ResourceModel\PostBWGY');
	}

}
