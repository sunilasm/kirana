<?php
namespace Asm\Search\Controller\Index;

use Magento\Framework\App\Action\Context;

class Addnewaddress extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    //protected $cart;

    /**
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param CustomerCart $cart
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function execute()
    {

          //print_r("Herrere");exit;
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');
          $address = $addresss->create();
          // print_r($this->customerSession->getCustomerData());exit;
          $address->setCustomerId($this->customerSession->getCustomer()->getId())
          ->setFirstname($this->customerSession->getCustomer()->getName())
          ->setLastname($this->customerSession->getCustomer()->getName())
          ->setCountryId('IN')
          //->setRegionId('1') //state/province, only needed if the country is USA
          ->setPostcode('411045')
          ->setCity('Pune')
          ->setTelephone('1234567890')
          ->setRegionId('553')
          ->setRegion("Maharashtra")
          ->setStreet('Prabhavee Tech Park Baner')
          ->setIsDefaultBilling('1')
          ->setIsDefaultShipping('1')
          ->setSaveInAddressBook('1');
          try{
                  $address->save();
                  print_r("Save address successfully");
          }
          catch (Exception $e) {
                  Zend_Debug::dump($e->getMessage());
          }
     }               
}     