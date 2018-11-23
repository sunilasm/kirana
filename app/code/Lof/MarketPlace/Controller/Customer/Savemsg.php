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
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Controller\Customer;


use Magento\Framework\App\Action\Context;


class Savemsg extends \Magento\Customer\Controller\AbstractAccount {
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;
    
    /**
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     *
     * @var \Lof\MarketPlace\Model\Sender 
     */

    protected $sender;
    /**
     *
     * @var \Lof\MarketPlace\Helper\Data 
     */

    protected $helper;
    /**
     *
     * @param Context $context            
     * @param Magento\Framework\App\Action\Session $customerSession            
     * @param PageFactory $resultPageFactory            
     */
    public function __construct(
        Context $context, 
        \Magento\Customer\Model\Session $customerSession, 
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->helper = $helper;
        $this->sender = $sender;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct ($context);
    }
    
    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute() {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        
        if ($customerSession->isLoggedIn()) {
           $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $data = $this->getRequest()->getPostValue();
            if ($data) {
                $id = $this->getRequest()->getParam('message_id');
                $messageModel = $objectManager->get('Lof\MarketPlace\Model\MessageDetail')->load($id);
                try {
                    $data['seller_send'] =0;
                    $messageModel->setData($data);
                    $messageModel->save();
                    $data['namestore'] = $this->helper->getStoreName();
                    $data['urllogin'] = $this->helper->getStoreUrl('/customer/account/login');
                    if($this->helper->getConfig('email_settings/enable_send_email')) {
                        $this->sender->replyMessage($data);
                    } else {
                        $this->messageManager->addSuccess('send contact success');
                    }
                    $this->_redirect ( $data['currUrl'] );
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the seller.'));
                }   
            } 
        } else {
            $this->messageManager->addNotice ( __ ( 'You must have a account to access' ) );
            $this->_redirect ( 'account/customer/login' );
        }
    }
}
