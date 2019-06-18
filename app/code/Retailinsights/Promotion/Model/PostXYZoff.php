<?php

namespace Retailinsights\Promotion\Model;

class PostXYZoff extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'custom_promotion_NXNYNZoff';

	protected $_cacheTag = 'custom_promotion_NXNYNZoff';

	protected $_eventPrefix = 'custom_promotion_NXNYNZoff';

	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\ResourceModel\PostXYZoff');
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
