<?php
namespace WebbyTroops\WishlistAPI\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use WebbyTroops\WishlistAPI\Api\Data\WishlistItemExtensionInterface;

/**
 * WishlistItemsInterface.
 * @api
 */
interface WishlistItemsInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LABEL_ITEM_ID  = 'item_id';
    const LABEL_STORE_ID          = 'store_id';
    const LABEL_ADDED_AT          = 'added_at';
    const LABEL_DESCRIPITION      = 'description';
    const LABEL_QTY               = 'qty';
    const LABEL_PRODUCT           = 'product';
    /**#@-*/

    /**
     * Get Wishlist Item Id
     *
     * @return int
     */
    public function getItemId();

    /**
     * Get Store Id
     *
     * @return int
     */
    public function getStoreId();
    
    /**
     * Get Added At
     *
     * @return string
     */
    public function getAddedAt();

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription();
    
    /**
     * Get Qty
     *
     * @return int
     */
    public function getQty();
    
    /**
     * Get Product
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct();
    
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \WebbyTroops\WishlistAPI\Api\Data\WishlistItemsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set Wishlist Item Id
     *
     * @param int $wishlistItemId
     * @return $this
     */
    public function setItemId($wishlistItemId);

    /**
     * Set Store Id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);
    
    /**
     * Set Added At
     *
     * @param  string $addedAt
     * @return $this
     */
    public function setAddedAt($addedAt);

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Set Qty
     *
     * @param  int $qty
     * @return $this
     */
    public function setQty($qty);
    
    /**
     * Set Product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return $this
     */
    public function setProduct($product);
    
    /**
     * Set an extension attributes object.
     *
     * @param \WebbyTroops\WishlistAPI\Api\Data\WishlistItemsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(WishlistItemExtensionInterface $extensionAttributes);
}
