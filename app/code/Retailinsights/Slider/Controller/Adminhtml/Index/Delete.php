<?php

namespace Retailinsights\Slider\Controller\Adminhtml\Index;

class Delete extends \Magento\Backend\App\Action
{
	protected $resultPageFactory;
	protected $_jsonFactory;
	protected $_postFactory;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Retailinsights\Slider\Model\postFactory $PostFactory
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
		$this->_jsonFactory = $jsonFactory;
		$this->_postFactory = $postFactory;
	}

	public function execute()
	{ echo 'dddd';die;
		if ($this->getRequest()->isAjax()) 
        {
            $imgId  = $this->getRequest()->getParam('id');
            echo $imgId; exit;
            try {
	            $model = $this->_postFactory->create()->getCollection();
	            $model->load($imgId);
	            $model->delete();
	            $this->messageManager->addSuccess(__('Image successfully deleted'));
	        } catch (\Exception $e) {
	            $this->messageManager->addError($e->getMessage());
	        }
	}
}


}
?>