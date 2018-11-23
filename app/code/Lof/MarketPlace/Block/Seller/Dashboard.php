<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
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
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Block\Seller;

class Dashboard extends \Magento\Framework\View\Element\Html\Link {


	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry = null;
    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerFactory;
    /**
     * @var \Lof\MarketPlace\Model\Data
     */
    protected $_helper;
     /**
     * @var \Lof\MarketPlace\Model\Amount
     */
    protected $amount;
      /**
     * @var \Lof\MarketPlace\Model\Amounttransaction
     */
    protected $amounttransaction;
    /**
     * @var \Lof\MarketPlace\Model\Orderitems
     */
    protected $orderitems;
    /**
     * @var \Lof\MarketPlace\Model\Order
     */
    protected $order;
    /**
     * @var \Magento\Sale\Model\Order
     */
    protected $_order;
    /**
     * @var \Lof\MarketPlace\Model\Product
     */
    protected $product;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    protected $date;

    protected $_localList;

    protected $_columnDate = 'main_table.created_at';

    protected $_resourceFactory;
    protected $_collectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lof\MarketPlace\Model\Seller
     * @param \Magento\Framework\App\ResourceConnection
     * @param array
    */
	public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
    	\Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Amount $amount,
        \Lof\MarketPlace\Model\Amounttransaction $amounttransaction,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        \Lof\MarketPlace\Model\Order $order,
        \Magento\Sales\Model\Order $_order,
        \Lof\MarketPlace\Model\SellerProduct $product,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Session $customerSession, 
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Locale\ListsInterface $localeLists,
       \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory,
       \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        array $data = []
        ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_resourceFactory = $resourceFactory;
        $this->_order = $this->_order;
        $this->amounttransaction = $amounttransaction;
        $this->product        = $product;
        $this->orderitems     = $orderitems;
        $this->order          = $order;
        $this->session        = $customerSession;
        $this->amount         = $amount;  
        $this->_helper        = $helper;
        $this->_coreRegistry  = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource      = $resource;
        $this->date = $date;
        $this->_localList = $localeLists;
        parent::__construct($context);
    }

    public function getResourceCollectionName()
    {
        return 'Lof\MarketPlace\Model\ResourceModel\Sales\Collection';
    }
    public function getCountry($country_code) {
        $country_name = $this->_localList->getCountryTranslation($country_code);
        $cell_value = ($country_name?$country_name:$country_code);
        return $cell_value;
    }

    public function getDataCountry() {
        $data = [];
        $data['country'] = $data['amount'] = 0;
        $country = $this->getTopCountries();
      
        foreach ($country as $key => $_country) {
            $data['country'] = $data['country'] + 1 ;
            $data['amount'] = $data['amount'] + $_country['seller_amount'];
        }
        return $data;
    }
    /**
     *  get Seller Colection
     *
     * @return Object
     */
     public function getSellerCollection(){
        $store = $this->_storeManager->getStore();
        $sellerCollection = $this->_sellerFactory->getCollection();
        return $sellerCollection;
    }
    /**
     *  get Seller Id
     *
     * @return Seller Id
     */
     public function getSellerId(){
        $seller_id = '';
        $seller = $this->_sellerFactory->getCollection()->addFieldToFilter('customer_id',$this->session->getId())->getData();
         foreach ($seller as $key => $_seller) {
              $seller_id = $_seller['seller_id'];
          } 
        return $seller_id;
    }
	/**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('Dashboard'));
        return parent::_prepareLayout ();
    }
    /**
     *  get Credit Amount Id
     *
     * @return Credit Amount Id
     */
    public function getCreditAmount() {
        $credit = 0;
        $amount = $this->amount->getCollection()->addFieldToFilter('seller_id',$this->getSellerId());
        foreach ($amount as $key => $_amount) {
            $credit = $this->_helper->getPriceFomat($_amount->getAmount());
        }
        return $credit;
    }
    public function getPriceFomat($price) {
        return $this->_helper->getPriceFomat($price);
    }
    public function getTopCountries() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $resourceCollection = $objectManager->create($this->getResourceCollectionName())
            ->prepareByCountryCollection()
            ->setMainTableId("country_id");
        $resourceCollection->applyCustomFilter(); 
        return $resourceCollection->getData();
    }


    public function getSalesReport() {
        $data=[];
        $dates = [];
        // for each day in the month
        for($i = 1; $i <=  date('t'); $i++)
        {
           // add the date to the dates array
           $dates[] = date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        } 
        $data[] = array();
        foreach ($dates as $key => $date) {
            $credit = $i = 0;
            
            $orderitems = $this->orderitems->getCollection()->addFieldToFilter('seller_id',$this->getSellerId())->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($date, null)->addDateToFilter($date , null); 
            $orderitems->applyCustomFilter();      
            foreach ($orderitems as $k => $_orderitems) {
                $credit = $credit +  $_orderitems->getSellerCommission() - $_orderitems->getSellerCommissionRefund();
                $i = $i+  $_orderitems->getQtyInvoiced() - $_orderitems->getQtyRefunded();
            }
            $data[$key]['earn']=$credit;
            $data[$key]['sales']=$i;
            $data[$key]['period']=substr($date,5);
        }
        return json_encode($data);
    }
    /**
     *  get Credit Amount Id
     *
     * @return Credit Amount Id
     */

    public function getEarningsToDay() {
        
        $credit = 0;
        $amount = $this->amounttransaction->getCollection()->addFieldToFilter('seller_id',$this->getSellerId())->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($this->date->gmtDate(), null)->addDateToFilter($this->date->gmtDate() , null); 
         $amount->applyCustomFilter();      
        foreach ($amount as $key => $_amount) {
            $credit = $credit + $_amount->getAmount();
        }
        return $this->_helper->getPriceFomat($credit);
    }
    public function getEarningsToMonth() {
        $credit = 0;
        $date = $this->date->gmtDate();
        $first_day = date('Y-m-01', strtotime($date));
        $last_day = date('Y-m-t', strtotime($date));
        $amount = $this->amounttransaction->getCollection()->addFieldToFilter('seller_id',$this->getSellerId())->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($first_day, null)->addDateToFilter($last_day , null); 
        $amount->applyCustomFilter();    
        foreach ($amount as $key => $_amount) {
            $credit = $credit + $_amount->getAmount();
        }
        return $this->_helper->getPriceFomat($credit);
    }
    /**
     *  get Total Sales
     *
     * @return Credit Total Sales
     */
    public function getTotalSales() {
        $total = 0;
        $orderitems = $this->orderitems->getCollection()->addFieldToFilter('seller_id',$this->getSellerId())->addFieldToFilter('status','complete');
        foreach ($orderitems as $key => $_orderitems) {
            $total = $total + $_orderitems->getQtyInvoiced() - $_orderitems->getQtyRefunded();
        }
        return $total;
    }
    public function getOrder($orderid) {
       
        //$order = $this->_order->getCollection()->addFieldToFilter('entity_id',$this->getOrderSeller()->getOrderId())->setOrder('entity_id', 'desc');
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->load($orderid,'entity_id');
        return $orderDatamodel;
    }
     /**
     *  get Total Sales
     *
     * @return Credit Total Sales
     */
    public function getTotalSalesDay() {
        $total = 0;
        $orderitems = $this->orderitems->getCollection()->addFieldToFilter('seller_id',$this->getSellerId())->addFieldToFilter('status','complete')->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($this->date->gmtDate(), null)->addDateToFilter($this->date->gmtDate() , null); 
        $orderitems->applyCustomFilter();

        foreach ($orderitems as $key => $_orderitems) {
            $total = $total + $_orderitems->getQtyInvoiced() - $_orderitems->getQtyRefunded();
        }
        return $total;
    }
    /**
     *  get Total Sales
     *
     * @return Credit Total Sales
     */
    public function getTotalSalesMonth() {
        $total = 0;
        $date = $this->date->gmtDate();
        $first_day = date('Y-m-01', strtotime($date));
        $last_day = date('Y-m-t', strtotime($date));
        $orderitems = $this->orderitems->getCollection()->addFieldToFilter('seller_id',$this->getSellerId())->addFieldToFilter('status','complete')->setDateColumnFilter($this->_columnDate)->addDateFromFilter($first_day, null)->addDateToFilter($last_day  , null); 
        $orderitems->applyCustomFilter();
        foreach ($orderitems as $key => $_orderitems) {
            $total = $total + $_orderitems->getQtyInvoiced() - $_orderitems->getQtyRefunded();
        }
        return $total;
    }
    public function getOrderSeller() {
        $order = $this->order->getCollection()->addFieldToFilter('seller_id',$this->getSellerId())->setOrder('id', 'desc');
        return $order;
    }

    public function getBestSeller() {
       
        $collection = $this->_collectionFactory->create();
        $connection = $collection->getConnection();
        
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToFilter('seller_id',$this->getSellerId());
        $resource = $collection->getResource();
        $collection->joinTable(
            ['order_items' => $resource->getTable('sales_order_item')],
            'product_id = entity_id',
            ['qty_ordered' => 'SUM(order_items.qty_ordered)'],
            null,
            'left'
        );
        
        $orderJoinCondition = [
            'order.entity_id = order_items.order_id',
            $connection->quoteInto("order.state <> ?", \Magento\Sales\Model\Order::STATE_CANCELED),
        ];
        
        $collection->getSelect()
            ->joinInner(
                ['order' => $resource->getTable('sales_order')],
                implode(' AND ', $orderJoinCondition),
                []
            )->where(
                'parent_item_id IS NULL'
            )->group(
                'order_items.product_id'
            )->order(
                'qty_ordered DESC'
            );
        return $collection;    
    }

    public function getMostView() {
        $collection = $this->_collectionFactory->create();
        $connection = $collection->getConnection();
        
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToFilter('seller_id',$this->getSellerId());
        $resource = $collection->getResource();
        $collection->joinTable(
            ['report_table_views' => $resource->getTable('report_event')],
            'object_id = entity_id',
            ['views' => 'COUNT(report_table_views.event_id)'],
            null,
            'right'
        );
        
        $collection->getSelect()->group(
                'e.entity_id'
            )->order(
                'views DESC'
            );
        return $collection;
    }
    /**
     *  get Total Order 
     *
     * @return Credit Total Order
     */
    public function getTotalOrder() {
        $total = 0;
        $order = $this->order->getCollection()->addFieldToFilter('seller_id',$this->getSellerId())->addFieldToFilter('status','complete');
        foreach ($order as $key => $_order) {
            $total = $total + 1;
        }
        return $total;
    }
    /**
     *  get Total Product 
     *
     * @return Credit Total Product
     */
    public function getTotalProduct() {
        $total = 0;
        $product = $this->product->getCollection()->addFieldToFilter('seller_id',$this->getSellerId());
        foreach ($product as $key => $_product) {
            $total = $total + 1;
        }
        return $total;
    }
}