<?php

namespace Retailinsights\Promotion\Model;

class PostXYZ extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'custom_promotion_NXNYNZ';

	protected $_cacheTag = 'custom_promotion_NXNYNZ';

	protected $_eventPrefix = 'custom_promotion_NXNYNZ';

	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\ResourceModel\PostXYZ');
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
