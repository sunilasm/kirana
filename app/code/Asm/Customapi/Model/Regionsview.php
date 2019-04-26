<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\RegionsInterface;
 
class Regionsview implements RegionsInterface
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
       \Magento\Directory\Model\CountryFactory $countryFactory
    ) {
       $this->request = $request;
       $this->_countryFactory = $countryFactory;
    }

    public function regions() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
          $countryCollection = $this->_countryFactory->create()->loadByCode($post['country_code'])->getRegions();
          $regions = $countryCollection->loadData()->toOptionArray(false);
          $data = array();
          foreach(array_slice($regions, 1) as $region):
              $data[$region['value']] = $region['title'];
          endforeach;
          // print_r($data);exit;
        return $data;
    }
}
