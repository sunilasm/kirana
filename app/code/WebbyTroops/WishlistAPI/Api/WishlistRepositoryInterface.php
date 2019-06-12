<?php
namespace WebbyTroops\WishlistAPI\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * WishlistRepositoryInterface.
 * @api
 */
interface WishlistRepositoryInterface
{
    /**
     * Add wishlist item.
     *
     * @api
     * @param int $customerId
     * @param string $sku
     * @return \WebbyTroops\WishlistAPI\Api\Data\ResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addWishlistItem($customerId, $sku);

    /**
     * Retrieve wishlist.
     *
     * @api
     * @param int $customerId
     * @return \WebbyTroops\WishlistAPI\Api\Data\WishlistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWishlist($customerId);

    /**
     * Remove wishlist item.
     *
     * @api
     * @param int $customerId
     * @param int $itemId
     * @return \WebbyTroops\WishlistAPI\Api\Data\WishlistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeWishlistItem($customerId, $itemId);
    
    /**
     * Update wishlist item.
     *
     * @api
     * @param int $customerId
     * @param int $itemId
     * @param int $qty
     * @param string $description
     * @return \WebbyTroops\WishlistAPI\Api\Data\WishlistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateWishlistItem($customerId, $itemId, $qty, $description = null);
    
    /**
     * Add wishlist item to cart.
     *
     * @api
     * @param int $customerId
     * @param int $itemId
     * @param int $qty
     * @return \WebbyTroops\WishlistAPI\Api\Data\MoveToInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveToCart($customerId, $itemId, $qty);
    
    /**
     * Share wishlist.
     *
     * @api
     * @param int $customerId
     * @param \WebbyTroops\WishlistAPI\Api\Data\ShareWishlistInterface $shareWishlist
     * @return \WebbyTroops\WishlistAPI\Api\Data\WishlistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function shareWishlist($customerId, $shareWishlist);
    
    /**
     * Share wishlist.
     *
     * @api
     * @param int $customerId
     * @param int $itemId
     * @return \WebbyTroops\WishlistAPI\Api\Data\MoveToInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveToWishlist($customerId, $itemId);
}
