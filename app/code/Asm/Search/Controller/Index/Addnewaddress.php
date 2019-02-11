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
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->customerSession = $customerSession;
        $this->_countryFactory = $countryFactory;
        $this->cart = $cart;
        $this->request = $request;
        parent::__construct($context);
    }

    public function execute()
    {

        $parameters = $this->request->getParams();
        $latitude = $parameters['latitude'];
        $longitude = $parameters['longitude'];
        //print_r("latitude-->".$latitude);
        //print_r("longitude-->".$longitude);
        $data1 = file_get_contents("https://maps.google.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=false&key=AIzaSyD-_0vriuYY2qKxzK82yvVqgUeo-bqayDk");
         $data = json_decode($data1);
          $add_array  = $data->results;
          $add_array = $add_array[0];
          $fullAdd = $add_array->formatted_address;
          $add_array = $add_array->address_components;
          // print_r($fullAdd);
          // print_r($add_array);exit;
          $country = "Not found";
          $state = "Not found"; 
          $city = "Not found";
          $newArray = array();
          // print_r($fullAdd.'<br/>');
          $formatted_address = explode(",", $fullAdd);
           $location = ''; $city = '';$state = '';$country = '';$postal_code ='';
          foreach ($add_array as $key) {
            $newArray[$key->types[0]] = $key->long_name;
            if($key->types[0] == 'political'){
              $location = $key->long_name;
            }
             if($key->types[0] == 'locality' || $key->types[0] == 'administrative_area_level_2'){
              $city = $key->long_name;
            }
             if($key->types[0] == 'administrative_area_level_1'){
              $state = $key->long_name;
            }
             if($key->types[0] == 'country'){
              $country = $key->long_name;
            }
             if($key->types[0] == 'postal_code'){
              $postal_code = $key->long_name;
            }
          }
          //print_r($location.'--'.$city.'--'.$state.'--'.$country.'--'.$postal_code);exit;
          $trimmed = '';
          foreach($newArray as $key => $value):
            if (strpos($fullAdd,$value) !== false) {
              $trimmed = str_replace($value, '', $fullAdd);
              $fullAdd = $trimmed;
            }
          endforeach;
          $string1 = substr(trim($trimmed), 0, -1);
          $string2 = substr(trim($string1), 0, -1);
          $string3 = substr(trim($string2), 0, -1);
          $street = $string3; 

          $countryCollection = $this->_countryFactory->create()->loadByCode('IN')->getRegions();
          $regions = $countryCollection->loadData()->toOptionArray(false);
          // print_r($regions);exit;
          $regionId = '533';
          $regionName = 'Andaman and Nicobar Islands';
          $countryCode = 'IN';
          foreach($regions as $region):
            if($region['title'] == $state){
              $regionId = $region['value'];
              $regionName = $region['title'];
              $countryCode = $region['country_id'];
            }
          endforeach;

          // print_r("country:".$countryCode.'<br/>');
          // print_r("postal_code:".$postal_code.'<br/>');
          // print_r("city:".$city.'<br/>');
          // print_r("regionId:".$regionId.'<br/>');
          // print_r("regionName:".$regionName.'<br/>');
          // print_r("street:".$street);

          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');
          $address = $addresss->create();
          $address->setCustomerId($this->customerSession->getCustomer()->getId())
          ->setFirstname($this->customerSession->getCustomerData()->getFirstname())
          ->setLastname($this->customerSession->getCustomerData()->getLastname())
          ->setCountryId($countryCode)
          ->setPostcode($postal_code)
          ->setCity($city)
          ->setRegionId($regionId)
          ->setRegion($regionName)
          ->setStreet($street)
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

          // $AllItems = $this->cart->getQuote()->getAllVisibleItems();
          // foreach($AllItems as $item):
          //   print_r($item); 
          // endforeach;
          // exit;
     }               
}     