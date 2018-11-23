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
namespace Lof\MarketPlace\Model;

use Magento\Framework\DataObject\IdentityInterface;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order as BaseOrder;
/**
 * Orderitems Model
 */
class Order extends \Magento\Framework\Model\AbstractModel
{

     /**
     * Seller Group Object
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context,$registry,$resource,$resourceCollection);
    }

	 /**
     * Define resource model
     */
    protected function _construct() {
        $this->_init ( 'Lof\MarketPlace\Model\ResourceModel\Order' );
    }

     /**
     * Get order object
     * @return \Mage\Sales\Model\Order
     */
    public function getOrder(){
        
        if(!$this->_order){
            
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_order = $om->create('Magento\Sales\Model\Order');
            $this->_order->load($this->getOrderId(),'entity_id');
        }
   
        return $this->_order;
    }


    /**
     * @return \Magento\Sales\Model\Order\Item[]
     */
    public function getAllItems()
    {
        if ($this->getData('all_items') == null) {
            $items = [];
            foreach ($this->getOrder()->getAllItems() as $item) {
               // if($item->getOrderId() == $this->getId()) {
                    $items[$item->getId()] = $item;
               // }
            }
            
            $this->setData('all_items',$items);
        }
        return $this->getData('all_items');
    }
    
    /**
     * Retrieve order invoice availability
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function canInvoice()
    {
        $order = $this->getOrder();
        
        if ($this->canUnhold() || $order->isPaymentReview()) {
            return false;
        }

        $status = $this->getStatus();
        
        if ($this->isCanceled() || $status === BaseOrder::STATE_COMPLETE || $status === BaseOrder::STATE_CLOSED) {
            return false;
        }
        
        foreach ($this->getAllItems() as $item) {
           
            if ($item->getQtyToInvoice() > 0 && !$item->getLockedDoInvoice()) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check whether order is canceled
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->getStatus() === BaseOrder::STATE_CANCELED;
    }
     /**
     * Retrieve order unhold availability
     *
     * @return bool
     */
    public function canUnhold()
    {
        if ($this->getOrder()->isPaymentReview()) {
            return false;
        }
        return $this->getStatus() === BaseOrder::STATE_HOLDED;
    }
    /**
     * Retrieve order shipment availability
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function canShip()
    {
        $order = $this->getOrder();

        if ($this->canUnhold() || $order->isPaymentReview()) {
            return false;
        }
           
        if ($order->getIsVirtual() || $order->isCanceled()) {
            return false;
        }
          
        foreach ($this->getAllItems() as $item) {
            
            if ($item->getQtyToShip() > 0 && !$item->getIsVirtual() && !$item->getLockedDoShip()) {
                return true;
            }
        }

      
        return false;
    }
    
    
    /**
     * Retrieve order credit memo (refund) availability
     *
     * @return bool
     */
    public function canCreditmemo()
    {

        if ($this->hasForcedCanCreditmemo()) {
            return $this->getForcedCanCreditmemo();
        }
    
        if ($this->canUnhold() || $this->getOrder()->isPaymentReview()) {
            return false;
        }
    
        if ($this->isCanceled() || $this->getState() === BaseOrder::STATE_CLOSED) {
            return false;
        }

        /**
         * We can have problem with float in php (on some server $a=762.73;$b=762.73; $a-$b!=0)
         * for this we have additional diapason for 0
         * TotalPaid - contains amount, that were not rounded.
         */
        if (abs($this->priceCurrency->round($this->getTotalPaid()) - $this->getTotalRefunded()) < .0001) {
            return false;
        }
    
        return true;
    }
}