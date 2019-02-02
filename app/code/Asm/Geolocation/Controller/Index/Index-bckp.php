<?php
namespace Asm\Geolocation\Controller\Index;
use Magento\Framework\Controller\ResultFactory; 
use Magento\Framework\App\Action\Action;


class Index extends \Magento\Framework\App\Action\Action
{
    protected $_curl;
    protected $_key = 'AIzaSyA2bRGYQUYqiVZj4SBYAjWvx-3eVqW3Yh4';
    // protected $_key = 'AIzaSyCoLbQMJVrWfwYGdNOWxOVz3NMzYjCRhQg';
    protected $_appUrl= 'https://maps.googleapis.com/maps/api/geocode/xml';
    protected $request;
    protected $helperData;
    public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\HTTP\Client\Curl $curl,
    \Asm\Geolocation\Helper\Data $helperData,
    \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->_curl = $curl;
        $this->request = $request;
        $this->helperData = $helperData;
        return parent::__construct($context);
    }

    public function execute()
    {
        //echo"wwwwwwwww";exit;
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if ($this->getRequest()->isAjax())
        {
            $parameters = $this->request->getParams();
            $address = $parameters['address'];
            $city = $parameters['city'];
            $state = $parameters['state'];
            $country = $parameters['country'];
            $postcode = $parameters['pincode'];
            $resultData = $this->helperData->getLatlng($address, $city, $state, $country, $postcode);
            $result->setData($resultData);
        }
        //print_r($result);exit;
        return $result;
    }
    
}