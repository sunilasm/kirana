<?php
namespace WebbyTroops\WishlistAPI\Helper;

use Magento\Framework\Exception\ValidatorException;

class PopulateData extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @param \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \WebbyTroops\WishlistAPI\Api\Data\ResponseInterfaceFactory $responseFactory
     * @param \WebbyTroops\WishlistAPI\Api\Data\WishlistInterfaceFactory $wishlistInterfaceFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \WebbyTroops\WishlistAPI\Api\Data\ResponseInterfaceFactory $responseFactory,
        \WebbyTroops\WishlistAPI\Api\Data\WishlistInterfaceFactory $wishlistInterfaceFactory,
        \WebbyTroops\WishlistAPI\Api\Data\MoveToInterfaceFactory $moveToInterfaceFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    ) {
        $this->responseFactory = $responseFactory;
        $this->wishlistInterfaceFactory = $wishlistInterfaceFactory;
        $this->moveToInterfaceFactory = $moveToInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context);
    }
    
    /**
     * Populate Values
     *
     * @param $object
     * @param $data
     * @param $interface
     * @return $object
     */
    public function populateValues($requestType, $data)
    {
        switch ($requestType) {
            case "add":
                $object = $this->responseFactory->create();
                $interface = \WebbyTroops\WishlistAPI\Api\Data\ResponseInterface::class;
                break;
            case "get":
                $object = $this->wishlistInterfaceFactory->create();
                $interface = \WebbyTroops\WishlistAPI\Api\Data\WishlistInterface::class;
                break;
            case "move-to":
                $object = $this->moveToInterfaceFactory->create();
                $interface = \WebbyTroops\WishlistAPI\Api\Data\MoveToInterface::class;
                break;
        }
        $this->dataObjectHelper->populateWithArray(
            $object,
            $data,
            $interface
        );
        return $object;
    }
}
