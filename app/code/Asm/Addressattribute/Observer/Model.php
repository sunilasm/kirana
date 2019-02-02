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
        $addressnew = implode(", ", $address);
        $city = $customerAddress->getCity();
        $state = $customerAddress->getRegion();
        $country = $customerAddress->getCountryId();
        $postcode = $customerAddress->getPostcode();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        //$resultdata = $this->helperData->getLatlng($addressnew, $city, $state, $country, $postcode);
        $url = $this->_appUrl;
        $logger->info("Base-->".$url);
        $address = '?address=';
       
        $address .= (isset($addressnew)) ? urlencode($addressnew).',' : urlencode('Gondhale Nagar Hadapsar');
        $address .= (isset($city)) ? $city.',' : 'Pune';
        $address .= (isset($state)) ? urlencode($state).',' : 'Maharashtra';
        $address .= (isset($country)) ? urlencode($country) : 'India';
        $address .= (isset($postcode)) ? urlencode($postcode) : '411001';
        
        $url .= $address."&key=".$this->_key;
        $logger->info("Gegrated-->".$url);
        //print_r($url);exit;
        $this->_curl->get($url);
        $response = $this->_curl->getBody();
        $logger->info(print_r($response,true));
        $data = json_decode($response);

        //$resultdata['geo']->lat = '11111';
        //$resultdata['geo']->lat = '2222';
        
        $logger->info("Call obeserver");
        $logger->info(print_r($data,true));

        $lat = '32432423';
        $lng = '56666666';

        //$decodedData = $this->jsonHelper->jsonDecode($resultdata);
        if($customerAddress->getEntityId()){

	        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	        $resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
	        $connection = $resource->getConnection();
	        $tableName = $resource->getTableName('customer_address_entity');

            // $sql = "UPDATE " . $tableName . " SET latitude = '" . $resultdata['geo']->lat . "', longitude = '" . $resultdata['geo']->lng . "'  WHERE entity_id = " . $customerAddress->getEntityId();
	        $sql = "UPDATE " . $tableName . " SET latitude = '" . $lat . "', longitude = '" . $lng . "'  WHERE entity_id = " . $customerAddress->getEntityId();
	        // $connection->query($sql);
    	}
        //$customer = $customerAddress->getCustomer(); //if you want to get customer details
		// $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
		// $logger = new \Zend\Log\Logger();
		// $logger->addWriter($writer);
		$logger->info('in oberver');//here you will get address data
		$logger->info($sql);//here you will get address data
		$logger->info($customerAddress->getEntityId());//here you will get address data
		$logger->info($address[0]);//here you will get address data
		$logger->info($city);//here you will get address data
		$logger->info($state);//here you will get address data
		$logger->info($country);//here you will get address data
		$logger->info($postcode);//here you will get address data
		$logger->info($sql);//here you will get address data
		// $logger->info(print_r($resultdata['geo']->lat,true));//here you will get address data
		$logger->info("Run observer");//here you will get address data
		//$logger->info($customerAddress->getLongitude());//here you will get address data
    }
}