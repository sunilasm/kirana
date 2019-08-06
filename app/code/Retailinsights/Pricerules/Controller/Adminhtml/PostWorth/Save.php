<?php

namespace Retailinsights\Pricerules\Controller\Adminhtml\PostWorth;

class Save extends \Magento\Backend\App\Action
{
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
        $this->_postCollection = $postCollection;
    }

    public function execute()
    {
    if ($this->getRequest()->isAjax()) 
        {
            $flag = 0;
        
            //imploding the customer Group.            
            $customer_group=$this->getRequest()->getParam('customer_group');
            $customer_group_str = implode(",",$customer_group);
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
                        if($mandeetotcol->save()){
                            $this->messageManager->addSuccess(__('You saved the rule.'));
                        }else{
                            $this->messageManager->addSuccess(__('Rule Not saved.'));
                        }
                        return $this->_redirect('retailinsights_pricerules/postworth/index/');
                    }
                else{
  
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
                        if($mandeetotcol->save()){
                            $this->messageManager->addSuccess(__('You saved the rule.'));
                        }else{
                            $this->messageManager->addSuccess(__('Rule Not saved.'));
                        }
                        return $this->_redirect('retailinsights_pricerules/postworth/index/');
                    }      
            }
    }     
}

?>