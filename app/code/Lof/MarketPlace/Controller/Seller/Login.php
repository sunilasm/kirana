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


class Login extends \Magento\Customer\Controller\AbstractAccount {
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
     * @param Context $context            
     * @param Magento\Framework\App\Action\Session $customerSession            
     * @param PageFactory $resultPageFactory            
     */
    public function __construct(
        Context $context, 
        \Magento\Customer\Model\Session $customerSession, 
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct ($context);
    }
    
    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute() {
        if ($this->session->isLoggedIn ()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect*/
            $resultRedirect = $this->resultRedirectFactory->create ();
            $resultRedirect->setPath ( 'marketplace/catalog/dashboard' );
            return $resultRedirect;
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create ();
        $resultPage->setHeader ( 'Login-Required', 'true' );
        return $resultPage;
    }
}
