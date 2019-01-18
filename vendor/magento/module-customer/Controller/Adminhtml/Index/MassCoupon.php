<?php
namespace Magento\Customer\Controller\Adminhtml\Index;

//use \TEXT\Smsnotifications\Observer;

use Magento\Backend\App\Action\Context;
//use \Magento\Framework\View\Element\Context as Context;
use \Magento\Framework\Event\Observer       as Observer;
use \TEXT\Smsnotifications\Helper\Data       as Helper;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;



class MassCoupon extends AbstractMassAction
{
    /**
     * @var CustomerRepositoryInterface.
     */
    
    protected $customerRepository;
    protected $helper;
    protected $addressRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository,
        \TEXT\Smsnotifications\Helper\Data  $helper,
       \Magento\Customer\Api\AddressRepositoryInterface  $addressRepository
       
       
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->customerRepository = $customerRepository;
         $this->helper = $helper;
          $this->addressRepository = $addressRepository;
        
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
  
        $settings = $this->helper->getSettings();
        $customersUpdated = 0;
            
                $admin_recipients=array();

                foreach($collection->getAllIds() as $customerId){
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);

                        $customer = $this->customerRepository->getById($customerId);
                        $billingAddressId = $customer->getDefaultBilling();
                        $shippingAddressId = $customer->getDefaultShipping();

                              $billingAddress = $this->addressRepository->getById($billingAddressId);
                              $firstname= $billingAddress->getFirstname();
                              $lastname= $billingAddress->getLastname();
                          
                           $admin_recipients[] = $telephone = $billingAddress->getTelephone();
               }

       
                   if (in_array($telephone, $admin_recipients)){

                     $text = $settings['coupon_code'];
                     $text = str_replace('{{firstname}}', $firstname, $text);
                     $text = str_replace('{{lastname}}', $lastname, $text);
                   }
                  

                     $admin_recipients[]=$settings['admin_recipients'];
                    // array_push($admin_recipients, $abc);
                   
        $object_manager = \Magento\Framework\App\ObjectManager ::getInstance();
        $result = $object_manager->get('TEXT\Smsnotifications\Helper\Data')->sendSms($text,$admin_recipients);
         if ($result) {
 
             $recipients_string = implode(',', $admin_recipients);
           $this->messageManager->addSuccess(__('A total of %1 record(s) have send sms .', $recipients_string));
 
            }
        
         $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
        }
    
        
}
