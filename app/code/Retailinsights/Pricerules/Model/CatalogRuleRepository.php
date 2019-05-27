<?php
namespace Retailinsights\Pricerules\Model;

use Retailinsights\Pricerules\Api\CatalogRuleRepositoryInterface;
use Magento\Framework\View\Element\Template;

class CatalogRuleRepository implements CatalogRuleRepositoryInterface
{
    protected $catalogRuleId;
    protected $catalogRule;
    protected $rule;
    protected $postFactory;
    protected $postFixedFactory;
    protected $postWorthFactory;
    protected $postthreeFactory; 
    private $PostXYZFactory;

    public function __construct(
        \Retailinsights\Pricerules\Model\ResourceModel\Post\CollectionFactory $catalogRuleId,
        \Magento\CatalogRule\Model\Rule $catalogRule,
        \Magento\CatalogRule\Model\RuleFactory $rule,
        \Retailinsights\Pricerules\Model\PostFactory $postFactory,
        \Retailinsights\Pricerules\Model\PostFixedFactory $postFixedFactory,
        \Retailinsights\Pricerules\Model\PostWorthFactory $postWorthFactory,
        \Retailinsights\Pricerules\Model\PostthreeFactory $postthreeFactory,
        \Retailinsights\Pricerules\Model\PostXYZFactory $PostXYZFactory       
    )
    { 
        $this->catalogRuleId = $catalogRuleId;
        $this->catalogRule = $catalogRule;
        $this->rule = $rule;
        $this->postFactory = $postFactory->create();
        $this->postFixedFactory = $postFixedFactory->create();
        $this->postWorthFactory = $postWorthFactory->create();
        $this->postthreeFactory = $postthreeFactory->create();
        $this->PostXYZFactory = $PostXYZFactory->create();
    }
    public function getRuleList()
    {      
        $collection= $this->catalogRuleId->create();
        $catalog_data = $collection->getData();
        return $catalog_data;
    }    

    public function getRule($ruleId) {

        /* test */
         $result=array();

        $postModelData = $this->postFactory->load($ruleId);

        $modeldata['post_id']=$postModelData->getPostId();
        $modeldata['buy_product']=$postModelData->getBuyProduct();
         $modeldata['buy_quantity']=$postModelData->getBuyQuantity();

         $modeldata['get_product']=$postModelData->getGetProduct();
         $modeldata['get_quantity']=$postModelData->getGetQuantity();
         $modeldata['name']=$postModelData->getName();
         $modeldata['store_id']=$postModelData->getStoreId();

         $modeldata['priority']=$postModelData->getPriority();

        $modeldata['fixed_price']=$postModelData->getFixedPrice();
        $modeldata['offer_from']=$postModelData->getOfferFrom();
        $modeldata['offer_to']=$postModelData->getOfferTo();
        $modeldata['customer_group']=$postModelData->getCustomerGroup();
        $modeldata['status']=$postModelData->getStatus();
        $modeldata['featured_image']=$postModelData->getFeaturedImage();
        $modeldata['created_at']=$postModelData->getCreatedAt();
        $modeldata['updated_at']=$postModelData->getUpdatedAt();
        array_push($result, $modeldata);
        return $result;        
    }   
    
    public function getBuyXgetFixed($ruleId) {
        $result=array();
        $postFixedData = $this->postFixedFactory->load($ruleId);

        $fixeddata['post_id']=$postFixedData->getPostId();
        $fixeddata['buy_product']=$postFixedData->getBuyProduct();
         $fixeddata['quantity']=$postFixedData->getQuantity();

        $fixeddata['fixed_price']=$postFixedData->getFixedPrice();
         $fixeddata['name']=$postFixedData->getName();
         $fixeddata['store_id']=$postFixedData->getStoreId();

         $fixeddata['priority']=$postFixedData->getPriority();

        $fixeddata['offer_from']=$postFixedData->getOfferFrom();
        $fixeddata['offer_to']=$postFixedData->getOfferTo();
        $fixeddata['customer_group']=$postFixedData->getCustomerGroup();
        $fixeddata['status']=$postFixedData->getStatus();
        $fixeddata['featured_image']=$postFixedData->getFeaturedImage();
        $fixeddata['created_at']=$postFixedData->getCreatedAt();
        $fixeddata['updated_at']=$postFixedData->getUpdatedAt();
        array_push($result, $fixeddata);
        return $result;
    }    
    
