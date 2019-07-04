<?php

namespace Retailinsights\Promotion\Model;

class PostTableBack extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'retailinsights_promostoremapp_backup';

	protected $_cacheTag = 'retailinsights_promostoremapp_backup';

	protected $_eventPrefix = 'retailinsights_promostoremapp_backup';

	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\ResourceModel\PostTableBack');
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
