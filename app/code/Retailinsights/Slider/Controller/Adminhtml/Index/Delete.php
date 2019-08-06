<?php

namespace Retailinsights\Slider\Controller\Adminhtml\Index;
use Retailinsights\Slider\Model\postFactory;

class Delete extends \Magento\Backend\App\Action
{
    protected $_resultPageFactory;
    protected $_jsonFactory;
    protected $_postFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Retailinsights\Slider\Model\PostFactory $postFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_jsonFactory = $jsonFactory;
        $this->_postFactory = $postFactory;
    }

    public function execute()
    {
        if ($this->getRequest()->isAjax()){
            $imgId  = $this->getRequest()->getParam('id');
            try {
                $model = $this->_postFactory->create();
                $model->load($imgId);
                $model->delete();
                $this->messageManager->addSuccess(__('Image successfully deleted'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
    }


}
