<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\OrderdetailsInterface;
use Retailinsights\Promotion\Model\PromoTableFactory;
 
class Orderdetailsview implements OrderdetailsInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;
    protected $_sellerCollection;
    protected $_productCollectionFactory;
    protected $_promoFactory;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Quote\Model\QuoteFactory $quoteFactory,
       \Lof\MarketPlace\Model\Seller $sellerCollection,
       \Lof\MarketPlace\Model\SellerProduct $sellerProductCollection,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       PromoTableFactory $promoFactory
    ) {
       $this->request = $request;
       $this->quoteFactory = $quoteFactory;
       $this->_sellerCollection = $sellerCollection;
       $this->_sellerProductCollection = $sellerProductCollection;
       $this->_productCollectionFactory = $productCollectionFactory;
       $this->_promoFactory = $promoFactory;
    }

    public function orderdetails() {
        //print_r("Api execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        if($post['quote_id']){
            $quoteId= $post['quote_id'];
            $quote = $this->quoteFactory->create()->load($post['quote_id']);
            $items = $quote->getAllItems();

            ////Fetching Seller Wise Discount to calculate Cart Summary for sellers (Retail)
            $sellerWiseDiscountArray=[];
            $promotions = $this->_promoFactory->create()->getCollection()
            ->addFieldToFilter('cart_id', $quoteId);
            foreach($promotions->getData() as $promotion){   
                $promotionDiscountArrays = json_decode($promotion['promo_discount']);
                   foreach($promotionDiscountArrays as $promotionDiscountArray) {
                       foreach($promotionDiscountArray as $discountArray) {
                            $discountArray = json_decode($discountArray);
                            if(isset($sellerWiseDiscountArray[$discountArray->seller])){
                                $sellerWiseDiscountArray[$discountArray->seller]=$sellerWiseDiscountArray[$discountArray->seller]+$discountArray->amount;
                            } else {
                                $sellerWiseDiscountArray[$discountArray->seller]=$discountArray->amount;
                            }
                       }
                   }               
            }
            ////////////////////////

            $sellerData = array();
            $deliverdeatils = array();
            $pickupdeatils = array();
            $tempOrgnizedSellerIdArray = array();
            $tempOrgnizedNameArray = array();
            $tempSellerIdArray = array();
            $itemsArray = array();
            $response = array();
            $response1 = array();
            $final = array();
            $j = 0;$k = 0;
            $sellerIdPresentArray = array();
            $kiranaArray = array();
            $orgnizedRetailrArray = array();
            $orgnizedRetailrProductArray = array();
            $kiranaProductArray = array();
            $kiranaNamesArray = array();
            $selllers = array();
            $sellerCount = [];
            foreach ($items as $item) 
            {
                $organizedQtyCount = 0;
                $kiranaQtyCount = 0;
                // Pickup from store
                // if($item->getPrice_type() == 1)
                // {
                    $i = 0;
                    if(!in_array($item->getSeller_id(), $tempOrgnizedSellerIdArray))
                    {
                        $tempOrgnizedSellerIdArray[] = $item->getSeller_id();
                        // Get Seller Data
                        $sellerCollectionDetails = $this->_sellerCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $item->getSeller_id()));

                        foreach($sellerCollectionDetails as $sellcoll):
				$sellerData = $sellcoll->getData();
			   //Set Contact Number
                            if ($sellerData['contact_number']) {
                               if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $sellerData['contact_number'],  $matches ) )
                                {
                                    $result = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
                                   // print_r($result) ;
                                    $sellerData['contact_number'] = $result;
                                }
                            }
			  //Set kirana landline
			   if ($sellerData['telephone']) {
                               if(preg_match( '/(\d{2})(\d{4})(\d{4})$/', $sellerData['telephone'],  $matches ) )
                                {
                                    $result = '0'.$matches[1] . '-' .$matches[2] . '-' . $matches[3];
                                   // print_r($result) ;
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

                            $tempOrgnizedNameArray[$item->getSeller_id()]['name'] = $sellcoll->getName();
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
                            }
   
                        endforeach;
                    }
                    // Get Product doorsetp price and pick price.
                    $sellerProductCollection = $this->_sellerProductCollection->getCollection()->addFieldToFilter('seller_id', array('in' => $item->getSeller_id()))->addFieldToFilter('product_id', array('in' => $item->getProduct_id()));
                    $sellerProductData = $sellerProductCollection->getData();
                    // print_r($sellerProductData);exit;

                    // Get Product image url
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $hotPrd = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProduct_id());
                    $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
                    $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $hotPrd->getThumbnail();

                    // Get item data
                    $selllers[$item->getSeller_id()]['products'][] = array(
                        'item_id' => $item->getItem_id(),
                        'sku' => $item->getSku(),
                        'qty' => $item->getQty(),
                        'product_type' => $item->getProduct_type(),
                        'quote_id' => $item->getQuote_id(),
                        'extension_attributes' => array(
                            'image' => $hotPrd->getThumbnail(),
                            'seller_name' => $tempOrgnizedNameArray[$item->getSeller_id()]['name'],
                            'product_id' => $item->getProduct_id(),
                            'image_url' => $imageUrl,
                            'doorstep_price' => $sellerProductData[0]['doorstep_price'],
                            'pickup_from_store' => $sellerProductData[0]['pickup_from_store'],
                            'price_type' => $item->getPrice_type(),
                            'volume' => $item->getExtension_attributes()->getVolume(),
                            'seller_id' => $item->getSeller_id(),
                            'unitm' => $item->getExtension_attributes()->getUnitm()
                        )

                    );
                    $subTotal = 0;
                    $selllers[$item->getSeller_id()]['cart_summary']['total_item_count'] += $item->getQty();

                    // Single Seller Discount
                    $sellerDiscount = (isset($sellerWiseDiscountArray[$item->getSeller_id()])) ? $sellerWiseDiscountArray[$item->getSeller_id()] : 0;                    
                    if(isset($sellerCount[$item->getSeller_id()])) {
                        $sellerCount[$item->getSeller_id()] += $sellerCount[$item->getSeller_id()];
                    } else {
                        $sellerCount[$item->getSeller_id()] = 1;
                    }
                    //////////////////////////

                    if($item->getPrice_type() == 1)
                    {
                        //$subTotal = ($sellerProductData[0]['pickup_from_store'] * $item->getQty());
                        //Subtracting Seller discount from total
                        if($sellerCount[$item->getSeller_id()]==1){
                            $subTotal = ($item->getPrice() * $item->getQty()) - $sellerDiscount;
                        } else {
                            $subTotal = ($item->getPrice() * $item->getQty()); 
                        }
                    }
                    else
                    {
                        //$subTotal = ($sellerProductData[0]['doorstep_price'] * $item->getQty());
                        //Subtracting Seller discount from total
                        if($sellerCount[$item->getSeller_id()]==1){
                            $subTotal = ($item->getPrice() * $item->getQty()) - $sellerDiscount;
                        } else {
                            $subTotal = ($item->getPrice() * $item->getQty()); 
                        }
                    }
                    
                    $selllers[$item->getSeller_id()]['cart_summary']['sub_total'] += $subTotal;
                    $selllers[$item->getSeller_id()]['cart_summary']['sub_total'] = number_format((float)$selllers[$item->getSeller_id()]['cart_summary']['sub_total'], 2, '.', '');
               
            }
            $response = array();
            $i=0;
            $j=0;
            foreach ($selllers as $seller) 
            {
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
        return $data;
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
        for($i=0; $i<count($response); $i++)
        {
            $temp = $i+1;
            if($temp < count($response))
            { 
                if($response[$i]['cart_summary']['total_item_count'] > 0)
                {
                    if($response[$i]['cart_summary']['total_item_count'] == $response[$temp]['cart_summary']['total_item_count'])
                    {
                        if($response[$i]['cart_summary']['sub_total'] > $response[$temp]['cart_summary']['sub_total'])
                        {
                            $temp_array = $response[$i];
                            $response[$i] = $response[$temp];
                            $response[$temp] = $temp_array;
                        }
                    }
                }
            }
        }
        return $response;
    }
   
}
