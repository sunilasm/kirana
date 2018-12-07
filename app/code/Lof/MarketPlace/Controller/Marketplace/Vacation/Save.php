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

namespace Lof\MarketPlace\Controller\Marketplace\Vacation;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Framework\App\Action\Action {
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
     * @var \Lof\MarketPlace\Helper\Data 
     */

    protected $helper;
     /**
     *
     * @var \Lof\MarketPlace\Helper\Url 
     */

    protected $url;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';
    
    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;
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
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Url $url,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
         parent::__construct ($context);

        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->url = $url;
        $this->helper = $helper;
        $this->sender = $sender;
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->_fileSystem = $filesystem;
        $this->resultPageFactory = $resultPageFactory;
    }
    public function getFrontendUrl($route,$params){
        return $this->_frontendUrl->getUrl($route,$params);
    }
    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($route = '', $params = []){
        $this->getResponse()->setRedirect($this->getFrontendUrl($route,$params));
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
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
        
        if ($customerSession->isLoggedIn() && $status == 1) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $data = $this->getRequest()->getPostValue();

            if ($data) {

                 $id= $data['vacation_id'];
                 $vacation = $objectManager->get('Lof\MarketPlace\Model\Vacation')->load($id);;
                 if(!$id) {
                    unset($data['vacation_id']);
                 }   
              
                try {
                    $vacation->setData($data);
                    $vacation->save();
                    $this->messageManager->addSuccess('Save Vacation Success');
                    $this->_redirect('catalog/vacation');
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the vacation.'));
                }   
            } 
        } elseif($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl ( 'lofmarketplace/seller/becomeseller' );
        } else {
            $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
            $this->_redirectUrl ( 'lofmarketplace/seller/login' );
        }
    }

}
