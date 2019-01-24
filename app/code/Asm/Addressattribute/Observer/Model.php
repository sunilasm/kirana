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
        $addressnew = implode(", ", $address);
        $city = $customerAddress->getCity();
        $state = $customerAddress->getRegion();
        $country = $customerAddress->getCountryId();
        $postcode = $customerAddress->getPostcode();

        $resultdata = $this->helperData->getLatlng($addressnew, $city, $state, $country, $postcode);

        //$decodedData = $this->jsonHelper->jsonDecode($resultdata);
        if($customerAddress->getEntityId()){

	        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	        $resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
	        $connection = $resource->getConnection();
	        $tableName = $resource->getTableName('customer_address_entity');

	        $sql = "UPDATE " . $tableName . " SET latitude = '" . $resultdata['geo']->lat . "', longitude = '" . $resultdata['geo']->lng . "'  WHERE entity_id = " . $customerAddress->getEntityId();
	        $connection->query($sql);
    	}
    }
}
