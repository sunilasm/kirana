<?php
namespace Retailinsights\Promotion\Model;
class PostSeller extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'lof_marketplace_seller';

	protected $_cacheTag = 'lof_marketplace_seller';

	protected $_eventPrefix = 'lof_marketplace_seller';

	protected function _construct()
	{
		$this->_init('Retailinsights\Promotion\Model\ResourceModel\PostSeller');
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
