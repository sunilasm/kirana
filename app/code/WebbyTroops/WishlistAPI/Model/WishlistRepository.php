<?php
namespace WebbyTroops\WishlistAPI\Model;

use WebbyTroops\WishlistAPI\Api\WishlistRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Layout as ResultLayout;

class WishlistRepository implements WishlistRepositoryInterface
{
    
    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;
    
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    
    /**
     * @var \Magento\Wishlist\Model\ItemFactory
     */
    protected $itemFactory;
    
    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;
    
    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $cartItemFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $cartItemRepository;
    
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;
    
    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $customerHelperView;
    
    /**
     * @var \WebbyTroops\WishlistAPI\Model\Share\Validate
     */
    protected $validator;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;
    
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    
    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $cartItem;
    
    /**
     * @var \WebbyTroops\WishlistAPI\Helper\PopulateData
     */
    protected $populateDataHelper;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;
    
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;
    
    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmultion;
    
    /**
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemFactory
     * @param \Magento\Quote\Api\CartItemRepositoryInterface $cartItemRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Helper\View $customerHelperView
     * @param \WebbyTroops\WishlistAPI\Model\Share\Validate $validator
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Quote\Model\Quote\ItemFactory $cartItem
     * @param \WebbyTroops\WishlistAPI\Helper\PopulateData $populateDataHelper
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     */
    public function __construct(
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Quote\Api\CartManagementInterface $cartManagement,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemFactory,
        \Magento\Quote\Api\CartItemRepositoryInterface $cartItemRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\View $customerHelperView,
        \WebbyTroops\WishlistAPI\Model\Share\Validate $validator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Quote\Model\Quote\ItemFactory $cartItem,
        \WebbyTroops\WishlistAPI\Helper\PopulateData $populateDataHelper,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Store\Model\App\Emulation $appEmulation
    ) {
        $this->productRepository = $productRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->itemFactory = $itemFactory;
        $this->cartManagement = $cartManagement;
        $this->cartItemFactory = $cartItemFactory;
        $this->cartItemRepository = $cartItemRepository;
        $this->customerRepository = $customerRepository;
        $this->customerHelperView = $customerHelperView;
        $this->validator = $validator;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->cartItem = $cartItem;
        $this->populateDataHelper = $populateDataHelper;
        $this->url = $url;
        $this->layout = $layout;
        $this->appEmultion = $appEmulation;
    }

    /**
     * @inheritDoc
     */
    public function addWishlistItem($customerId, $sku)
    {
        if ($sku == null) {
            throw new LocalizedException(__('Invalid product, Please select a valid product'));
        }
        try {
            $product = $this->productRepository->get($sku);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }
        if (!$product || !$product->isVisibleInCatalog()) {
            throw new LocalizedException(__('We can\'t specify a product.'));
        }
        try {
            $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);
            $item = $wishlist->addNewItem($product);
            $wishlist->save();
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }
        $response = [
            'wishlist_id' =>  $wishlist->getId(),
            'wishlist_item_id'  => $item->getId()
        ];
        
