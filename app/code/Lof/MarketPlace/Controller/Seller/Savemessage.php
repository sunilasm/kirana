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

namespace Lof\MarketPlace\Controller\Seller;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class Savemessage extends \Magento\Framework\App\Action\Action  {
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
     * @var \Lof\MarketPlace\Model\SellerFactory 
     */

    protected $sellerFactory;
     /**
     *
     * @var \Lof\MarketPlace\Model\Sender 
     */
    protected $sender;
     /**
     *
     * @var \Lof\MarketPlace\Data\Data 
     */
    protected $helper;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;
    /**
     *
     * @param Context $context            
     * @param Magento\Framework\App\Action\Session $customerSession            
     * @param PageFactory $resultPageFactory            
     */
    public function __construct(
        Context $context, 
        \Magento\Customer\Model\Session $customerSession, 
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->helper = $helper;
        $this->sender = $sender;
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->_fileSystem = $filesystem;
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
        $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();

            // $this->_view->loadLayout();
            // $this->_view->renderLayout();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            

            $data = $this->getRequest()->getPostValue();
  
            if ($data) {
                $data['status'] =1;
                if(!isset($data['owner_id'])) {
                    $data['owner_id'] = $data['seller_id'];
                }
                $messageModel = $objectManager->get('Lof\MarketPlace\Model\Message');
                try {

                    $messageModel->setData($data);
                    $messageModel->save();
                    $data['namestore'] = $this->helper->getStoreName();
                    $data['urllogin'] = $this->helper->getStoreUrl('/customer/account/login');
                    if($this->helper->getConfig('email_settings/enable_send_email')) {
                        $this->sender->newMessage($data);
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
       
    }

}
