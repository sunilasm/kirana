<?php
namespace Retailinsights\Slider\Model;
class Post extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'promoslider_images';

    protected $_cacheTag = 'promoslider_images';

    protected $_eventPrefix = 'promoslider_images';

    protected function _construct()
    {
        $this->_init('Retailinsights\Slider\Model\ResourceModel\Post');
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
