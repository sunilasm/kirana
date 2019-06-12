<?php

namespace WebbyTroops\WishlistAPI\Model\Data;

/**
 * Class MoveTo
 */
class MoveTo extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \WebbyTroops\WishlistAPI\Api\Data\MoveToInterface
{
    /**
     * @inheritdoc
     */
    public function getWishlist()
    {
        return $this->getData(self::LABEL_WISHLIST);
    }
    
    /**
     * @inheritdoc
     */
    public function setWishlist($wishlist)
    {
        return $this->setData(self::LABEL_WISHLIST, $wishlist);
    }
    
    /**
     * @inheritdoc
     */
    public function getCart()
    {
        return $this->getData(self::LABEL_CART);
    }
    
    /**
     * @inheritdoc
     */
    public function setCart($cart)
    {
        return $this->setData(self::LABEL_CART, $cart);
    }
}
