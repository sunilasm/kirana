<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Controller\Marketplace\Invoice;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;

abstract class View extends \Magento\Framework\App\Action\Action 
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->registry = $registry;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Invoice information page
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
       
         $resultPage = $this->resultPageFactory->create();
     
        return $resultPage;
    }
}
