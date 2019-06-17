<?php
namespace Asm\Timeslot\Model;
use Asm\Timeslot\Api\TimeslotInterface;
use Retailinsights\Promotion\Model\PromoTableFactory;
 
class Timeslotview implements TimeslotInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;
    protected $orderRepository;
    protected $_sellerCollection;
    protected $_promoFactory;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
       \Lof\MarketPlace\Model\Seller $sellerCollection,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection,
       \Asm\Timeslot\Model\TimeslotFactory $timeslotCollection,
       PromoTableFactory $promoFactory
    ) {
       $this->request = $request;
       $this->orderRepository = $orderRepository;
       $this->_sellerCollection = $sellerCollection;
       $this->_sellerProductCollection = $sellerProductCollection;
       $this->_timeslot = $timeslotCollection;
       $this->_promoFactory = $promoFactory;
    }

    public function timeslot() {
        // print_r("Execute successfully");exit;
        $tempOrgnizedNameArray = array();
        $tempOrgnizedSellerIdArray = array();
        
        // Set Data in time slot table
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $order = $this->orderRepository->get($post['order_id']);
        $orderIncrementId = $order->getIncrementId();

        $resultPage = $this->_timeslot->create();
        $collectionTimeslot = $resultPage->getCollection();
        $collectionTimeslot->addFieldToFilter('order_id',$post['order_id']); 
       // print_r($collectionTimeslot->getData());exit;
        if(!count($collectionTimeslot)){
          $resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
          $connection = $resource->getConnection();
          $tableName = $resource->getTableName('order_time_slot');
          
          foreach($post['slots'] as $slot):
              if($slot['time_slot_type']){
                  $sql = "INSERT INTO " . $tableName . "(order_id, order_increment_id, store_id, time_slot_type, date_slot, time_slot) VALUES('" . $post['order_id'] . "','" . $orderIncrementId . "','" . $slot['store_id'] . "','" . $slot['time_slot_type'] . "','" . $slot['date_slot'] . "','" . $slot['time_slot'] . "')";
                  // print_r($sql);exit;

              }else{
                  $sql = "INSERT INTO " . $tableName . "(order_id, order_increment_id, store_id, time_slot_type, date_slot, time_slot) VALUES('" . $post['order_id'] . "','" . $orderIncrementId . "','" . $slot['store_id'] . "','" . $slot['time_slot_type'] . "','NULL','NULL')";
              }
            $connection->query($sql);
          endforeach;
        }    

        // Return order summery 
       
        
        $items = $order->getAllItems();

        // Queries to Store discount in Quote Tables  (Retail)
        $quoteId = $order->getQuoteId();
        $subTotal = $order->getBaseSubtotal();
        $total_disc = 0;
        $promotions = $this->_promoFactory->create()->getCollection()
        ->addFieldToFilter('cart_id', $quoteId);
        foreach($promotions->getData() as $promotion){   
            $total_disc = $promotion['total_discount'];
        }
        $newSubTotal = ($subTotal - $total_disc);
        $total_disc = '-'.$total_disc;
  
        $resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $quoteAddressTableName = $resource->getTableName('quote_address');
        $quoteTableName = $resource->getTableName('quote');
        $salesOrderTableName = $resource->getTableName('sales_order');

        $sqlQuoteAdd = "Update $quoteAddressTableName Set subtotal=".$subTotal.", base_subtotal=".$subTotal.", subtotal_with_discount =".$newSubTotal.", base_subtotal_with_discount=".$newSubTotal.",  grand_total=".$newSubTotal.",  base_grand_total=".$newSubTotal.",	discount_amount=".$total_disc.", base_discount_amount =".$total_disc." where quote_id =".$quoteId ;
        $connection->query($sqlQuoteAdd);

        $sqlQuote = "Update $quoteTableName Set subtotal=".$subTotal.", base_subtotal =".$subTotal.", subtotal_with_discount =".$newSubTotal.", base_subtotal_with_discount=".$newSubTotal.", grand_total=".$newSubTotal.", base_grand_total=".$newSubTotal." where entity_id = ".$quoteId ;
        $connection->query($sqlQuote);

        $sqlOrder = "Update $salesOrderTableName Set grand_total=".$newSubTotal.",total_due=".$newSubTotal.",base_total_due=".$newSubTotal.", base_grand_total=".$newSubTotal.",	discount_amount=".$total_disc.", base_discount_amount =".$total_disc." where quote_id =".$quoteId ;
        $connection->query($sqlOrder);
        
        ///////////////////////////

        ////Fetching Seller Wise Discount to calculate Cart Summary for sellers (Retail)
        $sellerWiseDiscountArray=[];
        $promotions = $this->_promoFactory->create()->getCollection()
        ->addFieldToFilter('cart_id', $quoteId);
        foreach($promotions->getData() as $promotion){   
            $promotionDiscountArrays = json_decode($promotion['promo_discount']);
               foreach($promotionDiscountArrays as $promotionDiscountArray) {
                   foreach($promotionDiscountArray as $discountArray) {
                        $discountArray = json_decode($discountArray);
                        $sellerWiseDiscountArray[$discountArray->seller]=$discountArray->amount;
                   }
               }               
        }
        ////////////////////////


        foreach ($items as $item) {
          // print_r($item->getData());
          // print_r($item->getData());
            if(!in_array($item->getSeller_id(), $tempOrgnizedSellerIdArray))
            {
                $tempOrgnizedSellerIdArray[] = $item->getSeller_id();
                // Get Seller Data
                $sellerCollectionDetails = $this->_sellerCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $item->getSeller_id()));

                foreach($sellerCollectionDetails as $sellcoll):
                    $tempOrgnizedNameArray[$item->getSeller_id()]['name'] = $sellcoll->getName();
                    $sellerData = $sellcoll->getData();
		    //Set kirana landline
		    if ($sellerData['contact_number']) {
                        if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $sellerData['contact_number'],  $matches ) )
                        {
                           $result = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
        		   $sellerData['contact_number'] = $result;
    			}
		    }
	
		   //Set kirana landline
                    if ($sellerData['telephone']) {
                        if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $sellerData['telephone'],  $matches ) )
                        {
                           $result = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
                           $sellerData['telephone'] = $result;
                        }
                    }

		   //Set kirana fax
                    if ($sellerData['kirana_fixed_line']) {
                        if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $sellerData['kirana_fixed_line'],  $matches ) )
                        {
                           $result = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
                           $sellerData['kirana_fixed_line'] = $result;
                        }
                    }



		    $selllers[$item->getSeller_id()]['store'] = $sellerData;
                    $selllers[$item->getSeller_id()]['cart_summary']['total_item_count'] = 0;
                    $selllers[$item->getSeller_id()]['cart_summary']['sub_total'] = 0;
                    if($item->getPrice_type() == 1)
                    {
                        $selllers[$item->getSeller_id()]['type'] = 'org';
                    }
                    else
                    {
                        $selllers[$item->getSeller_id()]['type'] = 'kirana';
                        $orderObj = $objectManager->create('Magento\Sales\Model\Order')->load($post['order_id']);
                        $shipAddress = $orderObj->getShippingAddress()->getData();
			 //Set kirana landline
                    	if ($shipAddress['telephone']) {
                           if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $shipAddress['telephone'],  $matches ) )
                           {
                             $result = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
                             $shipAddress['telephone'] = $result;
                           }
                        }

			$selllers[$item->getSeller_id()]['customer_info'] = $shipAddress;
                    }

                endforeach;
            }

            $subTotal = 0;
            // print_r($item->getPrice_type());exit;
            $selllers[$item->getSeller_id()]['cart_summary']['total_item_count'] += $item->getQty_ordered();
            $sellerProductCollection = $this->_sellerProductCollection->getCollection()->addFieldToFilter('product_id', array('in' => $item->getProduct_id()))->addFieldToFilter('seller_id', array('in' => $item->getSeller_id()));
                    // print_r($sellerProductCollection->getData());exit;
            $sellerDiscount = (isset($sellerWiseDiscountArray[$item->getSeller_id()])) ? $sellerWiseDiscountArray[$item->getSeller_id()] : 0; // Single Seller Discount
            foreach($sellerProductCollection as $sellProducts){
                        // print_r($sellProducts->getPickup_from_store());exit;
                if($item->getPrice_type() == 1)
                {
                    // print_r("1111");exit;
                    //$subTotal = ($sellProducts->getPickup_from_store() * $item->getQty_ordered());

                    $subTotal = ($item->getPrice() * $item->getQty_ordered()) - $sellerDiscount ;

                }
                else
                {
                    // print_r("2222");exit;
                    //$subTotal = ($sellProducts->getDoorstep_price() * $item->getQty_ordered());

                    $subTotal = ($item->getPrice() * $item->getQty_ordered()) - $sellerDiscount;

                }
            }
            // print_r($subTotal);exit;
            $selllers[$item->getSeller_id()]['cart_summary']['sub_total'] += $subTotal;
            $selllers[$item->getSeller_id()]['cart_summary']['sub_total'] = number_format((float)$selllers[$item->getSeller_id()]['cart_summary']['sub_total'], 2, '.', '');

            $resultPage = $this->_timeslot->create();
            $collectionTimeslot = $resultPage->getCollection();
            $collectionTimeslot->addFieldToFilter('order_id',$post['order_id']); 
            $collectionTimeslot->addFieldToFilter('store_id',$item->getSeller_id()); 
            $timeSlot = $collectionTimeslot->getData();
            //print_r($collectionTimeslot->getSelect()->__toString());exit;
            if(count($timeSlot)){
                $selllers[$item->getSeller_id()]['time_slot_type'] = $timeSlot[0]['time_slot_type'];
                $selllers[$item->getSeller_id()]['date_slot'] = $timeSlot[0]['date_slot'];
                $selllers[$item->getSeller_id()]['time_slot'] = $timeSlot[0]['time_slot'];
            }
            
            $response = array();
            $i=0;
            $j=0;
            foreach ($selllers as $seller) {
                if($seller['type'] == 'org')
                {
                    $response['pick_up_from_store'][$i] = $seller;
                    $i++;
                }
                else
                {
                    $response['deliver_by_kirana'][$j] = $seller;
                    $j++;   
                }
                
            }
            if(isset($response['pick_up_from_store']) && count($response['pick_up_from_store']))
            {
                $temp_response = $this->sort_by_total_item_count($response['pick_up_from_store']);
                $response['pick_up_from_store'] = $temp_response;
            }
            $data = array($response);

        }
        // exit;
 //print_r($selllers);echo "<br/>";exit;
        return $data;
       // print_r($selllers);echo "<br/>";
        // exit;       
    }
    private function sort_by_total_item_count($array) 
    {
        $sorter = array();
        $ret = array();
        reset($array);
        $count_array = array();
        foreach($array as $key => $store)
        {
            $count_array[$key] = $store['cart_summary']['total_item_count'];
        }
        arsort($count_array);
        $response = array();
        foreach($count_array as $key => $value)
        {
            $response[] = $array[$key];
        }
        //print_r($response);exit;
        return $response;
    }
}

