<?php

namespace Retailinsights\Promotion\Model;

class Post extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'salesrule';

	protected $_cacheTag = 'salesrule';

	protected $_eventPrefix = 'salesrule';

	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\ResourceModel\Post');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}
