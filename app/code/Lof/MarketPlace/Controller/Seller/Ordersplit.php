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
namespace Lof\MarketPlace\Controller\Seller;
use Magento\Framework\App\Action\Context;

class Ordersplit extends \Magento\Framework\App\Action\Action
{
	protected $helper;

    protected $sellerProduct;

    protected $calculate;

    protected $sender;

    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Model\Ordersplit $ordersplit,
        Context $context
    ) {
        $this->sender = $sender;
        $this->calculate = $calculate;
        $this->helper      = $helper;
        $this->sellerProduct = $sellerProduct;
        $this->kiranaorder = $ordersplit; 
        parent::__construct($context);
    }

	/**
	 * Order list action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
	 */
	public function execute()
	{
		$time = time();
        $to = date('Y-m-d H:i:s', $time);
        $lastTime = $time - 300; // 60*60*24
        $from = date('Y-m-d H:i:s', $lastTime);
        // print_r("to:-".$to);
        // print_r("from:-".$from); exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $OrderFactory = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\CollectionFactory');
        $orderCollection = $OrderFactory->create()->addFieldToSelect(array('*'));
        $orderCollection->addFieldToFilter('created_at', ['lteq' => $to])->addFieldToFilter('created_at', ['gteq' => $from]);
        if(count($orderCollection->getData())){
	        foreach($orderCollection as $order):
	        	$orderId =  $order->getId();
	        	$this->kiranaorder->kiranaorders($orderId);
	        	print_r("Order Id-->".$orderId."__");
	        endforeach;
        }
	}
	
}