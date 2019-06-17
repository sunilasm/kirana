<?php

namespace Retailinsights\Pricerules\Controller\Adminhtml\PostXYZ;

class Save extends \Magento\Backend\App\Action
{
	 protected $jsonFactory;
     protected $_postCollection;

     public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Retailinsights\Pricerules\Model\PostXYZFactory $postFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Retailinsights\Pricerules\Model\ResourceModel\PostXYZ\CollectionFactory $postCollection
    )
    {
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
                    
                    if($mandeetotcol->save()){
                        $this->messageManager->addSuccess(__('You saved the rule.'));
                    }else{
                        $this->messageManager->addSuccess(__('Rule Not saved.'));
                    }

                    return $this->_redirect('retailinsights_pricerules/postxyz/index/');
                    }
               else{
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
                   
                    if($mandeetotcol->save()){
                        $this->messageManager->addSuccess(__('You saved the rule.'));
                    }else{
                        $this->messageManager->addSuccess(__('Rule Not saved.'));
                    }

                    return $this->_redirect('retailinsights_pricerules/postxyz/index/');
                    }

            }
        }     
}

?>
