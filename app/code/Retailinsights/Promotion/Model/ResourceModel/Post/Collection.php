<?php
namespace Retailinsights\Promotion\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'p_id';
	protected $_eventPrefix = 'salesrule_collection';
	protected $_eventObject = 'post_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\Post', 'Retailinsights\Promotion\Model\ResourceModel\Post');
	}

}
