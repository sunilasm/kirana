<?php
namespace Retailinsights\Promotion\Model\ResourceModel;


class PromoTable extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('applicable_promotions', 'ap_id');
	}
	
}
