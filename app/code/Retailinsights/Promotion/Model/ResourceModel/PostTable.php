<?php

namespace Retailinsights\Promotion\Model\ResourceModel;

class PostTable extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('retailinsights_promostoremapp', 'p_id');
	}	
}
