<?php
namespace WebbyTroops\WishlistAPI\Plugin;

class ProductRepositoryPlugin
{

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    protected $userContext;
    
    /**
     * @var \WebbyTroops\WishlistAPI\Helper\Data
     */
    protected $wishlistHelper;
    
    /**
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \WebbyTroops\WishlistAPI\Helper\Data $wishlistHelper
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \WebbyTroops\WishlistAPI\Helper\Data $wishlistHelper
    ) {
        $this->userContext = $userContext;
        $this->wishlistHelper = $wishlistHelper;
    }
    
    /**
     * Check Product is in wishlist or not
     *
     * @param   \Magento\Catalog\Api\ProductRepositoryInterface $subject,
     * @param   \Magento\Catalog\Api\Data\ProductInterface $product
     * @return  \Magento\Catalog\Api\Data\ProductInterface $product
     */
    public function afterGet(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductInterface $product
    ) {
        $customerId = $this->userContext->getUserId();
        $wishlist = $this->wishlistHelper->checkWishlistProduct($product->getId(), $customerId);
        $isWishListProduct = ($wishlist->hasData()) ? true : false;
        $extensionAttributes = $product->getExtensionAttributes();
        $extensionAttributes->setIsWishlistProduct($isWishListProduct);
        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }
    
    /**
     * Check Product is in wishlist or not
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param \Magento\Framework\Api\SearchResults $searchResult
     * @return \Magento\Framework\Api\SearchResults $searchResult
     */
    public function afterGetList(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Framework\Api\SearchResults $searchResult
    ) {
        $customerId = $this->userContext->getUserId();
        foreach ($searchResult->getItems() as $product) {
            $wishlist = $this->wishlistHelper->checkWishlistProduct($product->getId(), $customerId);
            $isWishListProduct = ($wishlist->hasData()) ? true : false;
            $extensionAttributes = $product->getExtensionAttributes();
            $extensionAttributes->setIsWishlistProduct($isWishListProduct);
            $product->setExtensionAttributes($extensionAttributes);
        }
        return $searchResult;
    }
}
