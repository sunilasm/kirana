<?php
namespace WebbyTroops\WishlistAPI\Api\Data;

/**
 * MoveToInterface.
 * @api
 */
interface MoveToInterface
{
    const LABEL_WISHLIST = "wishlist";
    const LABEL_CART = "cart";

    /**
     * Get Wishlist
     *
     * @return \WebbyTroops\WishlistAPI\Api\Data\WishlistInterface
     */
    public function getWishlist();
    
    /**
     * Set Wishlist
     *
     * @param \WebbyTroops\WishlistAPI\Api\Data\WishlistInterface $wishlist
     * @return $this
     */
    public function setWishlist($wishlist);

    /**
     * Get Cart
     *
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function getCart();
    
    /**
     * Set Cart
     *
     * @param \Magento\Quote\Api\Data\CartInterface $cart
     * @return $this
     */
    public function setCart($cart);
}
