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

namespace Lof\MarketPlace\Model;

interface UrlInterface extends \Magento\Framework\UrlInterface
{
    /**
     * Secret key query param name
     */
    const SECRET_KEY_PARAM_NAME = 'key';

    /**
     * xpath to startup page in configuration
     */
    const XML_PATH_STARTUP_MENU_ITEM = 'marketplace/general/start_page';
	
	
	const XML_PATH_USE_CUSTOM_VENDOR_PATH = 'marketplace/url/use_custom_path';
	
	const XML_PATH_CUSTOM_VENDOR_PATH = 'marketplace/url/custom_path';

    /**
     * Generate secret key for controller and action based on form key
     *
     * @param string $routeName
     * @param string $controller Controller name
     * @param string $action Action name
     * @return string
     */
    public function getSecretKey($routeName = null, $controller = null, $action = null);

    /**
     * Return secret key settings flag
     *
     * @return bool
     */
    public function useSecretKey();

    /**
     * Enable secret key using
     *
     * @return \Magento\Backend\Model\UrlInterface
     */
    public function turnOnSecretKey();

    /**
     * Disable secret key using
     *
     * @return \Magento\Backend\Model\UrlInterface
     */
    public function turnOffSecretKey();

    /**
     * Refresh admin menu cache etc.
     *
     * @return \Magento\Backend\Model\UrlInterface
     */
    public function renewSecretUrls();

    /**
     * Find admin start page url
     *
     * @return string
     */
    //public function getStartupPageUrl();

    /**
     * Set custom auth session
     *
     * @param \Magento\Backend\Model\Auth\Session $session
     * @return \Lof\MarketPlace\Model\UrlInterface
     */
    public function setSession(\Magento\Backend\Model\Auth\Session $session);

    /**
     * Return backend area front name, defined in configuration
     *
     * @return string
     */
    public function getAreaFrontName();

    /**
     * Find first menu item that user is able to access
     *
     * @return string
     */
    //public function findFirstAvailableMenu();
}
