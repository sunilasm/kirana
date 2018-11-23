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

class Menu extends \Magento\Backend\Block\Menu
{

    
    /**
     * @var \Lof\MarketPlace\Model\Menu\Config
     */
    protected $_menuConfig;


    protected $helper;

    protected $anchorRenderer;
    
    /**
     * @param Template\Context $context
     * @param \Magento\Backend\Model\UrlInterface $url
     * @param \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Backend\Model\Menu\Config $menuConfig
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Model\Menu\Config $menuConfig,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Lof\MarketPlace\Model\Menu\Config $config,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Backend\Block\AnchorRenderer $anchorRenderer,
        array $data = []
    ) {
        $this->_iteratorFactory = $iteratorFactory;
        $this->_authSession = $authSession;
        $this->_menuConfig = $config;
        $this->sellerFactory = $sellerFactory;
        $this->_localeResolver = $localeResolver;
        $this->helper = $helper;
        $this->anchorRenderer = $anchorRenderer;
        \Magento\Backend\Block\Template::__construct($context, $data);
    }


      /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = [
            'seller_top_nav',
            $this->getActive(),
           $this->helper->getSellerId(),
            $this->_localeResolver->getLocale(),
        ];
        // Add additional key parameters if needed
        $newCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
        if (is_array($newCacheKeyInfo) && !empty($newCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $newCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }
   
    public function isSeller() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();
            return $status;
        }
    }
    /**
     * Render menu item anchor title
     *
     * @param Item $menuItem
     * @return string
     */
    private function _renderItemAnchorTitle($menuItem)
    {
        return $menuItem->hasTooltip() ? 'title="' . __($menuItem->getTooltip()) . '"' : '';
    }
     /**
     * Render menu item onclick function
     *
     * @param Item $menuItem
     * @return string
     */
    private function _renderItemOnclickFunction($menuItem)
    {
        return $menuItem->hasClickCallback() ? ' onclick="' . $menuItem->getClickCallback() . '"' : '';
    }
    /**
     * Render menu item anchor
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @param int $level
     * @param boolean $hasChildren
     * @return string
     */
    protected function _renderAnchor($menuItem, $level,$hasChildren=false)
    {
        $output = '<a href="' . ($menuItem->getUrl()?$menuItem->getUrl():"#") . '" ' . $this->_renderItemAnchorTitle($menuItem) 
        . $this->_renderItemOnclickFunction($menuItem) 
        . ' class="' . $this->_renderAnchorCssClass($menuItem,$level) . '">' 
        . '<i class="'.($menuItem->getIconClass()?$menuItem->getIconClass():'').'"></i>'
        . '<span>' . $this->_getAnchorLabel($menuItem) . '</span>' 
        . ($hasChildren?'<span class="fa fa-chevron-down"></span>':'')
        . '</a>';

    
        return $output;

    }
     /**
     * Render menu item anchor css class
     *
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @param int $level
     * @return string
     */
    protected function _renderAnchorCssClass($menuItem, $level)
    {
        return $this->_isItemActive($menuItem, $level) ? '_active' : '';
    }
     /**
     * Check whether given item is currently selected
     *
     * @param \Magento\Backend\Model\Menu\Item $item
     * @param int $level
     * @return bool
     */
    protected function _isItemActive($item, $level)
    {
        $itemModel = $this->getActiveItemModel();
        $output = false;

        if ($level == 0 &&
            $itemModel instanceof \Magento\Backend\Model\Menu\Item &&
            ($itemModel->getId() == $item->getId() ||
            $item->getChildren()->get(
                $itemModel->getId()
            ) !== null)
        ) {
            $output = true;
        }
        return $output;
    }
    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @param int $level
     * @param int $limit
     * @param $id int
     * @return string HTML code
     */
    protected function _addSubMenu($menuItem, $level, $limit, $id = null)
    {
        $output = '';
        if (!$menuItem->hasChildren()) {
            return $output;
        }
        $colStops = null;

    
        $output .= $this->renderNavigation($menuItem->getChildren(), $level + 1, $limit, $colStops);
        return $output;
    }
     /**
     * Render item css class
     *
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @param int $level
     * @return string
     */
    protected function _renderItemCssClass($menuItem, $level)
    {
        $isLast = 0 == $level && (bool)$this->getMenuModel()->isLast($menuItem) ? 'last' : '';
        $output = ($this->_isItemActive(
            $menuItem,
            $level
        ) ? '_current _active' : '') .
            ' ' .
            ($menuItem->hasChildren() ? 'parent' : '') .
            ' ' .
            $isLast .
            ' ' .
            'level-' .
            $level;
        return $output;
    }
    /**
     * Render Navigation
     *
     * @param \Magento\Backend\Model\Menu $menu
     * @param int $level
     * @param int $limit
     * @param array $colBrakes
     * @return string HTML
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function renderNavigation($menu, $level = 0, $limit = 0, $colBrakes = [])
    {
        $itemPosition = 1;
        $outputStart = '<ul ' . (0 == $level ? ' role="menubar"' : 'role="menu"') . ' class="'.(0 == $level?"nav side-menu":"nav child_menu").'">';
        $output = '';
        
        /** @var $menuItem \Magento\Backend\Model\Menu\Item  */
        foreach ($this->_getMenuIterator($menu) as $menuItem) {
            $menuId = $menuItem->getId();
            $itemName = substr($menuId, strrpos($menuId, '::') + 2);
            $itemClass = str_replace('_', '-', strtolower($itemName));
    
            if (count($colBrakes) && $colBrakes[$itemPosition]['colbrake']) {
                $output .= '</ul></li><li class="column"><ul role="menu">';
            }
    
            $id = $this->getJsId($menuItem->getId());
            $subMenu = $this->_addSubMenu($menuItem, $level, $limit, $id);
            $anchor = $this->_renderAnchor($menuItem, $level,$subMenu);
            $output .= '<li ' . $this->getUiId($menuItem->getId())
            . ' class="'.($subMenu?"nav child_menu ":'').'item-' . $itemClass . ' ' . $this->_renderItemCssClass($menuItem, $level)
            . ($level == 0 ? '" id="' . $id . '" aria-haspopup="true' : '')
            . '" role="menu-item">' . $anchor . $subMenu . '</li>';
            $itemPosition++;
        }
    
        if (count($colBrakes) && $limit) {
            $output = '<li class="column"><ul role="menu">' . $output . '</ul></li>';
        }
    
        return $outputStart . $output . '</ul>';
    }

}
