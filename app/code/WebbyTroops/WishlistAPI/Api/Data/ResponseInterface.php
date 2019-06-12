<?php

namespace WebbyTroops\WishlistAPI\Api\Data;

/**
 * ResponseInterface.
 * @api
 */
interface ResponseInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LABEL_WISHLIST_ID    = 'wishlist_id';
    const LABEL_WISHLIST_ITEM_ID  = 'wishlist_item_id';
    /**#@-*/

    /**
     * Get Wishlist Id
     *
     * @return int
     */
    public function getWishlistId();

    /**
     * Get Wishlist Item Id
     *
     * @return int
     */
    public function getWishlistItemId();

    /**
     * Set Wishlist Id
     *
     * @param int $wishlistId
     * @return $this
     */
    public function setWishlistId($wishlistId);

    /**
     * Set Wishlist Item Id
     *
     * @param int $wishlistItemId
     * @return $this
     */
    public function setWishlistItemId($wishlistItemId);
}
