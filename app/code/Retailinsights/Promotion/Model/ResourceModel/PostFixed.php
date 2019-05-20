<?php
namespace Retailinsights\Promotion\Model\ResourceModel;


class PostFixed extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('custom_promotion_byX_getFixed', 'post_id');
	}
	
}
