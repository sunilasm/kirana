<?php
namespace Asm\Geolocation\Controller\Index;
use Magento\Framework\Controller\ResultFactory; 
use Magento\Framework\App\Action\Action;


class Index extends \Magento\Framework\App\Action\Action
{
    protected $_curl;
    // protected $_key = 'AIzaSyA2bRGYQUYqiVZj4SBYAjWvx-3eVqW3Yh4';
    protected $_key = 'AIzaSyD-_0vriuYY2qKxzK82yvVqgUeo-bqayDk';
    protected $_appUrl= 'https://maps.googleapis.com/maps/api/geocode/xml';
    protected $request;
    public function __construct(\Magento\Framework\App\Action\Context $context,
    \Magento\Framework\HTTP\Client\Curl $curl,
    \Magento\Framework\App\RequestInterface $request)
    {
        $this->_curl = $curl;
        $this->request = $request;
        return parent::__construct($context);
    }

    public function execute()
    {
        // $data=array("bdfb"); 
        // $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        // $resultJson->setData($data); 
        // return $resultJson;

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if ($this->getRequest()->isAjax())
        {
            $parameters = $this->request->getParams();
            // print_r($parameters['address']);exit;
            $url = $this->_appUrl;
            $address = '?address=';
           
            $address .= (isset($parameters['address'])) ? urlencode($parameters['address']).',' : urlencode('Gondhale Nagar Hadapsar');
            $address .= (isset($parameters['city'])) ? $parameters['city'].',' : 'Pune';
            $address .= (isset($parameters['state'])) ? urlencode($parameters['state']).',' : 'Maharashtra';
            $address .= (isset($parameters['country'])) ? urlencode($parameters['country']) : 'India';
            
            $url .= $address."&key=".$this->_key;
            $this->_curl->get($url);
            $response = $this->_curl->getBody();
            $response = new \SimpleXMLElement($response);
            //echo "<pre>".print_r($str,true); exit;
            $output = array();
            if($response->status == 'OK'){
                $output['status'] = 'success';
                $output['geo']= $response->result->geometry->location;
                //$output['lat'] = isset($response->result->geometry->location->lat) ? $response->result->geometry->location->lat : '';
                //$output['lng'] = isset($response->result->geometry->location->lng) ? $response->result->geometry->location->lng : '';                 
            }
            else
            {
                $output['status'] = (string) $response->status;
                $output['message'] = (string) $response->error_message;
            }
            $result->setData($output);
        }
        return $result;
    }
    
}