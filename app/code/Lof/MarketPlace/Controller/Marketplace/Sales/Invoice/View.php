<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Controller\Marketplace\Sales\Invoice;

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
    protected $resultForwardFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->registry = $context->getCoreRegsitry();
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
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
        $resultForward = $this->resultForwardFactory->create();
        if ($this->getRequest()->getParam('invoice_id')) {
            $resultForward->setController('order_invoice')
                ->setParams(['come_from' => 'invoice'])
                ->forward('view');
        } else {
            $resultForward->forward('noroute');
        }
        return $resultForward;
    }
}
