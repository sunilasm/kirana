<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\CountryInterface;
 
class Countryview implements CountryInterface
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

    public function country() {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $allowerdContries = $objectManager->get('Magento\Directory\Model\AllowedCountries')->getAllowedCountries() ;
            $countryFactory = $objectManager->get('\Magento\Directory\Model\CountryFactory');
            $countries = array();
            foreach($allowerdContries as $countryCode)
            {
                if($countryCode)
                {
                    $data = $countryFactory->create()->loadByCode($countryCode);
                    $countries[$countryCode] =  $data->getName();
                }
            }

        return $countries;
    }
}
