<?php

namespace Retailinsights\Pricerules\Controller\Adminhtml\Post;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Backend\App\Action
{
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
        $this->_postCollection = $postCollection; 
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
  if ($this->getRequest()->isAjax()) 
        {
             $flag = 0;
            $postCollectionObject = $this->_postCollection->create();
            $collectionData = $postCollectionObject->addFieldToSelect('buy_product')->load('*');
            $buy_product_explode=$this->getRequest()->getParam('buy_product');  
           
            $arr_buy_product = explode(",",$buy_product_explode);

            //sorting the array now
            sort($arr_buy_product);
            $buy_product_implode = implode(",",$arr_buy_product);
            //imploding the customer Group.
            
            $customer_group=$this->getRequest()->getParam('customer_group');
            
            $customer_group_str = implode(",",$customer_group);
       
                $post_id=$this->getRequest()->getParam('post_id');
                $name=$this->getRequest()->getParam('name');
                $store_id=$this->getRequest()->getParam('store_id');
                $buy_quantity=$this->getRequest()->getParam('buy_quantity');
                $get_product=$this->getRequest()->getParam('get_product');
                $get_quantity=$this->getRequest()->getParam('get_quantity');
                $priority=$this->getRequest()->getParam('priority');
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
                       
                    if($mandeetotcol->save()){
                        $this->messageManager->addSuccess(__('You saved the rule.'));
                    }else{
                        $this->messageManager->addSuccess(__('Rule Not saved.'));
                    }
                    return $this->_redirect('retailinsights_pricerules/post/index/');
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
                    if($mandeetotcol->save()){
                        $this->messageManager->addSuccess(__('You saved the rule.'));
                    }else{
                        $this->messageManager->addSuccess(__('Rule Not saved.'));
                    }
                    return $this->_redirect('retailinsights_pricerules/post/index/');
        
                }
	
        }

	}
}

?>
