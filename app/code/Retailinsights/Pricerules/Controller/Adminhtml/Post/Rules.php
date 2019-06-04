<?php

namespace Retailinsights\Pricerules\Controller\Adminhtml\Post;

class Rules extends \Magento\Backend\App\Action
{
	protected $resultPageFactory = false;
	protected $postFactory;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

	 public function execute()
   {
       $this->_view->loadLayout();
       $this->_view->getLayout()->initMessages();
       $this->_view->renderLayout();
}

}
