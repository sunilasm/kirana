<?php

namespace Asm\Kiranaproducts\Controller\Adminhtml\kiranaproducts;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPagee;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Asm_Kiranaproducts::kiranaproducts');
        $resultPage->addBreadcrumb(__('Asm'), __('Asm'));
        $resultPage->addBreadcrumb(__('Export'), __('Export Kirana Products'));
        $resultPage->getConfig()->getTitle()->prepend(__('Exort Kirana Products'));

        return $resultPage;
    }
}
?>