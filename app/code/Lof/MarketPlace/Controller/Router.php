<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Url;

class Router implements RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Response
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * Seller Factory
     *
     * @var \Lof\MarketPlace\Model\Seller $sellerCollection
     */
    protected $_sellerCollection;

    /**
     * Seller Factory
     *
     * @var \Lof\MarketPlace\Model\Group $groupCollection
     */
    protected $_groupCollection;

    /**
     * Seller Helper
     */
    protected $_sellerHelper;

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ActionFactory          $actionFactory   
     * @param ResponseInterface      $response        
     * @param ManagerInterface       $eventManager    
     * @param \Lof\MarketPlace\Model\Seller $sellerCollection 
     * @param \Lof\MarketPlace\Model\Group $groupCollection 
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper     
     * @param StoreManagerInterface  $storeManager    
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        ManagerInterface $eventManager,
        \Lof\MarketPlace\Model\Seller $sellerCollection,
        \Lof\MarketPlace\Model\Group $groupCollection,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        StoreManagerInterface $storeManager
        )
    {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->response = $response;
        $this->_sellerHelper = $sellerHelper;
        $this->_sellerCollection = $sellerCollection;
        $this->_groupCollection = $groupCollection;
        $this->storeManager = $storeManager;
    }
    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $_sellerHelper = $this->_sellerHelper;
        if (!$this->dispatched) {
            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;
            /** @var Object $condition */
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'lof_marketplace_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
                );
            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                    );
            }
            if (!$condition->getContinue()) {
                return null;
            }
            $route = $_sellerHelper->getConfig('general_settings/route');
      
            if( $route !='' && $urlKey == $route )
            {
                $request->setModuleName('lofmarketplace')
                ->setControllerName('index')
                ->setActionName('index');
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }
            $url_prefix = $_sellerHelper->getConfig('general_settings/url_prefix');
            $url_suffix = $_sellerHelper->getConfig('general_settings/url_suffix');

            $identifiers = explode('/',$urlKey);
            //Check Group Url
            if( (count($identifiers) == 2 && $identifiers[0] == $url_prefix && strpos($identifiers[1], $url_suffix)) || (trim($url_prefix) == '' && count($identifiers) == 1)){
                $sellerUrl = '';
                if(trim($url_prefix) == '' && count($identifiers) == 1){
                    $sellerUrl = str_replace($url_suffix, '', $identifiers[0]);
                }
                if(count($identifiers) == 2){
                    $sellerUrl = str_replace($url_suffix, '', $identifiers[1]);
                }
                $group = $this->_groupCollection->getCollection()
                ->addFieldToFilter('status', array('eq' => 1))
                ->addFieldToFilter('url_key', array('eq' => $sellerUrl))
                ->getFirstItem();

                if($group && $group->getId()){
                    $request->setModuleName('lofmarketplace')
                    ->setControllerName('group')
                    ->setActionName('view')
                    ->setParam('group_id', $group->getId());
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                    $request->setDispatched(true);
                    $this->dispatched = true;
                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                        );
                }
            }

            // Check Seller Url Key
            if( (count($identifiers) == 2 && $identifiers[0] == $url_prefix && (strpos($identifiers[1], $url_suffix) || !$url_suffix) ) || (trim($url_prefix) == '' && count($identifiers) == 1)){

                if(count($identifiers) == 2){
                    $sellerUrl = str_replace($url_suffix, '', $identifiers[1]);
                }
                if(trim($url_prefix) == '' && count($identifiers) == 1){
                    $sellerUrl = str_replace($url_suffix, '', $identifiers[0]);
                }

                $seller = $this->_sellerCollection->getCollection()
                ->addFieldToFilter('url_key', array('eq' => $sellerUrl))
                ->getFirstItem();
                
                $seller_stores = [];
                if($seller) {
                    $seller_stores = $seller->getStoreId();
                    if($seller_stores && !is_array($seller_stores)) {
                        if(false !== strpos($seller_stores, ",")) {
                            $seller_stores = explode(",",$seller_stores);
                        } else {
                            $seller_stores = [(int)$seller_stores];
                        }
                    }
                }
                if($seller && $seller->getId() && (in_array($this->storeManager->getStore()->getId(), $seller_stores) || in_array(0,$seller_stores)) ){
                    $request->setModuleName('lofmarketplace')
                    ->setControllerName('seller')
                    ->setActionName('view')
                    ->setParam('seller_id', $seller->getId());
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                    $request->setDispatched(true);
                    $this->dispatched = true;
                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                        );
                }
            }
        }
    }
}