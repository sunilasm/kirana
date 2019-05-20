<?php
namespace Retailinsights\Promotion\Model\ResourceModel;


class Postthree extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('custom_promotion_bytwo_getfixed', 'post_id');
	}
	
}
