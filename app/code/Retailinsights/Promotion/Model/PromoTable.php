<?php
namespace Retailinsights\Promotion\Model;
class PromoTable extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'applicable_promotions';

	protected $_cacheTag = 'applicable_promotions';

	protected $_eventPrefix = 'applicable_promotions';

	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\ResourceModel\PromoTable');
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
