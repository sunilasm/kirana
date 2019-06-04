<?php
namespace Retailinsights\Pricerules\Controller\Adminhtml\Postthree;

class Save extends \Magento\Backend\App\Action
{

	 //protected $resultPageFactory;
	 protected $jsonFactory;
     protected $_postCollection;

     public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Retailinsights\Pricerules\Model\PostthreeFactory $postFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Retailinsights\Pricerules\Model\ResourceModel\Postthree\CollectionFactory $postCollection
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
            $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/post_twofixed_pricerules_save_data.log');
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
                    $buy_product_one=$this->getRequest()->getParam('buy_product_one');
                    $buy_product_two=$this->getRequest()->getParam('buy_product_two');
                  
                    $fixed_price=$this->getRequest()->getParam('fixed_price');
                    $priority=$this->getRequest()->getParam('priority');
                    $status=$this->getRequest()->getParam('status');
                    $name=$this->getRequest()->getParam('name');
                    $store_id=$this->getRequest()->getParam('store_id');
                    
                    $fromdate_offer=$this->getRequest()->getParam('fromdate_offer');
                    $todate_offer=$this->getRequest()->getParam('todate_offer');
                    
                    if($post_id>0){
                        $logger->info("1post_id:" .$post_id); 
                        $logger->info("buy product:" .$buy_product_one); 
                 
                    $logger->info("fixed price:" .$fixed_price); 
                    $logger->info("priority:" .$priority); 

                    $logger->info("status:" .$status);
                  
                    $logger->info("fromdate offer:" .$fromdate_offer); 
                    $logger->info("todate offer:" .$todate_offer); 
                    $logger->info("customer group:" .$customer_group_str); 

                    $mandeetotcol = $this->_savedata->create();

                    $mandeetotcol->setPostId($post_id);
                    $mandeetotcol->setBuyProductOne($buy_product_one);
                    $mandeetotcol->setBuyProductTwo($buy_product_two);
                   
                    $mandeetotcol->setFixedPrice($fixed_price);
                    
                    $mandeetotcol->setName($name);
                    $mandeetotcol->setStoreId($store_id);

                    $mandeetotcol->setPriority($priority);
                    $mandeetotcol->setStatus($status);
                   

                    $mandeetotcol->setOfferFrom($fromdate_offer);
                    $mandeetotcol->setOfferTo($todate_offer);
                    $mandeetotcol->setCustomerGroup($customer_group_str);
                    
                    $this->messageManager->addSuccess(__('You saved the rule.'));
                    $mandeetotcol->save();

                    return $this->_redirect('retailinsights_pricerules/posttree/index/');

                    }
                else{
                    $logger->info("buy product:" .$buy_product_one); 
              
                    $logger->info("fixed price:" .$fixed_price); 
                    $logger->info("priority:" .$priority); 
                  
                    $logger->info("fromdate offer:" .$fromdate_offer); 
                    $logger->info("todate offer:" .$todate_offer); 
                    $logger->info("customer group:" .$customer_group_str); 
                    $logger->info("status:" .$status);

                    $mandeetotcol = $this->_savedata->create();
                    $mandeetotcol->setBuyProductOne($buy_product_one);
                    $mandeetotcol->setBuyProductTwo($buy_product_two);
              
                    $mandeetotcol->setFixedPrice($fixed_price);
                    
                    $mandeetotcol->setName($name);
                    $mandeetotcol->setStoreId($store_id);

                    $mandeetotcol->setPriority($priority);
                    $mandeetotcol->setStatus($status);

                    $mandeetotcol->setOfferFrom($fromdate_offer);
                    $mandeetotcol->setOfferTo($todate_offer);
                    $mandeetotcol->setCustomerGroup($customer_group_str);
                  
                    $this->messageManager->addSuccess(__('You saved the rule.'));
                    $mandeetotcol->save();

                    return $this->_redirect('retailinsights_pricerules/posttree/index/');
            }     
         }
        }     
    }
?>