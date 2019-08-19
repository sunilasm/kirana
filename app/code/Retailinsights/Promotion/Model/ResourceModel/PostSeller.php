<?php

namespace Retailinsights\Promotion\Model\ResourceModel;

class PostSeller extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('lof_marketplace_seller', 'rule_id');
	}	
}
