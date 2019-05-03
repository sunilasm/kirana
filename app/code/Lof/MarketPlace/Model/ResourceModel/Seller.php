<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Model\ResourceModel;

class Seller extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	 * Store model
	 *
	 * @var \Magento\Store\Model\Store
	 */
	protected $_store = null;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $_date;

	/**
	 * Store manager
	 */
	protected $_storeManager;

	/**
	 * @var \Magento\Framework\Stdlib\Datetime
	 */
	protected $dateTime;

	/**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string $connectionName
     */
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Stdlib\DateTime $dateTime,
		$connectionName = null
		) {
		parent::__construct($context, $connectionName);
		$this->_date = $date;
		$this->_storeManager = $storeManager;
		$this->dateTime = $dateTime;
	}

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(){
    	$this->_init('lof_marketplace_seller','seller_id');
    }

    /**
     *  Check whether seller url key is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericSellerUrlKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Cms\Model\Page $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int)$object->getStoreId()];
            $select->join(
                ['lof_marketplace_store' => $this->getTable('lof_marketplace_store')],
                $this->getMainTable() . '.seller_id = lof_marketplace_store.seller_id',
                []
                )->where(
                'status = ?',
                1
                )->where(
                'lof_marketplace_store.store_id IN (?)',
                $storeIds
                )->order(
                'lof_marketplace_store.store_id DESC'
                )->limit(
                1
                );
            }

            return $select;
        }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
            )->join(
            ['cps' => $this->getTable('lof_marketplace_store')],
            'cp.seller_id = cps.seller_id',
            []
            )->where(
            'cp.identifier = ?',
            $identifier
            )->where(
            'cps.store_id IN (?)',
            $store
            );

            if (!is_null($isActive)) {
                $select->where('cp.status = ?', $isActive);
            }

            return $select;
        }

    /**
     * Check if seller url key exist for specific store
     * return seller id if seller exists
     *
     * @param string $url_key
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($url_key, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($url_key, $stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)->columns('cp.seller_id')->order('cps.store_id DESC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Process seller data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['seller_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('lof_marketplace_store'), $condition);

        $condition = ['seller_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('lof_marketplace_product'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process seller data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        $result = $this->checkUrlExits($object); 

        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }

        $object->setUpdateTime($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Assign seller to store views
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('lof_marketplace_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['seller_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['seller_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }


        // Products Related
        if(null !== ($object->getData('products'))  && isset($object->getData()['in_products'])){
            $table = $this->getTable('lof_marketplace_product');
            $where = ['seller_id = ?' => (int)$object->getId()];
            $product_table = $this->getTable('catalog_product_entity');
            $product_data = [
                'approval' => 0,
                'seller_id' =>0
            ];
            $this->getConnection()->update($product_table,$product_data,$where);
            $this->getConnection()->delete($table, $where);
           
            if($quetionProducts = $object->getData('products')){
                $where = ['seller_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                $data = [];
                foreach ($quetionProducts as $k => $_post) {

                    // Get Doorstep price and Pickup from store.
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $products = $objectManager->create('\Magento\Catalog\Model\Product')->getCollection();
                    $products->addAttributeToSelect(array('name'))
                    ->addFieldToFilter('entity_id', ['in' => $k])
                    ->addFieldTofilter('type_id','simple')
                    ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                    ->addFieldToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->load();
                    
                    // Product deatil
                    foreach ($products as $product) {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $productData = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
                        if($productData->getPrice() <= 4 || $productData->getPrice() <= 4.00){
                            $doorsetp = $productData->getPrice();
                            $pickup = $productData->getPrice();
                        }elseif($product->getSku() == 'SKU590000592'){
                            $doorsetp = ($productData->getPrice()*0.8-4);
                            $pickup = $productData->getPrice();
                        }elseif($product->getSku() == 'SKU590000692'){
                            $doorsetp = $productData->getPrice();
                            $pickup = ($productData->getPrice()*0.9);
                        }else{
                            $doorsetp = ($productData->getPrice()*0.8-4);
                            $pickup = ($productData->getPrice()*0.9);
                        }
                        $mrp = $productData->getPrice();
                    }

                    $data[] = [
                    'seller_id' => (int)$object->getId(),
                    'product_id' => $k,
                    'position' => $_post['product_position'],
                    'doorstep_price' => $doorsetp,
                    'pickup_from_store' => $pickup,
                    'mrp' => $mrp
                    ];

                      $product_data2 = [
                        'seller_id' => (int)$object->getId(),
                        'approval' => 2
                    ];
                    $where_product =  ['entity_id = ?' => $k];
                    $this->getConnection()->update($product_table,$product_data2,$where_product);
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
        }
       
        return parent::_afterSave($object);
    }

    /**
     * Load an object using 'url_key' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'url_key';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
       
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }

        if ($id = $object->getId()) {
                $connection = $this->getConnection();
                $select = $connection->select()
                ->from($this->getTable('lof_marketplace_product'))
                ->where(
                    'seller_id = '.(int)$id
                    );
                $products = $connection->fetchAll($select);
                $object->setData('products', $products);
            } 

        return parent::_afterLoad($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $sellerId
     * @return array
     */
    public function lookupStoreIds($sellerId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lof_marketplace_store'),
            'store_id'
            )
        ->where(
            'seller_id = ?',
            (int)$sellerId
            );
        return $connection->fetchCol($select);
    }

    public function checkUrlExits(\Magento\Framework\Model\AbstractModel $object)
    {
        $stores = $object->getStores();
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('lof_marketplace_seller'),
            'seller_id'
            )
        ->where(
            'url_key = ?',
            $object->getUrlKey()
            )
        ->where(
            'seller_id != ?',
            $object->getId()
            );

        $sellerIds = $connection->fetchCol($select);
        if(count($sellerIds)>0 && is_array($stores)){
            if(in_array('0', $stores)){
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('URL key for specified store already exists.')
                    );  
            }
            $stores[] = '0';
            $select = $connection->select()->from(
                $this->getTable('lof_marketplace_store'),
                'seller_id'
                )
            ->where(
                'seller_id IN (?)',
                $sellerIds
                )
            ->where(
                'store_id IN (?)',
                $stores
                );
            $result = $connection->fetchCol($select);
            if(count($result)>0){
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('URL key for specified store already exists.')
                    );  
            }
        }
        return $this;
    }
}