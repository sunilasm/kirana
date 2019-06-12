<?php

namespace WebbyTroops\WishlistAPI\Block\Share\Email;

use Magento\Store\Model\ScopeInterface;

/**
 * @api
 * @since 100.0.2
 */
class Items extends \Magento\Wishlist\Block\AbstractBlock
{
    const CONFIG_PATH = 'catalog/placeholder/small_image_placeholder';

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory
     */
    protected $wishlistCollectionFactory;
    
    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;
    
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;
    
    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $wishlistCollectionFactory
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $wishlistCollectionFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = []
    ) {
        $this->wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->wishlistFactory = $wishlistFactory;
        $this->imageHelper = $imageHelper;
        parent::__construct(
            $context,
            $httpContext,
            $data
        );
    }
    /**
     * @var string
     */
    protected $_template = 'WebbyTroops_WishlistAPI::email/items.phtml';

    /**
     * Retrieve Product View URL
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        $additional['_scope_to_url'] = true;
        return parent::getProductUrl($product, $additional);
    }
    
    /**
     * Get media URL
     *
     * @param string $image
     * @return string
     */
    public function getMedia($image)
    {
        if ($image) {
            $imageUrl = $this->getUrl('pub/media/catalog/product'). $image;
        } else {
            $path = $this->_scopeConfig->getValue(self::CONFIG_PATH, ScopeInterface::SCOPE_STORE);
            $imageUrl = $this->getUrl('pub/media/catalog/product').$path;
        }
        return $imageUrl;
    }

    /**
     * Check whether wishlist item has description
     *
     * @param \Magento\Wishlist\Model\Item $item
     * @return bool
     */
    public function hasDescription($item)
    {
        $hasDescription = parent::hasDescription($item);
        if ($hasDescription) {
            return $item->getDescription() !== $this->_wishlistHelper->defaultCommentString();
        }
        return $hasDescription;
    }
    
    /**
     * Get wishlist items
     *
     * @return \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
    public function getWishlistItems()
    {
        return  $this->wishlistCollectionFactory->create()->addFieldToFilter('wishlist_id', 2)
                ->addStoreFilter(
                    $this->wishlistFactory->create()->getSharedStoreIds()
                )->setVisibilityFilter();
    }
    
    /**
     * Get wishlist items count
     *
     * @return int
     */
    public function getWishlistItemsCount()
    {
        return $this->getWishlistItems()->getSize();
    }
}
