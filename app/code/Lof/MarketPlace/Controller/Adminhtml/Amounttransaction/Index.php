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
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Controller\Adminhtml\Amounttransaction;

class Index extends \Lof\MarketPlace\Controller\Adminhtml\Amounttransaction
{
	/**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::amounttransaction');
    }

	/**
	 * Amounttransaction list action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
	 */
	public function execute()
	{

		$resultPage = $this->resultPageFactory->create();

		/**
		 * Set active menu item
		 */
		$resultPage->setActiveMenu("Lof_MarketPlace::amounttransaction");
		$resultPage->getConfig()->getTitle()->prepend(__('Amounts'));

		/**
		 * Add breadcrumb item
		 */
		$resultPage->addBreadcrumb(__('Amounts'),__('Amounts'));
		$resultPage->addBreadcrumb(__('Manage Amount Transaction'),__('Manage Amount Transaction'));

		return $resultPage;
	}
	
}