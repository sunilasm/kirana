<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\CityInterface;
 
class Cityview implements CityInterface
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
       \Magento\Framework\App\RequestInterface $request,
       \Asm\Customapi\Model\CityFactory $cityCollection
    ) {
       $this->request = $request;
       $this->_city = $cityCollection;
    }

    public function name() {
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        // $post = $request->getBodyParams();
        $region_id = 553;
        $resultPage = $this->_city->create();
        $collection = $resultPage->getCollection(); 
        $collection->addFieldToFilter('region_id',$region_id); 
        $data = array();
        $i = 0;
        foreach ($collection as $city) {
            $data[$i]['id'] = $city->getId();
            $data[$i]['name'] = $city->getName();
            $i++;
        }
        return $data;
    }
   
}
