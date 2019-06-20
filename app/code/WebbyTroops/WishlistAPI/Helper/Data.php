<?php
namespace WebbyTroops\WishlistAPI\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;
    
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->wishlistFactory = $wishlistFactory;
        $this->resource = $resource;
        parent::__construct($context);
    }
    
    public function checkWishlistProduct($productId, $customerId)
    {
        $wishlistItemTable = $this->resource->getTableName('wishlist_item');
        $wishlistCollection = $this->wishlistFactory->create()->getCollection()
                        ->addFieldToFilter(
                            'main_table.customer_id',
                            $customerId
                        )->addFieldToFilter(
                            'wli.product_id',
                            $productId
                        );
        $wishlistCollection->getSelect()
                ->joinLeft(
                    ['wli' => $wishlistItemTable],
                    'main_table.wishlist_id = wli.wishlist_id'
                );
        
        return $wishlistCollection->getFirstItem();
    }
}
