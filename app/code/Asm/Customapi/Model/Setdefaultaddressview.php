<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\SetdefaultaddressInterface;
 
class Setdefaultaddressview implements SetdefaultaddressInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;
    protected $addressRepository;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
       \Magento\Customer\Model\Address\Config $addressConfig,
    \Magento\Customer\Model\Address\Mapper $addressMapper
    ) {
       $this->request = $request;
       $this->addressRepository = $addressRepository;
       $this->_addressConfig = $addressConfig;
       $this->addressMapper = $addressMapper;
    }

    public function setdefaultcustomeraddress() {
        // print_r("herererre");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        if(isset($post['address_id'])){
            $addressId = $post['address_id'];
        }
        if(isset($post['customer_id'])){
            $customerId = $post['customer_id'];
        }

        if(isset($addressId)){
            $address = $this->addressRepository->getById($addressId);
            $default_shipping = "true";
            $address->setIsDefaultShipping($default_shipping);
            $this->addressRepository->save($address);

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
             
            $ch = curl_init("$baseUrl".''."rest/V1/customers/".$customerId);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
            $result = curl_exec($ch);
            $result = json_decode($result, 1);
            // print_r($result);exit;
            $addressArray = array();
            foreach($result['addresses'] as $addresses){
                if($addresses['id'] == $addressId && $addresses['customer_id'] == $customerId){
                    if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $addresses['telephone'],  $matches ) )
                        {
                            $result1 = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
                            $addresses['telephone'] = $result1;
                        }
                        $addressArray = $addresses;
                }

            }
        $response = array($addressArray);
        return $response;

        }
    }
}
