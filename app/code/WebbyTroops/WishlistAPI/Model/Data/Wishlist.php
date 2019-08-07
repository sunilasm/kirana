<?php
namespace WebbyTroops\WishlistAPI\Model\Data;

use WebbyTroops\WishlistAPI\Api\Data\WishlistInterface;
use WebbyTroops\WishlistAPI\Api\Data\WishlistExtensionInterface;

class Wishlist extends \Magento\Framework\Api\AbstractExtensibleObject implements WishlistInterface
{
    /**
     * @inheritDoc
     */
    public function getWishlistId()
    {
        return $this->_get(self::LABEL_WISHLIST_ID);
    }
    
    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->_get(self::LABEL_CUSTOMER_ID);
    }
    
    /**
     * @inheritDoc
     */
    public function getShared()
    {
        return $this->_get(self::LABEL_SHARED);
    }
    
    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::LABEL_UPDATED_AT);
    }
    
    /**
     * @inheritDoc
     */
    public function getWishlistItems()
    {
        return $this->_get(self::LABEL_WISHLIST_ITEMS);
    }
    
    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }
    
    /**
     * @inheritDoc
     */
    public function setWishlistId($wishlistId)
    {
        return $this->setData(self::LABEL_WISHLIST_ID, $wishlistId);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::LABEL_CUSTOMER_ID, $customerId);
    }
    
    /**
     * @inheritDoc
     */
    public function setShared($shared)
    {
        return $this->setData(self::LABEL_SHARED, $shared);
    }
    
    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::LABEL_UPDATED_AT, $updatedAt);
    }
    
    /**
     * @inheritDoc
     */
    public function setWishlistItems($wishlistItems)
    {
        return $this->setData(self::LABEL_WISHLIST_ITEMS, $wishlistItems);
    }
    
    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(WishlistExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
