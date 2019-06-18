<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\DeleteaddressInterface;
 
class Deleteaddressview implements DeleteaddressInterface
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

    public function deletecustomeraddress($addressId) {
        // print_r("Api execute successfully");exit;
        if(isset($addressId)){
            $address = $this->addressRepository->deleteById($addressId);
        }
        if($address){
            $result = array("success" => "Address removed");
        }else{
            $result = array("fail" => "No such entity");
        }
        $response = array($result);
        return $response;
    }
}
