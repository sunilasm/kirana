<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\NewaddressInterface;
 
class Newaddressview implements NewaddressInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request
    ) {
       $this->request = $request;
    }

    public function newcustomeraddress() {
        // print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        // print_r($post);exit;

        $CustomerModel = $objectManager->create('Magento\Customer\Model\Customer');
        $CustomerModel->setWebsiteId(1);
        $CustomerModel->loadByEmail($post['customer']['email']);
        $userId = $CustomerModel->getId();
        $addresses = $post['customer']['addresses'];
        // print_r($post);exit;
        foreach($addresses as $addressnew):
            $addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');
            $address = $addresss->create();
            $firstname = $lastname = $country_id = $postcode = $city = $telephone = $street = $default_billing = $default_shipping = $region_code = $region = $region_id = '';
            if(isset($addressnew['firstname'])){
                $firstname = $addressnew['firstname'];
            }
            if(isset($addressnew['lastname'])){
                $lastname = $addressnew['lastname'];
            }
            if(isset($addressnew['country_id'])){
                $country_id = $addressnew['country_id'];
            }
            if(isset($addressnew['postcode'])){
                $postcode = $addressnew['postcode'];
            }
            if(isset($addressnew['region']['region_code'])){
                $region_code = $addressnew['region']['region_code'];
            }
            if(isset($addressnew['region']['region'])){
                $region = $addressnew['region'];
            }
            if(isset($addressnew['region_id'])){
                $region_id = $addressnew['region_id'];
            }
            if(isset($addressnew['city'])){
                $city = $addressnew['city'];
            }
            if(isset($addressnew['telephone'])){
                $telephone = $addressnew['telephone'];
            }
            if(isset($addressnew['telephone'])){
                $telephone = $addressnew['telephone'];
            }
            if(count($addressnew['street'])){
                $street = implode(',', $addressnew['street']);
            }
            if(isset($addressnew['default_billing'])){
                $default_billing = $addressnew['default_billing'];
            }
            if(isset($addressnew['default_shipping'])){
                $default_shipping = $addressnew['default_shipping'];
            }
            $address->setCustomerId($userId)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setCountryId($country_id)
            ->setPostcode($postcode)
            ->setRegionCode($region_code)
            ->setRegion($region)
            ->setRegionId($region_id)
            ->setCity($city)
            ->setTelephone($telephone)
            // ->setFax($addressnew['lastname'])
            // ->setCompany($addressnew['lastname'])
            ->setStreet($street)
            ->setIsDefaultBilling($default_billing)
            ->setIsDefaultShipping($default_shipping)
            ->setSaveInAddressBook('1');
            $address->save();
        endforeach;

        // Set Response
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $baseUrl = $storeManager->getStore()->getBaseUrl();
        $userData = array("username" => "adminapi", "password" => "Admin@123");
        $ch = curl_init("$baseUrl".''."rest/V1/integration/admin/token");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

        $token = curl_exec($ch);
         
        $ch = curl_init("$baseUrl".''."rest/V1/customers/".$userId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
        $result = curl_exec($ch);
        $result = json_decode($result, 1);
        // print_r($result);exit;
        $addressArray = array();
        for($i=0;$i<count($result['addresses']);$i++){
            $k = 1;
            $lastEle = count($result['addresses']) - $k;
            if($lastEle == $i){
                if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $result['addresses'][$i]['telephone'],  $matches ) )
                {
                    $result1 = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
                    $result['addresses'][$i]['telephone'] = $result1;
                }
                $addressArray = array_pop($result['addresses']);
            }
        }
        $response = array($addressArray);
        return $response;
    }
}
