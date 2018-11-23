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
namespace Lof\MarketPlace\Controller\Seller;


use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\UrlFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;

class CreatePost extends \Magento\Customer\Controller\AbstractAccount {
    /** @var AccountManagementInterface */
    protected $accountManagement;
    /** @var FormFactory */
    protected $formFactory;
    /** @var Escaper */
    protected $escaper;
    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;
    /** @var DataObjectHelper  */
    protected $dataObjectHelper;
    /**
     *
     * @var Session
     */
    protected $session;
    /**
     *
     * @var AccountRedirect
     */
    private $accountRedirect;

    /**
    * @var \Lof\MarketPlace\Helper\Data
    */
    protected $_sellerHelper;
    /**
    * @var \Lof\MarketPlace\Model\Sender
    */
    protected $sender;

    /**
     *
     * @param Context $context            
     * @param Session $customerSession            
     * @param AccountManagementInterface $accountManagement            
     * @param UrlFactory $urlFactory            
     * @param Registration $registration            
     * @param Escaper $escaper            
     * @param DataObjectHelper $dataObjectHelper            
     * @param AccountRedirect $accountRedirect
     *            @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context, 
        Session $customerSession, 
        AccountManagementInterface $accountManagement, 
        UrlFactory $urlFactory, 
        Escaper $escaper, 
        DataObjectHelper $dataObjectHelper, 
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        AccountRedirect $accountRedirect) 
    {
        $this->_sellerHelper = $sellerHelper;
         $this->sender = $sender;
        $this->session = $customerSession;
        $this->accountManagement = $accountManagement;
        $this->escaper = $escaper;
        $this->urlModel = $urlFactory->create ();
        $this->dataObjectHelper = $dataObjectHelper;
        $this->accountRedirect = $accountRedirect;
        parent::__construct ( $context );
    }
    
    /**
     * Add address to customer during create account
     *
     * @return AddressInterface|null
     */
    protected function extractAddress() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        
        if (! $this->getRequest ()->getPost ( 'create_address' )) {
            return null;
        }
        
        $addressForm = $objectModelManager->get ( 'Magento\Customer\Model\Metadata\FormFactory' )->create ( 'customer_address', 'customer_register_address' );
        $allowedAttributes = $addressForm->getAllowedAttributes ();
        
        $addressData = [ ];
        
        $regionDataObject = $objectModelManager->get ( 'Magento\Customer\Api\Data\RegionInterfaceFactory' )->create ();
        foreach ( $allowedAttributes as $attribute ) {
            $attributeCode = $attribute->getAttributeCode ();
            $value = $this->getRequest ()->getParam ( $attributeCode );
            if ($value === null) {
                continue;
            }
            switch ($attributeCode) {
                case 'region_id' :
                    $regionDataObject->setRegionId ( $value );
                    break;
                case 'region' :
                    $regionDataObject->setRegion ( $value );
                    break;
                default :
                    $addressData [$attributeCode] = $value;
            }
        }
        $addressDataObject = $objectModelManager->get ( 'Magento\Customer\Api\Data\AddressInterfaceFactory' )->create ();
        $this->dataObjectHelper->populateWithArray ( $addressDataObject, $addressData, '\Magento\Customer\Api\Data\AddressInterface' );
        $addressDataObject->setRegion ( $regionDataObject );
        
