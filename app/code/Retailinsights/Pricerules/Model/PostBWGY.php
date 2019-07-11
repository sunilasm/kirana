<?php
namespace Retailinsights\Pricerules\Model;
class PostBWGY extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'custom_promotion_BWGY';

	protected $_cacheTag = 'custom_promotion_BWGY';

	protected $_eventPrefix = 'custom_promotion_BWGY';

	protected function _construct()
	{
		$this->_init('Retailinsights\Pricerules\Model\ResourceModel\PostBWGY');
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
