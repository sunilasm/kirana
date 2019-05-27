<?php
namespace Retailinsights\Promotion\Model;
class PostWorth extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'custom_promotion_byXXX_getY';

	protected $_cacheTag = 'custom_promotion_byXXX_getY';

	protected $_eventPrefix = 'custom_promotion_byXXX_getY';

	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\ResourceModel\PostWorth');
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
