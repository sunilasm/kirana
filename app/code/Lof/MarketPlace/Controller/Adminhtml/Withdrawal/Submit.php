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
namespace Lof\MarketPlace\Controller\Adminhtml\Withdrawal;
use Magento\Framework\Controller\ResultFactory;
class Submit extends \Lof\MarketPlace\Controller\Adminhtml\Withdrawal
{
	/**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::withdrawal');
    }

	/**
	 * Withdrawal list action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
	 */
	public function execute()
	{

		$resultPage = $this->resultPageFactory->create();

		/**
		 * Set active menu item
		 */
		$resultPage->setActiveMenu("Lof_MarketPlace::withdrawal");
		$resultPage->getConfig()->getTitle()->prepend(__('Withdrawals'));

		/**
		 * Add breadcrumb item
		 */
		$resultPage->addBreadcrumb(__('Withdrawals'),__('Withdrawals'));
		$resultPage->addBreadcrumb(__('Manage Withdrawals'),__('Manage Withdrawals'));
		$data = $this->getRequest()->getParams();

		$collection = $this->_objectManager->create(
            'Lof\MarketPlace\Model\Withdrawal'
        )->load( $data['withdrawal_id'],'withdrawal_id');
        $collection->setStatus($data['withdrawal_status'])->setAdminMessage($data['note'])->save ();
        if($data['withdrawal_status'] == 1) {

        	$amount = $this->_objectManager->create('Lof\MarketPlace\Model\Amount');
        	$amount->load($data['sellerid'], 'seller_id');

    		$withdrawal = - $this->toInt($data['amount']);
    		$description = __('	Withdraw Money : Amount ').$data['amount'].', Fee '.$data['fee'].', Net Amount '.$data['netamount'];

        	$this->updateSellerAmount ( $data['sellerid'],$withdrawal,$description);

        }
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
	}
	function toInt($str)
	{
	    return (int)preg_replace("/\..+$/i", "", preg_replace("/[^0-9\.]/i", "", $str));
	}
	 /**
     * Update seller amount
     *
     * @param int $updateSellerId            
     * @param double $totalAmount            
     *
     * @return void
     */
    public function updateSellerAmount($updateSellerId, $totalAmount,$description) {
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Load seller by seller id
         */
        $sellerModel = $objectManager->get ( 'Lof\MarketPlace\Model\Amount' );

        $amount_transaction = $objectManager->get('Lof\MarketPlace\Model\Amounttransaction');

        $date = $objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime');

        $sellerDetails = $sellerModel->load ( $updateSellerId, 'seller_id' );
        /**
         * Get remaining amount
         */
        $remainingAmount = $sellerDetails->getAmount ();
        /**
         * Total remaining amount
         */
        $totalRemainingAmount = $remainingAmount + $totalAmount;
        /**
         * Set total remaining amount
         */
        $amount_transaction->setSellerId($updateSellerId)->setAmount($totalAmount)->setBalance($totalRemainingAmount)->setDescription($description)->setUpdatedAt($date->gmtDate());

        $sellerDetails->setSellerId($updateSellerId)->setAmount($totalRemainingAmount);

        
        /**
         * Save remaining amount
         */
        $sellerDetails->save ();

        $amount_transaction->save();
    }
	
}