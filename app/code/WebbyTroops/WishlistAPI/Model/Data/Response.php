<?php
namespace WebbyTroops\WishlistAPI\Model\Data;

use WebbyTroops\WishlistAPI\Api\Data\ResponseInterface;

class Response extends \Magento\Framework\Api\AbstractExtensibleObject implements ResponseInterface
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
    public function getWishlistItemId()
    {
        return $this->_get(self::LABEL_WISHLIST_ITEM_ID);
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
    public function setWishlistItemId($wishlistItemId)
    {
        return $this->setData(self::LABEL_WISHLIST_ITEM_ID, $wishlistItemId);
    }
}
