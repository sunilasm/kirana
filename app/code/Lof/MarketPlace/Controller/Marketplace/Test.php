<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Controller\Vendors;

use Magento\Framework\App\Action\Context;

class Test extends \Magento\Framework\App\Action\Action  
{ 
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


    protected $productBuilder;
    /**
     *
     * @param Context $context            
     * @param Magento\Framework\App\Action\Session $customerSession            
     * @param PageFactory $resultPageFactory            
     */
    public function __construct(
        Context $context, 
        \Magento\Customer\Model\Session $customerSession, 
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->productBuilder = $productBuilder;
        parent::__construct ($context);
    }
    

    /**
     * Create new product page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $product = $this->productBuilder->build($this->getRequest());
    
        $resultPage = $this->resultPageFactory->create();
     
        return $resultPage;
    }
}
