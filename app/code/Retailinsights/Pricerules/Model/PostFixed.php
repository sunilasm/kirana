<?php
namespace Retailinsights\Pricerules\Model;
class PostFixed extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'custom_promotion_byX_getFixed';

	protected $_cacheTag = 'custom_promotion_byX_getFixed';

	protected $_eventPrefix = 'custom_promotion_byX_getFixed';

	protected function _construct()
	{
		$this->_init('Retailinsights\Pricerules\Model\ResourceModel\PostFixed');
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
