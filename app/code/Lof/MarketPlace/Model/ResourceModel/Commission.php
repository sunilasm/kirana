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

class Commission extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
	 * @var \Lof\MarketPlace\Model\ResourceModel\Commission\Collection
	 */
	protected $collection;

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
    	$this->_init('lof_marketplace_commission','commission_id');
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
            ['lof_marketplace_commission_store' => $this->getTable('lof_marketplace_commission_store')],
            $this->getMainTable() . '.commission_id = lof_marketplace_commission_store.commission_id',
            []
            )->where(
            'status = ?',
            1
            )->where(
            'lof_marketplace_commission_store.store_id IN (?)',
            $storeIds
            )->order(
            'lof_marketplace_commission_store.store_id DESC'
            )->limit(
            1
            );
        }

        return $select;
    }
     /**
     * Process seller data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['commission_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('lof_marketplace_commission_store'), $condition);

      

        return parent::_beforeDelete($object);
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
        $oldGroups= $this->lookupGroupIds($object->getId());

        $newStores = (array)$object->getStores();
        $newGroups = (array)$object->getGroups();
   
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('lof_marketplace_commission_store');
        $table1 = $this->getTable('lof_marketplace_commission_group');

        $insert = array_diff($newStores, $oldStores);
        $insert1 = array_diff($newGroups, $oldGroups);

        $delete = array_diff($oldStores, $newStores);
        $delete1 = array_diff($oldGroups, $newGroups);

        if ($delete) {
            $where = ['commission_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['commission_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        if ($delete) {
            $where = ['commission_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }
         
        if ($insert1) {
            $data = [];
            foreach ($insert1 as $groupId) {
                $data[] = ['commission_id' => (int)$object->getId(), 'group_id' => (int)$groupId];
            }
 
            $this->getConnection()->insertMultiple($table1, $data);
        }

        if ($delete1) {
            $where = ['commission_id = ?' => (int)$object->getId(), 'group_id IN (?)' => $delete1];
            $this->getConnection()->delete($table1, $where);
        }
        

        return parent::_afterSave($object);
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
            $object->setData('stores', $stores);
            $groups = $this->lookupGroupIds($object->getId());
        
            $object->setData('group_id', $groups); 
            $object->setData('groups', $groups);
        }
        return parent::_afterLoad($object);
    }
     /**
     * Get store ids to which specified item is assigned
     *
     * @param int $sellerId
     * @return array
     */
    public function lookupGroupIds($commission_id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lof_marketplace_commission_group'),
            'group_id'
            )
        ->where(
            'commission_id = ?',
            (int)$commission_id
            );
        return $connection->fetchCol($select);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $sellerId
     * @return array
     */
    public function lookupStoreIds($commission_id)
    {
        
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lof_marketplace_commission_store'),
            'store_id'
            )
        ->where(
            'commission_id = ?',
            (int)$commission_id
            );
        return $connection->fetchCol($select);
    }


}