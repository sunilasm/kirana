<?php
namespace Retailinsights\Slider\Model;

use Retailinsights\Slider\Api\SliderimagesInterface;

class Sliderimages implements SliderimagesInterface
{

    protected $_postFactory;
    
    public function __construct(
                    \Retailinsights\Slider\Model\PostFactory $postFactory
							) 
							{
	         	$this->_postFactory = $postFactory;
             	}

    public function getimages($ruletype)
    {
			$result = array();
			$ImageCollection = $this->_postFactory->create()->getCollection()
			->addFieldToFilter('promo_type',array('promo_type'=>$ruletype));
			foreach($ImageCollection->getData() as $image){
				$x['image_path'] =  $image['image_path'];
				$x['promo_type'] = $image['promo_type'];
				array_push($result,$x);
			}
			
			return $result ;
		    
		}
}