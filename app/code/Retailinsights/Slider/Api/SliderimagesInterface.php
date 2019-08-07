<?php
namespace Retailinsights\Slider\Api;

interface SliderimagesInterface
{
    
     /**
     *  
     * @api
     * @param string $ruletype getimages ruletype.
     * @return string
     */
     
    public function getimages($ruletype);
   

}