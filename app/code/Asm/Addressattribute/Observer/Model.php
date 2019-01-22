<?php
namespace Asm\Addressattribute\Observer;
use Magento\Framework\Controller\ResultFactory; 

class Model implements \Magento\Framework\Event\ObserverInterface 
{
    protected $helperData;

    public function __construct(
     \Magento\Framework\App\Action\Context $context,
     \Asm\Geolocation\Helper\Data $helperData,
     \Magento\Framework\Json\Helper\Data $jsonHelper

	) {
	    $this->helperData = $helperData;
	    $this->jsonHelper = $jsonHelper;

	}
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$customerAddress = $observer->getCustomerAddress();
        $address = $customerAddress->getStreet();
        $city = $customerAddress->getCity();
        $state = $customerAddress->getRegion();
        $country = $customerAddress->getCountryId();
        $postcode = $customerAddress->getPostcode();

        $resultdata = $this->helperData->getLatlng($address, $city, $state, $country, $postcode);

        //$decodedData = $this->jsonHelper->jsonDecode($resultdata);
        if($customerAddress->getEntityId()){

	        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	        $resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
	        $connection = $resource->getConnection();
	        $tableName = $resource->getTableName('customer_address_entity');

	        $sql = "UPDATE " . $tableName . " SET latitude = '" . $resultdata['geo']->lat . "', longitude = '" . $resultdata['geo']->lng . "'  WHERE entity_id = " . $customerAddress->getEntityId();
	        $connection->query($sql);
    	}
        $customer = $customerAddress->getCustomer(); //if you want to get customer details
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('in oberver');//here you will get address data
		$logger->info($sql);//here you will get address data
		$logger->info($customerAddress->getEntityId());//here you will get address data
		$logger->info($address[0]);//here you will get address data
		$logger->info($city);//here you will get address data
		$logger->info($state);//here you will get address data
		$logger->info($country);//here you will get address data
		$logger->info($postcode);//here you will get address data
		$logger->info($sql);//here you will get address data
		$logger->info(print_r($resultdata['geo']->lat,true));//here you will get address data
		$logger->info("Run observer");//here you will get address data
		$logger->info($customerAddress->getLongitude());//here you will get address data
    }
}