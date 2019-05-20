<?php
namespace Retailinsights\Pricerules\Controller\Adminhtml\PostXYZ;

class Save extends \Magento\Backend\App\Action
{

	 //protected $resultPageFactory;
	 protected $jsonFactory;
     protected $_postCollection;

     public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Retailinsights\Pricerules\Model\PostXYZFactory $postFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Retailinsights\Pricerules\Model\ResourceModel\PostXYZ\CollectionFactory $postCollection
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
        
            $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/post_xyz_pricerules_save_data.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("========");  

            /*test-2*/

             $flag = 0;

            
            //imploding the customer Group.
            
            $customer_group=$this->getRequest()->getParam('customer_group');
            
            $customer_group_str = implode(",",$customer_group);
            $logger->info("imploding the customer Group =".$customer_group_str);

                  //  $genral =$this->getRequest()->getParams('genral');

                  $post_id=$this->getRequest()->getParam('post_id');

                  $name=$this->getRequest()->getParam('name');
                    $status=$this->getRequest()->getParam('status');
                    $store_id=$this->getRequest()->getParam('store_id');

                  $rule_condition=$this->getRequest()->getParam('rule_condition');        
                  $discount_product=$this->getRequest()->getParam('discount_product');
                  $discount=$this->getRequest()->getParam('discount');
                  
                  $priority=$this->getRequest()->getParam('priority');
                
                  $fromdate_offer=$this->getRequest()->getParam('fromdate_offer');
                  $todate_offer=$this->getRequest()->getParam('todate_offer');

                if($post_id>0){

                    $logger->info("1post_id:" .$post_id); 
                    $logger->info("1rule condition:" .$rule_condition); 
                    $logger->info("discount_product:" .$discount_product); 
                    $logger->info("1priority:" .$priority); 

                  
                    $logger->info("1fromdate offer:" .$fromdate_offer); 
                    $logger->info("1todate offer:" .$todate_offer); 
                    $logger->info("1customer group:" .$customer_group_str); 

                    $mandeetotcol = $this->_savedata->create();
                        
                    $mandeetotcol->setPostId($post_id);
                    
                   
                    $mandeetotcol->setStatus($status);

                    $mandeetotcol->setRuleCondition($rule_condition);
                    $mandeetotcol->setDiscountProduct($discount_product);
                    $mandeetotcol->setDiscount($discount);
                    $mandeetotcol->setName($name);
                    $mandeetotcol->setPriority($priority);
                    $mandeetotcol->setStoreId($store_id);
                    $mandeetotcol->setOfferFrom($fromdate_offer);
                    $mandeetotcol->setOfferTo($todate_offer);
                    $mandeetotcol->setCustomerGroup($customer_group_str);
                    
                    $this->messageManager->addSuccess(__('You saved the rule.'));
                    $mandeetotcol->save();

                    return $this->_redirect('retailinsights_pricerules/postxyz/index/');
                    
                }
               else{
                    
                    $logger->info("rule condition:" .$rule_condition); 
                    $logger->info("discount_product:" .$discount_product); 
                    $logger->info("priority:" .$priority); 

                  
                    $logger->info("fromdate offer:" .$fromdate_offer); 
                    $logger->info("todate offer:" .$todate_offer); 
                    $logger->info("customer group:" .$customer_group_str); 

                    $mandeetotcol = $this->_savedata->create();
                        
                    $mandeetotcol->setRuleCondition($rule_condition);
                    $mandeetotcol->setDiscountProduct($discount_product);
                    $mandeetotcol->setStatus($status);

                    $mandeetotcol->setDiscount($discount);
                    $mandeetotcol->setName($name);
                    $mandeetotcol->setPriority($priority);
                    $mandeetotcol->setStoreId($store_id);
                   
                    $mandeetotcol->setOfferFrom($fromdate_offer);
                    $mandeetotcol->setOfferTo($todate_offer);
                    $mandeetotcol->setCustomerGroup($customer_group_str);
                   
                    $this->messageManager->addSuccess(__('You saved the rule.'));
                    $mandeetotcol->save();

                    return $this->_redirect('retailinsights_pricerules/postxyz/index/');


                    //return $this->jsonFactory->create()->setData($todate_offer);
                }

         }
        }     
    }
?>