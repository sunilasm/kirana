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


class Item extends \Magento\Backend\Model\Menu\Item
{
    protected $_icon_class;
    
    /**
     * @var \Lof\MarketPlace\Model\UrlInterface
     */
    protected $urlInterface;
    
    /**
     * System event manager
     *
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    
    /**
     * 
     * @param \Magento\Backend\Model\Menu\Item\Validator $validator
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\Model\MenuFactory $menuFactory
     * @param \Magento\Backend\Model\UrlInterface $urlModel
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Lof\MarketPlace\Model\UrlInterface $vendorUrlModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Menu\Item\Validator $validator,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\MenuFactory $menuFactory,
        \Magento\Backend\Model\UrlInterface $urlModel,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Manager $moduleManager,
        \Lof\MarketPlace\Model\UrlInterface $urlInterface,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $data = []
    ) {
        parent::__construct(
            $validator,
            $authorization,
            $scopeConfig,
            $menuFactory,
            $urlModel,
            $moduleList,
            $moduleManager,
            $data
        );
        $this->_eventManager = $eventManager;
        $this->urlInterface = $urlInterface;
        $this->_icon_class = $this->_getArgument($data, 'icon');
    }
    
    /**
     * Retrieve icon class
     *
     * @return string
     */
    public function getIconClass()
    {
        return $this->_icon_class;
    }
    
    /**
     * Check whether item is allowed to the user
     *
     * @return bool
     */
    public function isAllowed()
    {
        $result = new \Magento\FrameWork\DataObject(['is_allowed' => true]);
        return $result->getIsAllowed();
    }
    
    /**
     * Check whether item is disabled. Disabled items are not shown to user
     *
     * @return bool
     */
    public function isDisabled()
    {
        return false;
    }
    
    /**
     * Retrieve menu item url
     *
     * @return string
     */
    public function getUrl()
    {
        if ((bool)$this->_action) {
            return $this->urlInterface->getUrl(
                (string)$this->_action, ['_cache_secret_key' => true]
            );
        }
        return '#';
    }
    
    public function __wakeup()
    {
        parent::__wakeup();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->urlInterface = $objectManager->get('Lof\MarketPlace\Model\UrlInterface');
        $this->_eventManager = $objectManager->get('Magento\Framework\Event\ManagerInterface');
    }
    
    public function __sleep(){
        $result = parent::__sleep();
        $result[] = '_icon_class';
        return $result;
    }
    
}
