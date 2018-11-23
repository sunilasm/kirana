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
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Block;

class GroupList extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_sellerHelper;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_group;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Magento\Framework\Registry                      $registry     
     * @param \Lof\MarketPlace\Helper\Data                           $sellerHelper  
     * @param \Lof\MarketPlace\Model\Group                           $group        
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager 
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Group $group,
        array $data = []
        ) {
        $this->_group = $group;
        $this->_coreRegistry = $registry;
        $this->_sellerHelper = $sellerHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if(!$this->getConfig('general_settings/enable')) return;
        parent::_construct();
    }

    public function getGroupList(){
        $collection = $this->_group->getCollection()
        ->addFieldToFilter('status',1)
        ->addFieldToFilter('shown_in_sidebar',1)
        ->setOrder('position','ASC');
        return $collection;
    }
}