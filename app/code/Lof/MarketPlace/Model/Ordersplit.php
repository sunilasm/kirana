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

use Magento\Framework\DataObject\IdentityInterface;
/**
 * Seller Model
 */
class Ordersplit extends \Magento\Framework\Model\AbstractModel
{	
	protected $helper;
    protected $sellerProduct;
    protected $calculate;
    protected $sender;

    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct,
        \Lof\MarketPlace\Model\Sender $sender
    ) {
        $this->sender = $sender;
        $this->calculate = $calculate;
        $this->helper      = $helper;
        $this->sellerProduct = $sellerProduct; 
    }

    public function kiranaorders($orderId) {
        if($orderId){
            $objectManager       = \Magento\Framework\App\ObjectManager::getInstance ();
            $orderDetails        = $objectManager->get ( 'Magento\Sales\Model\Order' );
            $orderData           = $orderDetails->load ( $orderId );
      
            $currencyCode        = $orderData->getOrderCurrencyCode();
            $orderShippingAmount = $orderDetails->getShippingAmount();
            $incrementId         = $orderDetails->getIncrementId();
            $customerId          = $orderDetails->getCustomerId();
            $orderItems          = $orderData->getAllItems ();
            $sellerData          = array ();
            $customOptions       = array ();

            foreach ( $orderItems as $item ) {
                $productId = $item->getProductId ();
                $itemId = $item->getItemId ();
                $customOptions = $item->getProductOptions ();
                $customOptionArray = json_encode ( $customOptions );
                $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
                //$sellerId = $product->getSellerId();
                $sellerId = $item->getSellerId();
                $priceComparison = $this->helper->isEnableModule('Lofmp_PriceComparison');
                if($priceComparison) {
                    $assignHelper = $objectManager->create('Lofmp\PriceComparison\Helper\Data');
                    $quote = $assignHelper->getQuoteCollection()->addFieldToFilter('product_id',$productId)->addFieldToFilter('customer_id',$customerId)->getLastItem();
                    if(count($quote->getData())>0) {
                        $sellerId = $quote->getSellerId();
                    } else {
                        $sellerId = $product->getSellerId();
                    }
                } else {
                    $sellerId = $product->getSellerId();
                }
                $sellerId = $item->getSellerId();
                 if (! empty ( $sellerId ) && $item->getParentItemId () == '') {
                    $sellerDatas = $objectManager->get ( 'Lof\MarketPlace\Model\Seller' )->load ( $sellerId, 'seller_id' );
                    $sellerProduct = $objectManager->get ( 'Lof\MarketPlace\Model\SellerProduct' )->load ( $productId, 'product_id' );               

                    $commission = $this->helper->getCommission($sellerId,$productId);
                   
                    $productSku = $item->getSku ();
                    $productName = $item->getName ();
                    $productPrice = $item->getData('row_total')+ $item->getData('tax_amount');
                    $discount_amount = $item->getDiscountAmount();
                    $priceBase = $item->getData('row_total');
                    $baseProductPrice = $item->getBasePrice ();
                    $priceCommission = $this->calculate->calculate($commission,$item);
                    $seller_commission = $priceCommission;
                    $admin_commission = $item->getData('row_total') + $item->getData('tax_amount') - $item->getData('discount_amount') - $priceCommission;

                    $sellerOrderItemsModel = $objectManager->create ( 'Lof\MarketPlace\Model\Orderitems' );
                    $productQty = $item->getQtyOrdered ();
                    $sellerOrderItemsModel->setProductId ( $productId )->setProductSku ( $productSku )->setProductName ( $productName )->setSellerId ( $sellerId )->setOrderId ( $orderId )->setProductPrice ( $productPrice )->setBaseProductPrice ( $baseProductPrice )->setOrderItemId ( $itemId )->setProductQty ( $productQty )->setProductSku ( $productSku )->setProductName ( $productName )->setCommission ( $commission )->setOptions ( $customOptionArray )->setStatus ( $orderData->getData('status') )->setSellerCommissionOrder($seller_commission)->setAdminCommissionOrder($admin_commission)->save ();

                    $sellerShippingAmount = 0;

                    $country = $sellerDatas->getCountry ();
                    
                    $nationalShippingAmount = $product->getNationalShippingAmount ();
                    $internationalShippingAmount = $product->getInternationalShippingAmount ();
                
                    $sellerShippingAmount = 0;
                    if ($orderData->getBillingAddress ()->getCountryId () == $country) {
                        $sellerShippingAmount = $nationalShippingAmount * $productQty;
                    } else {
                        $sellerShippingAmount = $internationalShippingAmount * $productQty;
                    }
                    if(is_array($commission)) {
                        $seller_commission = $commission['commission_amount'];
                    } else {
                        $seller_commission = $commission;
                    }
                    if (array_key_exists ( $sellerId, $sellerData )) {
                        $sellerData [$sellerId] ['price'] += $productPrice -$discount_amount;
                        $sellerData [$sellerId] ['commission'] = $seller_commission;
                        $sellerData [$sellerId] ['amount'] += $priceCommission;
                        $sellerData [$sellerId] ['seller_id'] = $sellerId;
                        $sellerData [$sellerId] ['shipping'] += $sellerShippingAmount;
                        $sellerData [$sellerId] ['discount_amount'] += $discount_amount;
                    } else {
                        $sellerData [$sellerId] ['price'] = $productPrice - $discount_amount;
                        $sellerData [$sellerId] ['commission'] = $seller_commission;
                        $sellerData [$sellerId] ['seller_id'] = $sellerId;
                        $sellerData [$sellerId] ['amount'] = $priceCommission;
                        $sellerData [$sellerId] ['shipping'] = $sellerShippingAmount;
                        $sellerData [$sellerId] ['discount_amount'] = $discount_amount;
                    }
                 
                }
            }
            $customerOrderDetails = $objectManager->get ( 'Magento\Sales\Model\Order' );
            $customerOrderData = $customerOrderDetails->load ( $orderId );
            foreach ( $sellerData as $sellerIds ) {
                $sellerId = $sellerIds ['seller_id'];
                $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
                if ($customerSession->isLoggedIn ()) {
                    $customerId = $customerSession->getId ();
                }
                $products = $objectManager->get ( 'Lof\MarketPlace\Model\Orderitems' )->getCollection ();
                $products->addFieldToSelect ( '*' );
                $products->addFieldToFilter ( 'order_id', $orderId );
                $products->addFieldToFilter ( 'seller_id', $sellerId );
                $productIds = array_unique ( $products->getColumnValues ( 'product_id' ) );
                
                $orderShippingAmount = $customerOrderData->getShippingAmount ();
                $totalSellerShippingQty = $totalShippingQty = $shippingAmount = 0;
                foreach ( $orderDetails->getAllItems () as $item ) {
                    $itemProductId = $item->getProductId ();
                    if (in_array ( $itemProductId, $productIds ) && $item->getIsVirtual () != 1) {
                        $totalSellerShippingQty = $totalSellerShippingQty + $item->getQtyOrdered ();
                    }
                    if ($item->getIsVirtual () != 1) {
                        $totalShippingQty = $totalShippingQty + $item->getQtyOrdered ();
                    }
                }
                if (! empty ( $orderShippingAmount ) && ! empty ( $totalSellerShippingQty ) && ! empty ( $totalShippingQty )) {
                    $shippingAmount = round ( $orderShippingAmount * ($totalSellerShippingQty / $totalShippingQty), 2 );
                }
                //$sellerAmount = $sellerIds ['price'] - $sellerIds ['commission'];
                $name = '';
                if($sellerIds ['seller_id']){
                    $sellerData = $objectManager->get ( 'Lof\MarketPlace\Model\Seller' )->load ( $sellerIds ['seller_id'], 'seller_id' );
                    $name = $sellerData->getData('name');  
                }
                
                $sellerOrderModel = $objectManager->create ( 'Lof\MarketPlace\Model\Order' );
                $sellerOrderModel->setName($name)->setSellerId ( $sellerIds ['seller_id'] )->setOrderId ( $orderId )->setSellerProductTotal ( $sellerIds ['price'] )->setCommission ( $sellerIds ['commission'] )->setSellerAmount ( $sellerIds ['amount'] )->setIncrementId ( $incrementId )->setOrderCurrencyCode ( $currencyCode )->setCustomerId ( $customerId )->setShippingAmount ( $shippingAmount )->setStatus ( $orderData->getData('status') )->setDiscountAmount($sellerIds ['discount_amount'])->save ();
                /**
                 * Send order details to seller
                 */
                $data = [];
                $data['email']= $sellerDatas->getData('email');
                $data['name'] = $sellerDatas->getData('name');
                $data['order_id'] = $orderId;
                $data['order_status'] = $orderData->getData('status');
                if($this->helper->getConfig('email_settings/enable_send_email')) {
                    $this->sender->newOrder($data);
                }
            }

        }

     }
}