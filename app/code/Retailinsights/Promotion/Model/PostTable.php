<?php
namespace Retailinsights\Promotion\Model;
class PostTable extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'retailinsights_promostoremapp';

	protected $_cacheTag = 'retailinsights_promostoremapp';

	protected $_eventPrefix = 'retailinsights_promostoremapp';

	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\ResourceModel\PostTable');
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
