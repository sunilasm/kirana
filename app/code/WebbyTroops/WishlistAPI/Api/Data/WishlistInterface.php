<?php
namespace WebbyTroops\WishlistAPI\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use WebbyTroops\WishlistAPI\Api\Data\WishlistExtensionInterface;

/**
 * WishlistInterface.
 * @api
 */
interface WishlistInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LABEL_WISHLIST_ID        = 'wishlist_id';
    const LABEL_CUSTOMER_ID        = 'customer_id';
    const LABEL_SHARED             = 'shared';
    const LABEL_WISHLIST_ITEMS     = 'wishlist_items';
    const LABEL_UPDATED_AT         = 'updated_at';
    /**#@-*/

    /**
     * Get Wishlist Id
     *
     * @return int
     */
    public function getWishlistId();

    /**
     * Get Customer Id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Get Shared
     *
     * @return bool
     */
    public function getShared();

    /**
     * Get Wishlist Items
     *
     * @return \WebbyTroops\WishlistAPI\Api\Data\WishlistItemsInterface[]
     */
    public function getWishlistItems();

    /**
     * Get Updated At
     *
     * @return string
     */
    public function getUpdatedAt();
    
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \WebbyTroops\WishlistAPI\Api\Data\WishlistExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set Wishlist Id
     *
     * @param int $wishlistId
     * @return $this
     */
    public function setWishlistId($wishlistId);

    /**
     * Set Customer Id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Set Shared
     *
     * @param bool $shared
     * @return $this
     */
    public function setShared($shared);

    /**
     * Set Wishlist Items
     *
     * @param \WebbyTroops\WishlistAPI\Api\Data\WishlistItemsInterface[] $wishlistItems
     * @return $this
     */
    public function setWishlistItems($wishlistItems);

    /**
     * Set Updated At
     *
     * @param  string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
    
    /**
     * Set an extension attributes object.
     *
     * @param \WebbyTroops\WishlistAPI\Api\Data\WishlistExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(WishlistExtensionInterface $extensionAttributes);
}
