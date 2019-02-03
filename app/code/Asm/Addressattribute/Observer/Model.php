<?php
namespace Asm\Addressattribute\Observer;
use Magento\Framework\Controller\ResultFactory; 

class Model implements \Magento\Framework\Event\ObserverInterface 
{
    protected $helperData;
    protected $_curl;
    protected $_key = 'AIzaSyD-_0vriuYY2qKxzK82yvVqgUeo-bqayDk'; //avinash sir 
    protected $_appUrl= 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct(
     \Magento\Framework\App\Action\Context $context,
     \Asm\Geolocation\Helper\Data $helperData,
     \Magento\Framework\HTTP\Client\Curl $curl,
     \Magento\Framework\Json\Helper\Data $jsonHelper

	) {
	    $this->helperData = $helperData;
	    $this->jsonHelper = $jsonHelper;
        $this->_curl = $curl;


	}
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
	$customerAddress = $observer->getCustomerAddress();
        $address = $customerAddress->getStreet();
         $city = ''; $state = ''; $country = ''; $postcode ='';
        $addressnew = implode(", ", $address);
        $city = $customerAddress->getCity();
        $state = $customerAddress->getRegion();
        $country = $customerAddress->getCountryId();
        $postcode = $customerAddress->getPostcode();
        // if(!isset($addressnew)){$addressnew = '';}
        // if(!isset($city)){$city = '';}
        // if(!isset($state)){$state = '';}
        // if(!isset($country)){$country = '';}
        // if(!isset($postcode)){$postcode = '';}

        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templognew.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info('in oberver');//here you will get address data
        // $logger->info(print_r($customerAddress, true));//here you will get address data

       
        $resultdata = $this->helperData->getLatlng($addressnew, $city, $state, $country, $postcode);
       
        //$decodedData = $this->jsonHelper->jsonDecode($resultdata);
        if($customerAddress->getEntityId()){

	        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	        $resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
	        $connection = $resource->getConnection();
	        $tableName = $resource->getTableName('customer_address_entity');

             $sql = "UPDATE " . $tableName . " SET latitude = '" . $resultdata['geo']->lat . "', longitude = '" . $resultdata['geo']->lng . "'  WHERE entity_id = " . $customerAddress->getEntityId();
	        // $sql = "UPDATE " . $tableName . " SET latitude = '" . $lat . "', longitude = '" . $lng . "'  WHERE entity_id = " . $customerAddress->getEntityId();
	        // $connection->query($sql);
    	}

    }
}
