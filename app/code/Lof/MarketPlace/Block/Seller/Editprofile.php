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

class Editprofile extends \Magento\Framework\View\Element\Template {

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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;
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
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
        ) {
		$this->_helper        = $helper;
		$this->_coreRegistry  = $registry;
		$this->_sellerFactory = $sellerFactory;
		$this->_resource      = $resource;
        parent::__construct($context);
    }
    /**
     *  get Seller Colection
     *
     * @return Object
     */
     public function getSellerCollection(){
        $store            = $this->_storeManager->getStore();
        $sellerCollection = $this->_sellerFactory->getCollection();
        return $sellerCollection;
    }

	/**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('Edit Profile'));
        return parent::_prepareLayout ();
    }
}