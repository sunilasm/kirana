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
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Controller\Seller;


use Magento\Framework\App\Action\Context;


class Dashboard extends \Magento\Customer\Controller\AbstractAccount {
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;
    
    /**
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     *
     * @var \Lof\MarketPlace\Model\SellerFactory 
     */
    protected $sellerFactory;
    /**
     *
     * @param Context $context            
     * @param Magento\Framework\App\Action\Session $customerSession            
     * @param PageFactory $resultPageFactory            
     */
    public function __construct(
        Context $context, 
        \Magento\Customer\Model\Session $customerSession, 
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct ($context);
    }
    
    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute() {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();
        
        if ($customerSession->isLoggedIn() && $status == 1) {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } elseif($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirect ( 'lofmarketplace/seller/becomeseller' );
        } else {
            $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
            $this->_redirect ( 'lofmarketplace/seller/login' );
        }
    }
}
