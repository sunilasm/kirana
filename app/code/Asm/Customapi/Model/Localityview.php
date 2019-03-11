<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\LocalityInterface;
 
class Localityview implements LocalityInterface
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
       \Asm\Customapi\Model\LocalityFactory $localityCollection
    ) {
       $this->request = $request;
       $this->_locality = $localityCollection;
    }

    public function locality() {
        //print_r("Api execute successfully");exit;
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $resultPage = $this->_locality->create();
        $collection = $resultPage->getCollection(); 
        $collection->addFieldToFilter('area_id',$post['area_id']); 
        $data = array();
        $i = 0;
        foreach ($collection as $locality) {
            $data[$i]['id'] = $locality->getId();
            $data[$i]['name'] = $locality->getName();
            $i++;
        }
        return $data;
    }
   
}
