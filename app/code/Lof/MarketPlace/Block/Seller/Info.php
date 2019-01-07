<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Block\Seller;

class Info extends \Magento\Framework\View\Element\Template {

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerFactory;
    /**
     * @var \Lof\MarketPlace\Model\Data
     */
    protected $_helper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;
    /**
     * @var \Lof\MarketPlace\Model\Rating
     */
    protected $rating;
     /**
     * @var \Lof\MarketPlace\Model\Orderitems
     */
    protected $orderitems;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lof\MarketPlace\Model\Seller
     * @param \Magento\Framework\App\ResourceConnection
     * @param array
    */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\MarketPlace\Model\Rating $rating,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        array $data = []
        ) {

        $this->_helper        = $helper;
        $this->_coreRegistry  = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource      = $resource;
        $this->rating = $rating;
        $this->orderitems     = $orderitems;
        parent::__construct($context);
    }
    /**
     *  get Seller Colection
     *
     * @return Object
     */
     public function getSellerCollection(){
        $store            = $this->_storeManager->getStore();
        $sellerCollection = $this->_sellerFactory->getCollection();
        return $sellerCollection;
    }
     /**
     *  get Total Sales
     *
     * @return Credit Total Sales
     */
    public function getTotalSales() {
        $total = 0;
        $orderitems = $this->orderitems->getCollection()->addFieldToFilter('seller_id',$this->getCurrentSeller()->getData('seller_id'))->addFieldToFilter('status','complete');
        foreach ($orderitems as $key => $_orderitems) {
            $total = $total + $_orderitems->getProductQty();
        }
        return $total;
    }
    public function getCurrentSeller()
    {
        $seller = $this->_coreRegistry->registry('current_seller');
        if ($seller) {
            $this->setData('current_seller', $seller);
        }
        return $seller;
    }
     public function _toHtml()
    {

        if ($this->getCurrentSeller()->getData('status') == 0) {
            return;
        }
        return parent::_toHtml();
    }
    public function getRating() {
        $rating = $this->rating->getCollection()->addFieldToFilter('seller_id',$this->getCurrentSeller()->getData('seller_id'));
        return $rating;
    }

    public function getRate() {
        $count = $total_rate = 0;
        $rate1 = $rate2 =$rate3 = $rate4 = $rate5 = 0;
        foreach ($this->getRating() as $key => $rating) {
            if($rating->getData('rate1') > 0) {
                $count ++;
                $total_rate = $total_rate + $rating->getData('rate1');
                if($rating->getData('rate1') == 1) {
                    $rate1 ++;
                }elseif($rating->getData('rate1') == 2) {
                    $rate2 ++;
                }elseif($rating->getData('rate1') == 3) {
                    $rate3 ++;
                }elseif($rating->getData('rate1') == 4) {
                    $rate4 ++;
                }elseif($rating->getData('rate1') == 5) {
                    $rate5 ++;
                }
            }
            if($rating->getData('rate2') > 0) {
                $count ++;
                $total_rate = $total_rate + $rating->getData('rate2');
                if($rating->getData('rate2') == 1) {
                    $rate1 ++;
                }elseif($rating->getData('rate2') == 2) {
                    $rate2 ++;
                }elseif($rating->getData('rate2') == 3) {
                    $rate3 ++;
                }elseif($rating->getData('rate2') == 4) {
                    $rate4 ++;
                }elseif($rating->getData('rate2') == 5) {
                    $rate5 ++;
                }
            }
            if($rating->getData('rate3') > 0) {
                $count ++;
                $total_rate = $total_rate + $rating->getData('rate3');
                if($rating->getData('rate3') == 1) {
                    $rate1 ++;
                }elseif($rating->getData('rate3') == 2) {
                    $rate2 ++;
                }elseif($rating->getData('rate3') == 3) {
                    $rate3 ++;
                }elseif($rating->getData('rate3') == 4) {
                    $rate4 ++;
                }elseif($rating->getData('rate3') == 5) {
                    $rate5 ++;
                }
            }
        }
        $data = [];
        if($count>0) {
            $average = ($total_rate/$count);
        } else {
            $average = 0;
        }
        $data['count'] = $count;
        $data['total_rate'] = $total_rate;
        $data['average'] = $average;
        $data['rate'] =[];
        $data['rate'][1] = $rate1;
        $data['rate'][2] = $rate2;
        $data['rate'][3] = $rate3;
        $data['rate'][4] = $rate4;
        $data['rate'][5] = $rate5;
        return $data;

    }
}