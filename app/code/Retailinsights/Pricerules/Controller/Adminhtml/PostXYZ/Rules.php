<?php

namespace Retailinsights\Pricerules\Controller\Adminhtml\PostXYZ;

class Rules extends \Magento\Backend\App\Action
{
	protected $resultPageFactory = false;
	protected $PostXYZFactory;

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
	// $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	// $model = $objectManager->create('\Retailinsights\Pricerules\Model\CatalogRuleRepository');
	// $post_id = $this->getRequest()->getParam('post_id');
	// $temp=$model->getBuyXYZ($post_id);
	
	 //$rule_condition=$this->getRequest()->getParam('rule_condition');   

       $this->_view->loadLayout();
	   $this->_view->getLayout()->initMessages();
	 //  $block = $this->_view->getLayout()->createBlock('Retailinsights\Pricerules\Block\XYZblock', 'XYZblock',['postid'=>"hello"]);

       $this->_view->renderLayout();
}


}