        $addressDataObject->setIsDefaultBilling ( $this->getRequest ()->getParam ( 'default_billing', false ) )->setIsDefaultShipping ( $this->getRequest ()->getParam ( 'default_shipping', false ) );
        return $addressDataObject;
    }
    
    /**
     * Create customer account action
     *
     * @return void @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute() {
        $resultRedirectFlag = 0;
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create ();
        
        if ($this->session->isLoggedIn () || ! $objectModelManager->get ( 'Magento\Customer\Model\Registration' )->isAllowed ()) {
            $resultRedirect->setPath ( '*/*/' );
            return $resultRedirect;
        }
        
        if (! $this->getRequest ()->isPost ()) {
            $url = $this->urlModel->getUrl ( '*/*/create', [ 
                    '_secure' => true 
            ] );
            $resultRedirect->setUrl ( $this->_redirect->error ( $url ) );
            return $resultRedirect;
        }
        
        $this->session->regenerateId ();
        $data = $this->getRequest()->getPost();

        try {
            $address = $this->extractAddress ();
            $addresses = $address === null ? [ ] : [ 
                    $address 
            ];

            $customer = $objectModelManager->get ( 'Magento\Customer\Model\CustomerExtractor' )->extract ( 'customer_account_create', $this->_request );
            $customer->setAddresses ( $addresses );
            
            $password = $this->getRequest ()->getParam ( 'password' );
            $confirmation = $this->getRequest ()->getParam ( 'password_confirmation' );
            $redirectUrl = $this->session->getBeforeAuthUrl ();
            
            $this->checkPasswordConfirmation ( $password, $confirmation );
            
            $customer = $this->accountManagement->createAccount ( $customer, $password, $redirectUrl );
            
            if ($this->getRequest ()->getParam ( 'is_subscribed', false )) {
                $objectModelManager->get ( 'Magento\Newsletter\Model\SubscriberFactory' )->create ()->subscribeCustomerById ( $customer->getId () );
            }
            
            $this->_eventManager->dispatch ( 'customer_register_success', [ 
                    'account_controller' => $this,
                    'customer' => $customer 
            ] );
            
            $confirmationStatus = $this->accountManagement->getConfirmationStatus ( $customer->getId () );
            if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                $email = $objectModelManager->get ( 'Magento\Customer\Model\Url' )->getEmailConfirmationUrl ( $customer->getEmail () );
                
                $this->messageManager->addSuccess ( __ ( 'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.', $email ) );
                
                $url = $this->urlModel->getUrl ( '*/*/index', [ 
                        '_secure' => true 
                ] );
                $resultRedirect->setUrl ( $this->_redirect->success ( $url ) );
            } else {
                $this->session->setCustomerDataAsLoggedIn ( $customer );
                $this->messageManager->addSuccess ( $this->getSuccessMessage () );
                $resultRedirect = $this->accountRedirect->getRedirect ();
                $url = $this->urlModel->getUrl ( 'customer/account', [ 
                        '_secure' => true 
                ] );
                $resultRedirect->setUrl ( $this->_redirect->success ( $url ) );
            }
            $resultRedirectFlag = 1;          
        } catch ( StateException $e ) {
            $url = $this->urlModel->getUrl ( 'customer/account/forgotpassword' );
            
            $message = __ ( 'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.', $url );
            
            $this->messageManager->addError ( $message );
        } catch ( InputException $e ) {
            $this->messageManager->addError ( $this->escaper->escapeHtml ( $e->getMessage () ) );
            foreach ( $e->getErrors () as $error ) {
                $this->messageManager->addError ( $this->escaper->escapeHtml ( $error->getMessage () ) );
            }
        } catch ( \Exception $e ) {
            $this->messageManager->addException ( $e, __ ( 'We can\'t save the customer.' ) );
        }
        if($resultRedirectFlag == 0){
        $this->session->setCustomerFormData ( $this->getRequest ()->getPostValue () );
        $defaultUrl = $this->urlModel->getUrl ( '*/*/create', [ 
                '_secure' => true 
        ] );        
        $resultRedirect->setUrl ( $this->_redirect->error ( $defaultUrl ) );
        }
    
        $url                = $this->getRequest()->getPost('url');
        $group              = $this->getRequest()->getPost('group');
        $layout             = "2columns-left";
        $stores = array();
        $stores[] = $this->_sellerHelper->getCurrentStoreId();
         
        $objectManager      = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession    = $objectManager->get('Magento\Customer\Model\Session');
        
        if ($customerSession->isLoggedIn()) {

            $customerId     = $customerSession->getId ();
            $customerObject = $customerSession->getCustomer ();
            $customerEmail  = $customerObject->getEmail ();
            $customerName   = $customerObject->getName();
            $sellerApproval = $this->_sellerHelper->getConfig('general_settings/seller_approval');
            $street = '';
            $country = $this->_sellerHelper->getCountryname($data['country_id']);
            if(empty($data['region'])) {
                $region = $objectManager->create('Magento\Directory\Model\Region')
                        ->load($data['region_id']);
                $data['region'] = $region->getData('name');       
            }
            foreach ($data['street'] as $key => $_street) {
                $street .= ' '.$_street;
            }
            if ($sellerApproval) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $sellerModel = $objectManager->get('Lof\MarketPlace\Model\Seller');
                try {
                    $sellerModel->setCity($data['city'])->setCommany($data['company'])->setTelephone($data['telephone'])->setAddress($street)->setRegion($data['region'])->setRegionId($data['region_id'])->setPostcode($data['postcode'])->setCountry($country)->setName($customerName)->setEmail($customerEmail)->setStatus(0)->setGroupId($group)->setCustomerId($customerId)->setStores($stores)->setUrlKey($url)->setPageLayout($layout)->save();
                    $this->_redirect ('lofmarketplace/seller/becomeseller/approval/0');
                }  catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                     $this->_redirect ('lofmarketplace/seller/becomeseller');
                } 
            } else {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $sellerModel = $objectManager->get('Lof\MarketPlace\Model\Seller');
                try {
                    $sellerModel->setCity($data['city'])->setCommany($data['company'])->setTelephone($data['telephone'])->setAddress($street)->setRegion($data['region'])->setRegionId($data['region_id'])->setPostcode($data['postcode'])->setCountry($country)->setName($customerName)->setEmail($customerEmail)->setStatus(1)->setGroupId($group)->setCustomerId($customerId)->setUrlKey($url)->save();
                  
                    $this->_redirect ('marketplace/catalog/dashboard');

                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                     $this->_redirect ('lofmarketplace/seller/becomeseller');
                }
            }

            if($this->_sellerHelper->getConfig('email_settings/enable_send_email')) {
                $data = [];
                $data['name'] = $customerName;
                $data['email'] = $customerEmail;
                $data['group'] = $group;
                $data['url'] = $sellerModel->getUrl();
                $this->sender->registerSeller($data);
            } 
     
        } else {
            $resultRedirect = $this->resultRedirectFactory->create ();
            $resultRedirect->setPath('lofmarketplace/seller/login/');
            return $resultRedirect;
        }
    }
    
    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password            
     * @param string $confirmation            
     * @return void
     * @throws InputException
     */
    protected function checkPasswordConfirmation($password, $confirmation) {
        if ($password != $confirmation) {
            throw new InputException ( __ ( 'Please make sure your passwords match.' ) );
        }
    }
    
    /**
     * Retrieve success message
     *
     * @return string
     */
    protected function getSuccessMessage() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        if ($objectModelManager->get ( 'Magento\Customer\Helper\Address' )->isVatValidationEnabled ()) {
            if ($objectModelManager->get ( 'Magento\Customer\Helper\Address' )->getTaxCalculationAddressType () == Address::TYPE_SHIPPING) {
                $message = __ ( 'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your shipping address for proper VAT calculation.', $this->urlModel->getUrl ( 'customer/address/edit' ) );
            } else {
                $message = __ ( 'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your billing address for proper VAT calculation.', $this->urlModel->getUrl ( 'customer/address/edit' ) );
            }
        } else {
            $message = __ ( 'Thank you for registering with %1.', $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getFrontendName () );
        }
        return $message;
    }
}
