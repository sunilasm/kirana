<?php

namespace WebbyTroops\WishlistAPI\Model\Data;

/**
 * Class KeyValue
 */
class KeyValue extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \WebbyTroops\WishlistAPI\Api\Data\KeyValueInterface
{
    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return $this->getData(self::LABEL_KEY);
    }
    
    /**
     * @inheritdoc
     */
    public function setKey($key)
    {
        return $this->setData(self::LABEL_KEY, $key);
    }
    
    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->getData(self::LABEL_VALUE);
    }
    
    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        return $this->setData(self::LABEL_VALUE, $value);
    }
}
