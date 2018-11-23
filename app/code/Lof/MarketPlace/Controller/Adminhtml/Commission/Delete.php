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
namespace Lof\MarketPlace\Controller\Adminhtml\Commission;

class Delete extends \Lof\MarketPlace\Controller\Adminhtml\Commission
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::commission_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('commission_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('Lof\MarketPlace\Model\Commission');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The seller has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['commission_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a seller to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

}