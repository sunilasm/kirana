<?php

namespace Retailinsights\Pricerules\Controller\Adminhtml\Post;

class Save extends \Magento\Backend\App\Action
{

	 //protected $resultPageFactory;
	 protected $jsonFactory;
     protected $_postCollection;

     public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Retailinsights\Pricerules\Model\PostFactory $postFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Retailinsights\Pricerules\Model\ResourceModel\Post\CollectionFactory $postCollection
    ) {
        parent::__construct($context);
        $this->_savedata=$postFactory;
        $this->jsonFactory = $jsonFactory;
        //$this->resultPageFactory = $resultPageFactory;
        $this->_postCollection = $postCollection; 
    }


    public function execute()
    {

        /* test-1 */
        /*$postCollectionObject = $this->_postCollection->create();
        $collectionData = $postCollectionObject->addFieldToSelect('buy_product')->load('*');
        print_r($collectionData->getData());
        die();*/

  if ($this->getRequest()->isAjax()) 
        {
        
            

            /*test-2*/

             $flag = 0;

            $postCollectionObject = $this->_postCollection->create();
            $collectionData = $postCollectionObject->addFieldToSelect('buy_product')->load('*');
            //print_r($collectionData->getData());

            /*Buy product list validate to the table*/     

            $buy_product_explode=$this->getRequest()->getParam('buy_product');  


            
           
            $arr_buy_product = explode(",",$buy_product_explode);

           

            // $sorted_data = natsort($buy_product_explode);
            //$result = strnatcasecmp($buy_product_list['buy_product']);

            //sorting the array now
            sort($arr_buy_product);
           
            
            $buy_product_implode = implode(",",$arr_buy_product);
           

            //imploding the customer Group.
            
            $customer_group=$this->getRequest()->getParam('customer_group');
            
            $customer_group_str = implode(",",$customer_group);
           

       
                $post_id=$this->getRequest()->getParam('post_id');
                $name=$this->getRequest()->getParam('name');
                $store_id=$this->getRequest()->getParam('store_id');
                //$buy_product=$this->getRequest()->getParam('buy_product');
                    $buy_quantity=$this->getRequest()->getParam('buy_quantity');
                    $get_product=$this->getRequest()->getParam('get_product');
                    $get_quantity=$this->getRequest()->getParam('get_quantity');
                    $priority=$this->getRequest()->getParam('priority');
                     //$name=$this->getRequest()->getParam('name');
                     $status=$this->getRequest()->getParam('status');

                    $fromdate_offer=$this->getRequest()->getParam('fromdate_offer');
                    $todate_offer=$this->getRequest()->getParam('todate_offer');
                    
            if($post_id>0){

               
                    $mandeetotcol = $this->_savedata->create();

                    $mandeetotcol->setPostId($post_id);
                    $mandeetotcol->setBuyProduct($buy_product_implode);
                    $mandeetotcol->setBuyQuantity($buy_quantity);
                    $mandeetotcol->setGetProduct($get_product);
                    $mandeetotcol->setGetQuantity($get_quantity);

                    $mandeetotcol->setPriority($priority);

                    $mandeetotcol->setName($name);
                    $mandeetotcol->setStoreId($store_id);
                    
                  
                    $mandeetotcol->setOfferFrom($fromdate_offer);
                    $mandeetotcol->setOfferTo($todate_offer);
                    $mandeetotcol->setCustomerGroup($customer_group_str);
                    $mandeetotcol->setStatus($status);
                   
                    $this->messageManager->addSuccess(__('You saved the rule.'));
                    $mandeetotcol->save();

                    return $this->_redirect('retailinsights_pricerules/post/index/');


                    //return $this->jsonFactory->create()->setData($todate_offer);
                    }
                    else{
                        
                   

                    $mandeetotcol = $this->_savedata->create();
                    $mandeetotcol->setBuyProduct($buy_product_implode);
                    $mandeetotcol->setBuyQuantity($buy_quantity);
                    $mandeetotcol->setGetProduct($get_product);
                    $mandeetotcol->setGetQuantity($get_quantity);

                    $mandeetotcol->setPriority($priority);

                    $mandeetotcol->setName($name);
                    $mandeetotcol->setStoreId($store_id);
                    
                    $mandeetotcol->setOfferFrom($fromdate_offer);
                    $mandeetotcol->setOfferTo($todate_offer);
                    $mandeetotcol->setCustomerGroup($customer_group_str);
                    $mandeetotcol->setStatus($status);
                    $this->messageManager->addSuccess(__('You saved the rule.'));
                    $mandeetotcol->save();

                    return $this->_redirect('retailinsights_pricerules/post/index/');


                    }
	
        }

	}
}




            /*other logic which not implimented*/


            /*
        $arr_buy_product = explode(",",$buy_product_explode);
        $size_inserted_buy_prdct = count($arr_buy_product);

        $logger->info("length of inserted data ?????????");
        $logger->info($size_inserted_buy_prdct);

        $flag = 0;
        $logger->info("after explode");
            foreach($arr_buy_product as $i){
                $logger->info($i);
            
                foreach ($buy_product_list as $j) {
                    $logger->info("data in list<><><><><>");
                    $logger->info($j);
                    
                    $after_explode = explode(",",$j['buy_product']);
                    $size_db_buy_prdct = count($after_explode);

                    $logger->info("length of that data?????????");
                    $logger->info($size_db_buy_prdct);

                    if($size_inserted_buy_prdct == $size_db_buy_prdct)
                    {
                        $logger->info("then only");
                        $logger->info("exploding here......");
                        foreach ($after_explode as $value) {
                            //foreach ($after_explode as $key => $value) {
                            //$logger->info("after_explode[".$key."] = ".$value);
                            //$logger->info("after_explode");
                            //comparing with buy product field
                            if($i == $value)
                            {
                                $logger->info("db value =".$value);
                                $logger->info("field value =".$i);
                                $flag ++;
                                $logger->info("flag =".$flag);
                            }
                              
                            else{
                                $logger->info("db value =".$value);
                                $logger->info("field value =".$i);
                                //$flag = 0;
                                $logger->info("flag =".$flag);
                            }                  
                        }

                    }
                    else{
                        $logger->info("Size not Mattched ");
                        $logger->info("flag =".$flag);
                    }
                    
              
                }
            }

        $logger->info("loop ends here");
        $logger->info("Final flag value".$flag);
        //for($i = 0; i)
        */

?>