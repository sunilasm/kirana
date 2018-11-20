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
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Model;

class Sellerlist extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected  $_seller;
    
    /**
     * 
     * @param \Lof\MarketPlace\Model\Seller $seller
     */
    public function __construct(
        \Lof\MarketPlace\Model\Seller $seller
        ) {
        $this->_seller = $seller;
    }
    
    
    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $sellers = $this->_seller->getCollection()
        ->addFieldToFilter('status', '1');
        $listSeller = array();
        foreach ($sellers as $seller) {
            $listSeller[] = array('label' => $seller->getName(),
                'value' => $seller->getId());
        }
        return $listSeller;
    }

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = array();
        $options = $this->getAvailableTemplate();

        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => '-- Please Select --',
                ));
        }
        return $options;
    }
}