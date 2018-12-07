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

class TopLink extends \Magento\Framework\View\Element\Html\Link {

    protected $_template = 'Lof_MarketPlace::account/top_link.phtml';
    
	/**
     * @var \Lof\MarketPlace\Helper\Data
     */
	protected $helper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context
	 * @param \Lof\MarketPlace\Helper\Data
	 * @param array
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Lof\MarketPlace\Helper\Data $helper,
		array $data = []
		) {
		$this->helper = $helper;
		parent::__construct($context, $data);
	}

	public function getHref() {
		if($this->helper->getConfig('general_settings/enable') == 1) {
			$url ='';

			if($this->helper->isLoggedIn()) {
				$url = $this->getUrl ( 'marketplace/catalog/dashboard' );
			}else{
				$url = $this->getUrl ( 'lofmarketplace/seller/login' );
			}
			return $url;
		}
	}
	/**
     * Render block HTML
     *
     * @return string
     */
	protected function _toHtml()
	{	
		
		if(!$this->helper->getConfig('general_settings/enable')) return;
		return '<li><a href="' . $this->getHref() . '"> ' . $this->getLabel() . ' </a></li>';
	}

	 /**
     * Function to Get Label on Top Link
     *
     * @return string
     */
    public function getLabel() {
        return __ ( 'Sell On AHA ' );
    }
    
}