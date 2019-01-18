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

namespace Lof\MarketPlace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ProductFactory;


class AddSellerProduct implements ObserverInterface
{
    /**
     * Product Factory
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        ProductFactory $productFactory,
        \Magento\Framework\View\Element\Context $context, 
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
    }
    
    /**
     * Set the vendor id in bulk for product
     * 
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $attrData = $observer->getAttributesData();
         $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        // $logger->info($sql);//here you will get address data
        $logger->info(print_r($attrData,true));//here you will get address data
        $logger->info("Run observer1111");//here you will get address data


        if(isset($attrData['seller_id'])){
            $productIds = $observer->getProductIds();
            $resource = $this->_productFactory->create()->getResource();
            
            $adapter   = $resource->getConnection();
            $sql = "UPDATE ".$resource->getTable('catalog_product_entity').' SET seller_id="'.$attrData['seller_id'].'" WHERE entity_id IN('.implode(",", $productIds).')';
            $adapter->query($sql);
            
            unset($attrData['seller_id']);
            $observer->getEvent()->setData('attributes_data',$attrData);
        }
    }
    
    
}