        return $this->populateDataHelper->populateValues('add', $response);
    }
    
    /**
     * @inheritDoc
     */
    public function getWishlist($customerId)
    {
        try {
            $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }
        $wishlistItems = $wishlist->getItemCollection();
        $items = [];
        foreach ($wishlistItems as $wishlistItem) {
            $product = $this->productRepository->getById($wishlistItem->getProductId());
            $items[] = [
                'item_id' => $wishlistItem->getId(),
                'store_id' => $wishlistItem->getStoreId(),
                'added_at' => $wishlistItem->getAddedAt(),
                'description' => $wishlistItem->getDescription(),
                'qty' => $wishlistItem->getQty(),
                'product' => $product
            ];
        }
        
        $response = [
            'wishlist_id' => $wishlist->getId(),
            'customer_id' => $wishlist->getCustomerId(),
            'shared' => $wishlist->getShared(),
            'wishlist_items' => $items,
            'updated_at' => $wishlist->getUpdatedAt()
        ];
        return $this->populateDataHelper->populateValues('get', $response);
    }
    
    /**
     * @inheritDoc
     */
    public function removeWishlistItem($customerId, $itemId)
    {
        if ($itemId == null) {
            throw new LocalizedException(__('Please select a item'));
        }
        $item = $this->itemFactory->create()->load($itemId);
        if (!$item->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('The requested Wish List Item doesn\'t exist.')
            );
        }
        $wishlistId = $item->getWishlistId();
        $wishlist = $this->wishlistFactory->create();

        if ($wishlistId) {
            $wishlist->load($wishlistId);
        } elseif ($customerId) {
            $wishlist->loadByCustomerId($customerId, true);
        }
        if (!$wishlist) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('The requested Wish List doesn\'t exist.')
            );
        }
        if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('The requested Wish List doesn\'t exist.')
            );
        }
        try {
            $item->delete();
            $wishlist->save();
        } catch (LocalizedException $e) {
            throw new LocalizedException(
                __('Something went wrong, please try again later.')
            );
        }
         
        return $this->getWishlist($customerId);
    }
    
    /**
     * @inheritDoc
     */
    public function updateWishlistItem($customerId, $itemId, $qty, $description = null)
    {
        try {
            $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }
        $item = $wishlist->getItem($itemId);
        $isWishlistUpdated = false;
        if ($qty == 0) {
            try {
                $item->delete();
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\Exception(
                    __('Can\'t delete wish list item.')
                );
            }
        } else {
            $isWishlistUpdated = true;
            $item->setQty($qty);
            $item->setDescription($description);
            $item->save();
        }
        try {
            $wishlist->save();
        } catch (\Magento\Framework\Exception\Exception $e) {
            throw new \Magento\Framework\Exception\Exception(
                __('Can\'t update wish list.')
            );
        }
         
        return $this->getWishlist($customerId);
    }

    /**
     * @inheritDoc
     */
    public function moveToCart($customerId, $itemId, $qty)
    {
        $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);
        $item = $wishlist->getItem($itemId);
        $isMoveSuccess = false;
        if ($item) {
            $product = $this->productRepository->getById($item->getProductId());
            $quoteId = $this->cartManagement->createEmptyCartForCustomer($customerId);
            $cartItemObject = $this->cartItemFactory->create();
            $cartItemObject->setQty($qty);
            $cartItemObject->setSku($product->getSku());
            $cartItemObject->setQuoteId($quoteId);
            try {
                $addedItem = $this->cartItemRepository->save($cartItemObject);
            } catch (\Magento\Framework\Exception\Exception $e) {
                throw new \Magento\Framework\Exception\Exception(
                    __('Unable to add this item in cart. ')
                );
            }
            $item->delete();
            $isMoveSuccess = true;
        } else {
            throw new NoSuchEntityException(__('No such item in wishlist'));
        }
        if ($isMoveSuccess) {
            $wishlist = $this->getWishlist($customerId);
            $cart = $this->cartManagement->getCartForCustomer($customerId);
            $response = [
              'cart' => $cart,
              'wishlist' => $wishlist
            ];
            return $this->populateDataHelper->populateValues('move-to', $response);
        } else {
            throw new LocalizedException(__('Can\'t move this product into cart'));
        }
    }
    
    /**
     * @inheritDoc
     */
    public function shareWishlist($customerId, $shareWishlist)
    {
        $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);
        $items = $wishlist->getItemCollection();
        if ($items->getSize() == 0) {
            throw new LocalizedException(
                __('Wishlist has no item right now.')
            );
        }
        $emails = $shareWishlist->getEmails();
        $message = (string)$shareWishlist->getComments();
        $validatedData = $this->validator->validateData($emails, $wishlist, $message);
        $emailsArray = $validatedData['emails'];
        $message = $validatedData['message'];
         
        $sent = 0;

        try {
            $customer = $this->customerRepository->getById($customerId);
            $customerName = $this->customerHelperView->getCustomerName($customer);
            
            $emailsArray = array_unique($emailsArray);
            $sharingCode = $wishlist->getSharingCode();
             
            try {
                foreach ($emailsArray as $email) {
                    $transport = $this->transportBuilder->setTemplateIdentifier(
                        $this->scopeConfig->getValue(
                            'wishlist/email/email_template',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getStoreId(),
                        ]
                    )->setTemplateVars(
                        [
                            'customer' => $customer,
                            'customerName' => $customerName,
                            'salable' => $wishlist->isSalable() ? 'yes' : '',
                            'items' => $this->getWishlistItems(),
                            'viewOnSiteLink' => $this->url->getUrl('*/shared/index', ['code' => $sharingCode]),
                            'message' => $message,
                            'store' => $this->storeManager->getStore(),
                        ]
                    )->setFrom(
                        $this->scopeConfig->getValue(
                            'wishlist/email/email_identity',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $email
                    )->getTransport();
                    
                    $transport->sendMessage();

                    $sent++;
                }
            } catch (LocalizedException $e) {
                $wishlist->setShared($wishlist->getShared() + $sent);
                $wishlist->save();
                throw $e;
            }
            $wishlist->setShared($wishlist->getShared() + $sent);
            $wishlist->save();

            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
        }
        return $this->getWishlist($customerId);
    }
    
    /**
     * @inheritDoc
     */
    public function moveToWishlist($customerId, $itemId)
    {
        $isMoveSuccess = false;
        try {
            $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId, true);
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('We can\'t create the Wishlist right now.')
            );
        }
        try {
            $item = $this->cartItem->create()->load($itemId);
            if (!$item) {
                throw new LocalizedException(
                    __('The requested cart item doesn\'t exist.')
                );
            }

            $productId = $item->getProductId();
            $buyRequest = $item->getBuyRequest();
            $wishlist->addNewItem($productId, $buyRequest);
            $this->cartItemRepository->deleteById($item->getQuoteId(), $itemId);
            $wishlist->save();
            $isMoveSuccess = true;
        } catch (LocalizedException $e) {
            throw new LocalizedException(
                __($e->getMessage())
            );
        } catch (LocalizedException $e) {
            throw new LocalizedException(
                __('The requested Wish List Item doesn\'t exist.'. $e->getMessage())
            );
        }
        if ($isMoveSuccess) {
            $wishlist = $this->getWishlist($customerId);
            $cart = $this->cartManagement->getCartForCustomer($customerId);
            $response = [
              'cart' => $cart,
              'wishlist' => $wishlist
            ];
            return $this->populateDataHelper->populateValues('move-to', $response);
        } else {
            throw new LocalizedException(__('Can\'t move this product into wishlist'));
        }
    }

    /**
     * Retrieve wishlist items content (html)
     *
     * @return string
     */
    protected function getWishlistItems()
    {
        $block = $this->layout->createBlock("WebbyTroops\WishlistAPI\Block\Share\Email\Items");
        return $block->setArea(\Magento\Framework\App\Area::AREA_FRONTEND)
                ->setIsSecureMode(true)
                ->toHtml();
    }
}
