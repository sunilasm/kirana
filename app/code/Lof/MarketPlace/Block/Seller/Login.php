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

class Login extends \Magento\Framework\View\Element\Template {
    /**
     *
     * @var int
     */
    private $_username = - 1;
    
    //protected $_template = 'Lof_MarketPlace::seller/login.phtml';
    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    /**
     *
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;
    
    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context            
     * @param \Magento\Customer\Model\Session $customerSession            
     * @param \Magento\Customer\Model\Url $customerUrl            
     * @param array $data            
     */
    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context, 
    	\Magento\Customer\Model\Session $customerSession, 
    	\Lof\MarketPlace\Helper\Url $customerUrl, 
    	array $data = []) {
        parent::__construct ( $context, $data );
        $this->_isScopePrivate = false;
        $this->_customerUrl = $customerUrl;
        $this->_customerSession = $customerSession;
    }
    
    /**
     *
     * @return $this
     */
    protected function _prepareLayout() {
        $this->pageConfig->getTitle ()->set ( __ ( 'Kirana Login' ) );
        return parent::_prepareLayout ();
    }
   
    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl() {
        return $this->_customerUrl->getLoginPostUrl ();
    }
    
    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl() {
        return $this->_customerUrl->getForgotPasswordUrl ();
    }
    
    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername() {
        if (- 1 === $this->_username) {
            $this->_username = $this->_customerSession->getUsername ( true );
        }
        return $this->_username;
    }
    
    /**
     * Check if autocomplete is disabled on storefront
     *
     * @return bool
     */
    public function isAutocompleteDisabled() {
        return ( bool ) ! $this->_scopeConfig->getValue ( \Magento\Customer\Model\Form::XML_PATH_ENABLE_AUTOCOMPLETE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
    }
}
