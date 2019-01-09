<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Helper;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as ObjectConverter;
use Magento\Customer\Api\CustomerRepositoryInterface;

Class DataRule extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
     /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
     /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $_collection;
       /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;
     /**
     * @var \Lof\MarketPlace\Model\commission
     */
    protected $commission;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Lof\MarketPlace\Model\EmailFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
    */


    public function __construct(
    \Magento\Framework\App\Helper\Context $context, 
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    CustomerRepositoryInterface $customerRepository,
    \Magento\Sales\Model\ResourceModel\Order\Collection $collection,
    \Lof\MarketPlace\Model\Condition\Sql\Builder $sqlBuilder,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
    \Magento\Quote\Model\QuoteFactory $quoteFactory,
    \Magento\Checkout\Model\Cart $cart,
    \Magento\Checkout\Model\Session $session,
    \Lof\MarketPlace\Model\ResourceModel\Commission\Collection $commission,
    \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->cart = $cart;
        $this->sqlBuilder   = $sqlBuilder;
        $this->storeManager = $storeManager;
        $this->customerSession              = $customerSession;
        $this->productCollectionFactory     = $productCollectionFactory;
        $this->quoteFactory = $quoteFactory;
        $this->_collection          = $collection;
        $this->customerRepository = $customerRepository;
        $this->_session             =  $session;
        $this->commission  = $commission;
    }

    public function getStore($storeId = '')
    {
        $store = $this->storeManager->getStore($storeId);
        return $store;
    }

     public function getCustomer()
    {   
        $customer = $this->customerSession->getCustomer();
        return $customer;
    }

    /**
     * Get rule by store && customer group id
     * @param  string $store
     * @param  string $customerGroupId
     * @return Lof\MarketPlace\Model\ResourceModel\Earning\Rule\Collection
     */
    public function getRules()
    {
        $websiteId     = $this->storeManager->getStore()->getId();
        $collection = $this->commission;
 
        return $collection;
    }

      /**
     * 
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
      $collection = $this->productCollectionFactory->create();
      
      $collection->addMinimalPrice()
      ->addFinalPrice()
      ->addTaxPercents()
      ->addUrlRewrite()
      ->addStoreFilter(); 
 
      return $collection;
    }

    public function getRuleProducts($sellerId,$entity_id)
    { 
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $seller = $objectManager->create('Lof\MarketPlace\Model\Seller');
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->getProductCollection();
        $seller = $seller->load($sellerId);
        $today = (new \DateTime())->format('Y-m-d');
     
        $rules = $this->getRules()->addFieldToFilter('is_active',1)->setOrder('priority', 'ASC');
      
         $rules->getSelect()
            ->where(
                '(from_date IS NULL OR from_date<=?) AND (to_date IS NULL OR to_date>=?)',
                $today,
                $today
            );  

        $commissionRules = $rules;
        foreach($commissionRules as $commissionRule) {
            if(is_array($commissionRule->getGroupId()) && in_array($seller->getGroupId(), $commissionRule->getGroupId())) {
                foreach ($rules as $key => $_rules) {
                    if((in_array($storeId, $_rules->getData('store_id')) || in_array(0, $_rules->getData('store_id'))) && count($_rules) >0 && in_array($seller->getGroupId(), $_rules->getGroupId()) ){
                        if($_rules->getData('stop_rules_processing')) {
                            return $_rules;
                        }
                    }
                } 
                $collection = $this->getProductCollection();
                if((in_array($storeId, $commissionRule->getData('store_id')) || in_array(0, $commissionRule->getData('store_id'))) && count($commissionRule) >0 ){
                  
                    $collection->getSelect()->reset(\Magento\Framework\DB\Select::WHERE);         
                    $conditions = $commissionRule->getActions();            
                    $conditions->collectValidatedAttributes($collection);   
                    $collection->getSelect()->where('e.entity_id IN (?) ',$entity_id)->where($this->sqlBuilder->attachConditionToCollection($collection, $conditions));
                    if(count($collection->getData())>0) {
                        return $commissionRule;
                    } 
               } 
           }
        } 
    }
}
?>