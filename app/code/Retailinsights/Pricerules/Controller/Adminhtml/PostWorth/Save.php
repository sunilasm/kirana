<?php
namespace Retailinsights\Pricerules\Controller\Adminhtml\PostWorth;

class Save extends \Magento\Backend\App\Action
{

	 //protected $resultPageFactory;
	 protected $jsonFactory;
     protected $_postCollection;

     public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Retailinsights\Pricerules\Model\PostWorthFactory $postFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Retailinsights\Pricerules\Model\ResourceModel\PostWorth\CollectionFactory $postCollection
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
        
            $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/post_worth_pricerules_save_data.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("========");  

            /*test-2*/

             $flag = 0;

            
            //imploding the customer Group.
            
            $customer_group=$this->getRequest()->getParam('customer_group');
            
            $customer_group_str = implode(",",$customer_group);
            $logger->info("imploding the customer Group =".$customer_group_str);
                    $post_id=$this->getRequest()->getParam('post_id');
                    $name=$this->getRequest()->getParam('name');
                    $store_id=$this->getRequest()->getParam('store_id');

                    $subtotal=$this->getRequest()->getParam('subtotal');
                    $get_product=$this->getRequest()->getParam('get_product');
                    $quantity=$this->getRequest()->getParam('quantity');
                    $priority=$this->getRequest()->getParam('priority');
                    $status=$this->getRequest()->getParam('status');
                    $fromdate_offer=$this->getRequest()->getParam('fromdate_offer');
                    $todate_offer=$this->getRequest()->getParam('todate_offer');
                
                    
                    if($post_id>0){

                         
                        $logger->info("1post_id:" .$post_id); 
                        $logger->info("subtotal:" .$subtotal); 
                        $logger->info("get product:" .$get_product); 
                        $logger->info("quantity:" .$quantity); 
                        $logger->info("priority:" .$priority); 
                      
                    $logger->info("status:" .$status);
                        $logger->info("fromdate offer:" .$fromdate_offer); 
                        $logger->info("todate offer:" .$todate_offer); 
                        $logger->info("customer group:" .$customer_group_str); 
    
                        $mandeetotcol = $this->_savedata->create();
                        
                        $mandeetotcol->setPostId($post_id);
                        $mandeetotcol->setName($name);
                        $mandeetotcol->setStoreId($store_id);

                        $mandeetotcol->setSubtotal($subtotal);
                        $mandeetotcol->setGetProduct($get_product);
                        $mandeetotcol->setQuantity($quantity);
                       
                        $mandeetotcol->setPriority($priority);
                       
    
                        $mandeetotcol->setOfferFrom($fromdate_offer);
                        $mandeetotcol->setOfferTo($todate_offer);
                        $mandeetotcol->setCustomerGroup($customer_group_str);
                        $mandeetotcol->setStatus($status);
                        $this->messageManager->addSuccess(__('You saved the rule.'));
                        $mandeetotcol->save();
    
                        return $this->_redirect('retailinsights_pricerules/postworth/index/');
                    }
                    else{
  
                    $logger->info("1subtotal:" .$subtotal); 
                    $logger->info("1Fixed Price:" .$get_product); 
                    $logger->info("1quantity:" .$quantity); 
                    $logger->info("1priority:" .$priority); 
                    $logger->info("status:" .$status);
                    $logger->info("1fromdate offer:" .$fromdate_offer); 
                    $logger->info("1todate offer:" .$todate_offer); 
                    $logger->info("1customer group:" .$customer_group_str); 

                    $mandeetotcol = $this->_savedata->create();
                    $mandeetotcol->setSubtotal($subtotal);
                    $mandeetotcol->setGetProduct($get_product);
                    $mandeetotcol->setQuantity($quantity);
                   
                    $mandeetotcol->setName($name);
                    $mandeetotcol->setStoreId($store_id);
                    $mandeetotcol->setPriority($priority);
                   

                    $mandeetotcol->setOfferFrom($fromdate_offer);
                    $mandeetotcol->setOfferTo($todate_offer);
                    $mandeetotcol->setCustomerGroup($customer_group_str);
                    $mandeetotcol->setStatus($status);
                    $this->messageManager->addSuccess(__('You saved the rule.'));
                    $mandeetotcol->save();

                    return $this->_redirect('retailinsights_pricerules/postworth/index/');


                    //return $this->jsonFactory->create()->setData($todate_offer);
                }      
         }
        }     
    }
?>