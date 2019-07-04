<?php

namespace Retailinsights\Promotion\Model\ResourceModel;

class PostTableBack extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('retailinsights_promostoremapp_backup', 'p_id');
	}	
}
