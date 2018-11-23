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

namespace Lof\MarketPlace\Block\Seller\Rating;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
class View extends \Magento\Framework\View\Element\Template
{
	
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\Seller
    */
    protected $seller;
    /**
     * @var \Lof\MarketPlace\Model\Rating
    */
    protected $rating; 
 
    protected $request;
     

    public $helper;
 
	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession, 
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Model\Rating $rating,
        \Lof\MarketPlace\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);

    	$this->rating = $rating;
    	$this->helper = $helper;
    	$this->request =  $context->getRequest();
        $this->seller = $seller;
        $this->session           = $customerSession;
     
    }

    public function getRating() {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
    	$rating = $objectManager->get('Lof\MarketPlace\Model\Rating')->load($this->getRatingId());
    	return $rating;
    }

    public function getRatingId() {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return end($params);
    }
     /**
     *  get Seller Id
     *
     * @return Seller Id
     */
     public function getSeller(){
        $seller = $this->seller->getCollection()->addFieldToFilter('customer_id',$this->session->getId())->getFirstItem();
        
        return $seller;
    }


    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set('Detail Rating');
        return parent::_prepareLayout ();
    }
}