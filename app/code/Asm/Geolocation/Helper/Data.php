<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Asm\Geolocation\Helper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory; 
use Magento\Framework\App\Action\Action;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_curl;
    // protected $_key = 'AIzaSyA2bRGYQUYqiVZj4SBYAjWvx-3eVqW3Yh4'; //pradeep
    // protected $_key = 'AIzaSyCoLbQMJVrWfwYGdNOWxOVz3NMzYjCRhQg'; //pradeep
    // protected $_key = 'AIzaSyD6iVNHsDTaqsRzmZ6-KSvdS_KG44lvf14'; //umesh
    protected $_key = 'AIzaSyD-_0vriuYY2qKxzK82yvVqgUeo-bqayDk'; //avinash sir 
    protected $_appUrl= 'https://maps.googleapis.com/maps/api/geocode/json';
    protected $request;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_curl = $curl;
        $this->request = $request;
        parent::__construct($context);
    }
    public function getLatlng($address1, $city, $state, $country, $postcode){
        //$result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $output = array();
        if ($address1 || $city || $state || $country || $postcode)
        {
            //print_r($address1."--".$city."--".$state."--".$country."--".$postcode);exit;
            $url = $this->_appUrl;      
            $address = (isset($address1)) ? urlencode($address1).',' : urlencode('Gondhale Nagar Hadapsar');
            $address .= (isset($city)) ? $city.',' : 'Pune';
            $address .= (isset($state)) ? urlencode($state).',' : 'Maharashtra';
            $address .= (isset($country)) ? urlencode($country) : 'India';
            $address .= (isset($postcode)) ? urlencode($postcode) : '411028';
            
            $url .= $address."&key=".$this->_key;
            $formattedAddr = str_replace(' ','+',$address);
            $data1 = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$formattedAddr&sensor=false&key=AIzaSyD-_0vriuYY2qKxzK82yvVqgUeo-bqayDk");
            $response = json_decode($data1);
            $output = array();
            if($response->status == 'OK'){
                $output['status'] = 'success';
                $output['geo']= $response->results[0]->geometry->location;        
            }
            else
            {
                $output['status'] = (string) $response->status;
                $output['message'] = (string) $response->error_message;
            }
        }

        //print_r($output);exit;
        return $output;
    }
}
