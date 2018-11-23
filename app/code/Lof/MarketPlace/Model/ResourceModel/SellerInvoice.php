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
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Model\ResourceModel;
 
class SellerInvoice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store model
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $_store = null;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $connectionName = null
    ) {
        $this->_date = $date;

        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_marketplace_sellerinvoice', 'entity_id');
    }

    /**
     * Load an object using 'identifier' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
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
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt($this->_date->gmtDate());
        }

        $object->setUpdatedAt($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }
    /**
     * Set store model
     *
     * @param \Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }
}
