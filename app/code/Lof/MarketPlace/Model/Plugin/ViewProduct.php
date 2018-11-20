<?php
namespace Lof\MarketPlace\Model\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ViewProduct
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $_redirect;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;


    /**
     * Seller helper
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $sellerHelper;
    /**
     * seller 
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Seller $seller
    ) {
        $this->seller = $seller;
        $this->productRepository = $productRepository;
        $this->sellerHelper = $helper;
    }
    /**
     * Get Not active seller ids
     * 
     * @return array
     */
    public function getNotActiveSellerIds(){
        $collection = $this->seller->getCollection();
        $collection->addFieldToFilter('status',['neq' => 1]);
        return $collection->getAllIds();
    }
     /**
     * If the product have these approval status, it will be displayed in frontend.
     */
    public function getAllowedApprovalStatus(){
        return [
            0,
            2,
        ];
    }
    /**
     * Check if resource for which access is needed has self permissions defined in webapi config.
     *
     * @param \Magento\Framework\Authorization $subject
     * @param callable $proceed
     * @param ModelProduct|int $product
     * @param string $privilege
     *
     * @return bool true If resource permission is self, to allow
     * customer access without further checks in parent method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCanShow(
        \Magento\Catalog\Helper\Product $subject,
        \Closure $proceed,
        $product
    ) {
        
        if (is_int($product)) {
            try {
                $product = $this->productRepository->getById($product);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        } else {
            if (!$product->getId()) {
                return false;
            }
        }

        $notActiveSellerIds = $this->getNotActiveSellerIds();

        if(!in_array($product->getData("approval"),$this->getAllowedApprovalStatus())
        || in_array($product->getData("seller_id"),$notActiveSellerIds)
        ) return false;


        return $proceed($product);
    }
}
