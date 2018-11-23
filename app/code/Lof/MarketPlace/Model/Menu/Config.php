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

namespace Lof\MarketPlace\Model\Menu;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @api
 * @since 100.0.2
 */
class Config extends \Magento\Backend\Model\Menu\Config
{
    const CACHE_ID = 'seller_menu_config';
    const CACHE_MENU_OBJECT = 'seller_menu_object';

    /**
     * Initialize menu object
     *
     * @return void
     */
    protected function _initMenu()
    {
        if (!$this->_menu) {
            $this->_menu = $this->_menuFactory->create();

            $cache = $this->_configCacheType->load(self::CACHE_MENU_OBJECT);
            if ($cache) {
                $this->_menu->unserialize($cache);
                return;
            }
    
            $this->_director->direct(
                $this->_configReader->read($this->_appState->getAreaCode()),
                $this->_menuBuilder,
                $this->_logger
            );
            $this->_menu = $this->_menuBuilder->getResult($this->_menu);
    
            $this->_configCacheType->save($this->_menu->serialize(), self::CACHE_MENU_OBJECT);
        }
    }
}
