<?php

namespace Retailinsights\Pricerules\Controller\Adminhtml\PostBWGY;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Backend\App\Action
{
	 protected $jsonFactory;
     protected $_postCollection;

     public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Retailinsights\Pricerules\Model\PostBWGYFactory $postFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Retailinsights\Pricerules\Model\ResourceModel\PostBWGY\CollectionFactory $postCollection
    ) {
        parent::__construct($context);
        $this->_savedata=$postFactory;
        $this->jsonFactory = $jsonFactory;
        $this->_postCollection = $postCollection; 
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
  if ($this->getRequest()->isAjax()) 
        {
             $flag = 0;

             $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testBWGY.log'); 
             $logger = new \Zend\Log\Logger(); $logger->addWriter($writer); 
             $logger->info('testBWGY');
           
                $customer_group=$this->getRequest()->getParam('customer_group'); 
                $customer_group_str = implode(",",$customer_group);
       
                $post_id=$this->getRequest()->getParam('post_id');
                $name=$this->getRequest()->getParam('name');
                $store_id=$this->getRequest()->getParam('store_id');
                $fixed_amount=$this->getRequest()->getParam('fixed_amount');
                $condition=$this->getRequest()->getParam('condition');
               
                $get_product=$this->getRequest()->getParam('get_product');
                $get_quantity=$this->getRequest()->getParam('get_quantity');
                $priority=$this->getRequest()->getParam('priority');
                $status=$this->getRequest()->getParam('status');
                $fromdate_offer=$this->getRequest()->getParam('fromdate_offer');
                $todate_offer=$this->getRequest()->getParam('todate_offer');


                // $logger->info($customer_group_str);
                // $logger->info($post_id);
                // $logger->info($name);
                // $logger->info($store_id);
                // $logger->info($fixed_amount);
                // $logger->info($get_product);
                // $logger->info($get_quantity);
                // $logger->info($status);
                // $logger->info($fromdate_offer);
                // $logger->info($todate_offer);
                
            if($post_id>0){
                    $mandeetotcol = $this->_savedata->create();

                    $mandeetotcol->setPostId($post_id);
                   
                  
                    $mandeetotcol->setFixedAmount($fixed_amount);
                    $mandeetotcol->setGetProduct($get_product);
                    $mandeetotcol->setGetQuantity($get_quantity);
                    $mandeetotcol->setCondition($condition);
                    $mandeetotcol->setPriority($priority);
                    $mandeetotcol->setName($name);
                    $mandeetotcol->setStoreId($store_id);
                    $mandeetotcol->setOfferFrom($fromdate_offer);
                    $mandeetotcol->setOfferTo($todate_offer);
                    $mandeetotcol->setCustomerGroup($customer_group_str);
                    $mandeetotcol->setStatus($status);
                       
                    if($mandeetotcol->save()){
                        $this->messageManager->addSuccess(__('You saved the rule.'));
                    }else{
                        $this->messageManager->addSuccess(__('Rule Not saved.'));
                    }
                    return $this->_redirect('retailinsights_pricerules/postbwgy/index/');
                    }
                else{
                  
                    $mandeetotcol = $this->_savedata->create();
                  
                    $mandeetotcol->setFixedAmount($fixed_amount);
                    $mandeetotcol->setGetProduct($get_product);
                    $mandeetotcol->setGetQuantity($get_quantity);
                    $mandeetotcol->setCondition($condition);
                    $mandeetotcol->setPriority($priority);
                    $mandeetotcol->setName($name);
                    $mandeetotcol->setStoreId($store_id);
                    $mandeetotcol->setOfferFrom($fromdate_offer);
                    $mandeetotcol->setOfferTo($todate_offer);
                    $mandeetotcol->setCustomerGroup($customer_group_str);
                    $mandeetotcol->setStatus($status);
                    if($mandeetotcol->save()){
                        $this->messageManager->addSuccess(__('You saved the rule.'));
                    }else{
                        $this->messageManager->addSuccess(__('Rule Not saved.'));
                    }
                    return $this->_redirect('retailinsights_pricerules/postbwgy/index/');
        
                }
	
        }

	}
}

?>