    public function getBuyXXXgetY($ruleId) {
        $result=array();

        $postWorthData = $this->postWorthFactory->load($ruleId);

        $worthdata['post_id']=$postWorthData->getPostId();
        $worthdata['subtotal']=$postWorthData->getSubtotal();
        $worthdata['get_product']=$postWorthData->getGetProduct();
        $worthdata['quantity']=$postWorthData->getQuantity();

        $worthdata['name']=$postWorthData->getName();
        $worthdata['store_id']=$postWorthData->getStoreId();

        $worthdata['priority']=$postWorthData->getName();

        $worthdata['offer_from']=$postWorthData->getOfferFrom();
        $worthdata['offer_to']=$postWorthData->getOfferTo();
        $worthdata['customer_group']=$postWorthData->getCustomerGroup();
        $worthdata['status']=$postWorthData->getStatus();
        $worthdata['featured_image']=$postWorthData->getFeaturedImage();
        $worthdata['created_at']=$postWorthData->getCreatedAt();
        $worthdata['updated_at']=$postWorthData->getUpdatedAt();
        array_push($result, $worthdata);
        return $result;    
    } 

    public function getBuythree($ruleId) {
        $result=array();
        $postthreeFixedData = $this->postthreeFactory->load($ruleId);

        $fixedthreedata['post_id']=$postthreeFixedData->getPostId();
        $fixedthreedata['buy_product_one']=$postthreeFixedData->getBuyProductOne();
        $fixedthreedata['buy_product_two']=$postthreeFixedData->getBuyProductTwo();
         
        $fixedthreedata['fixed_price']=$postthreeFixedData->getFixedPrice();
         $fixedthreedata['name']=$postthreeFixedData->getName();
         $fixedthreedata['store_id']=$postthreeFixedData->getStoreId();

         $fixedthreedata['priority']=$postthreeFixedData->getPriority();

        $fixedthreedata['offer_from']=$postthreeFixedData->getOfferFrom();
        $fixedthreedata['offer_to']=$postthreeFixedData->getOfferTo();
        $fixedthreedata['customer_group']=$postthreeFixedData->getCustomerGroup();
        $fixedthreedata['status']=$postthreeFixedData->getStatus();
        $fixedthreedata['featured_image']=$postthreeFixedData->getFeaturedImage();
        $fixedthreedata['created_at']=$postthreeFixedData->getCreatedAt();
        $fixedthreedata['updated_at']=$postthreeFixedData->getUpdatedAt();
        array_push($result, $fixedthreedata);
        return $result;
    }    
        public function getBuyXYZ($ruleId) {
        $result=array();
        $postXYZData = $this->PostXYZFactory->load($ruleId);
        $xyzdata['post_id']=$postXYZData->getPostId();
    //JSON within JSON
        $data = json_decode($postXYZData->getRuleCondition());
        $xyzdata['rule_condition']= $data;
        $xyzdata['name']=$postXYZData->getName();
        $xyzdata['store_id']=$postXYZData->getStoreId();
        $xyzdata['discount_product']=$postXYZData->getDiscountProduct();
        $xyzdata['discount']=$postXYZData->getDiscount();
        $xyzdata['priority']=$postXYZData->getPriority();
        $xyzdata['offer_from']=$postXYZData->getOfferFrom();
        $xyzdata['offer_to']=$postXYZData->getOfferTo();
        $xyzdata['customer_group']=$postXYZData->getCustomerGroup();
        $xyzdata['status']=$postXYZData->getStatus();
    
        array_push($result, $xyzdata);
     
    	return $result;    
    }    
           
    public function deleteById($ruleId){
        return $this->delete($this->getById($ruleId));
    }
}
