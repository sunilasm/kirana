<?php
namespace Retailinsights\Pricerules\Model;
class Postthree extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'custom_promotion_bytwo_getfixed';

	protected $_cacheTag = 'custom_promotion_bytwo_getfixed';

	protected $_eventPrefix = 'custom_promotion_bytwo_getfixed';

	protected function _construct()
	{
		$this->_init('Retailinsights\Pricerules\Model\ResourceModel\Postthree');
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
