<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface as CustomerData;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\ResourceModel\Customer as ResourceCustomer;
use Lof\MarketPlace\Model\Seller;

class Session extends \Magento\Customer\Model\Session
{
    
    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $_sellerFactory;
    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerModel;
    
    
    
    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Session\SidResolverInterface $sidResolver
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Framework\Session\ValidatorInterface $validator
     * @param \Magento\Framework\Session\StorageInterface $storage
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\App\State $appState
     * @param Share $configShare
     * @param \Magento\Framework\Url\Helper\Data $coreUrl
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param ResourceCustomer $customerResource
     * @param CustomerFactory $customerFactory
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param CustomerRepositoryInterface $customerRepository
     * @param GroupManagementInterface $groupManagement
     * @param \Magento\Framework\App\Response\Http $response
     * @throws \Magento\Framework\Exception\SessionException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Framework\Url\Helper\Data $coreUrl,
        \Magento\Customer\Model\Url $customerUrl,
        ResourceCustomer $customerResource,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Http\Context $httpContext,
        CustomerRepositoryInterface $customerRepository,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\App\Response\Http $response,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory
    ) {
        $this->_sellerFactory = $sellerFactory;
        return parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState,
            $configShare,
            $coreUrl,
            $customerUrl,
            $customerResource,
            $customerFactory,
            $urlFactory,
            $session,
            $eventManager,
            $httpContext,
            $customerRepository,
            $groupManagement,
            $response
        );
    }
    
    
    /**
     * Retrieve customer model object
     *
     * @return Seller
     * use getCustomerId() instead
     */
    public function getSeller()
    {
        if ($this->_sellerModel === null) {
            $this->_sellerModel = $this->_sellerFactory->create()->loadByCustomer($this->getCustomer());
        }
    
        return $this->_sellerModel;
    }
}
