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
namespace Lof\MarketPlace\Controller\Seller;

use Magento\Framework\App\Action\Context;

class Saveseller extends \Magento\Framework\App\Action\Action {


	/**
    * @var \Lof\MarketPlace\Helper\Data
    */
    protected $_sellerHelper;
    /**
    * @var \Lof\MarketPlace\Model\Sender
    */
    protected $sender;

    /**
     * @param Context    $context   [description]
     * @param \Lof\MarketPlace\Helper\Data   $sellerHelper  [description]
     */
    public function __construct(
        Context $context,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Helper\Data $sellerHelper
        ) {
        parent::__construct($context);
        $this->_sellerHelper = $sellerHelper;
        $this->sender = $sender;
    }
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute() {

        $approvedConditions = $this->getRequest()->getPost('privacy_policy');
        $url                = $this->getRequest()->getPost('url');
        $group              = $this->getRequest()->getPost('group');
        $layout             = "2columns-left";
        $stores = array();
        $stores[] = $this->_sellerHelper->getCurrentStoreId();
         
        $objectManager      = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession    = $objectManager->get('Magento\Customer\Model\Session');
        
        if ($customerSession->isLoggedIn()) {
            if ($approvedConditions == 1) {
                $customerId     = $customerSession->getId ();
                $customerObject = $customerSession->getCustomer ();
                $customerEmail  = $customerObject->getEmail ();
                $customerName   = $customerObject->getName();
                $sellerApproval = $this->_sellerHelper->getConfig('general_settings/seller_approval');
                
                if ($sellerApproval) {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $sellerModel = $objectManager->get('Lof\MarketPlace\Model\Seller');
                    try {
                        $sellerModel->setName($customerName)->setEmail($customerEmail)->setStatus(0)->setGroupId($group)->setCustomerId($customerId)->setStores($stores)->setUrlKey($url)->setPageLayout($layout)->save();
                        $this->_eventManager->dispatch(
                            'controller_action_seller_save_entity_after',
                            ['controller' => $this,'data' => $sellerModel->getData()]
                        );

                        $this->_redirect ('lofmarketplace/seller/becomeseller/approval/0');
                    }  catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addError($e->getMessage());
                         $this->_redirect ('lofmarketplace/seller/becomeseller');
                    } 
                } else {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $sellerModel = $objectManager->get('Lof\MarketPlace\Model\Seller');
                    try {
                        $sellerModel->setName($customerName)->setEmail($customerEmail)->setStatus(1)->setGroupId($group)->setCustomerId($customerId)->setStores($stores)->setUrlKey($url)->setPageLayout($layout)->save();
                         $this->_eventManager->dispatch(
                            'controller_action_seller_save_entity_after',
                            ['controller' => $this,'data' => $sellerModel->getData()]
                        );
                        $this->_redirect ('marketplace/catalog/dashboard');

                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addError($e->getMessage());
                         $this->_redirect ('lofmarketplace/seller/becomeseller');
                    }
                }

                if($this->_sellerHelper->getConfig('email_settings/enable_send_email')) {
                    $data = [];
                    $data['name'] = $customerName;
                    $data['email'] = $customerEmail;
                    $data['group'] = $group;
                    $data['url'] = $sellerModel->getUrl();
                    $this->sender->registerSeller($data);
                }
               
            } 
        } else {
            $resultRedirect = $this->resultRedirectFactory->create ();
            $resultRedirect->setPath('account/login/');
            return $resultRedirect;
        }
        /**
         * Load page layout
         */
        $this->_view->loadLayout ();
        $this->_view->renderLayout ();
    }
}