<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\AreaInterface;
 
class Areaview implements AreaInterface
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
       \Asm\Customapi\Model\AreaFactory $areaCollection
    ) {
       $this->request = $request;
       $this->_area = $areaCollection;
    }

    public function area() {
        //print_r("Api execute successfully");exit;
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $resultPage = $this->_area->create();
        $collection = $resultPage->getCollection(); 
        $collection->addFieldToFilter('city_id',$post['city_id']); 
        $data = array();
        $i = 0;
        foreach ($collection as $area) {
            $data[$i]['id'] = $area->getId();
            $data[$i]['name'] = $area->getName();
            $i++;       
        }
        //print_r($data);exit;
        return $data;
    }
   
}
