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

namespace Lof\MarketPlace\Controller\Marketplace\Saveprofile;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class Index extends \Magento\Customer\Controller\AbstractAccount  {
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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
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
        $this->messageManager->addSuccess('Save Profile Success', 'demo');
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();
      
        if ($customerSession->isLoggedIn() && $status == 1) {
            // $this->_view->loadLayout();
            // $this->_view->renderLayout();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            

            $data = $this->getRequest()->getPostValue();
  
            if ($data) {
                $id = $this->getRequest()->getParam('seller_id');
                $sellerModel = $objectManager->get('Lof\MarketPlace\Model\Seller')->load($id);

            
                
                try {
                    /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
                    $mediaFolder = 'lof/seller/';
                    $path = $mediaDirectory->getAbsolutePath($mediaFolder);
                    
                    // Delete, Upload Image
                   // $imagePath = $mediaDirectory->getAbsolutePath($_FILES['image']['name']);

                    if(isset($data['image']['delete']) && file_exists($path)){
                        unlink($path);
                        $data['image'] = '';
                    }

                    if(isset($data['image']) && is_array($data['image'])){
                        unset($data['image']);
                    }
                   
                    if($image = $this->uploadImage('image')){
                        
                        $data['image'] = $image;
                    }
                    
                    // Delete, Upload Thumbnail
                    //$thumbnailPath = $mediaDirectory->getAbsolutePath($_FILES['thumbnail']['name']);

                    if(isset($data['thumbnail']['delete']) && file_exists($path)){
                        unlink($path);
                        $data['thumbnail'] = '';
                    }
                    if(isset($data['thumbnail']) && is_array($data['thumbnail'])){
                        unset($data['thumbnail']);
                    }
                    if($thumbnail = $this->uploadImage('thumbnail')){
                        $data['thumbnail'] = $thumbnail;
                    }
                    
                    $sellerModel->setData($data);
                    $sellerModel->save();
                    $this->messageManager->addSuccess('Save Profile Success');
                    $this->_redirect ( 'catalog/seller/index/status/1' );
                   } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the seller.'));
                }   
            } 
        } elseif($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirect ( 'lofmarketplace/seller/becomeseller' );
        } else {
            $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
            $this->_redirect ( 'lofmarketplace/seller/login' );
        }
    }



    public function uploadImage($fieldId = 'image')
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name']!='') 
        {

            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $fieldId)
                );
            $path = $this->_fileSystem->getDirectoryRead(
                DirectoryList::MEDIA
                )->getAbsolutePath(
                'catalog/category/'
                );

                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
                $mediaFolder = 'lof/seller/';
               
                try {
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder)
                        );

                    return $mediaFolder.$result['name'];
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/edit', ['seller_id' => $this->getRequest()->getParam('seller_id')]);
                }
        }
        return;
    }
}
