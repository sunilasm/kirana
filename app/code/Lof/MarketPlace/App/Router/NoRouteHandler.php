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

namespace Lof\MarketPlace\App\Router;

class NoRouteHandler implements \Magento\Framework\App\Router\NoRouteHandlerInterface
{
    /**
     * @var \Lof\Vendors\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Route\ConfigInterface
     */
    protected $routeConfig;

    /**
     * @param \Lof\Vendors\Helper\Data $helper
     * @param \Magento\Framework\App\Route\ConfigInterface $routeConfig
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\Route\ConfigInterface $routeConfig
    ) {
        $this->helper = $helper;
        $this->routeConfig = $routeConfig;
    }

    /**
     * Check and process no route request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function process(\Magento\Framework\App\RequestInterface $request)
    {
        $requestPathParams = explode('/', trim($request->getPathInfo(), '/'));

        $areaFrontName = array_shift($requestPathParams);

        if ($areaFrontName === $this->helper->getAreaFrontName(true)) {
            $moduleName = $this->routeConfig->getRouteFrontName('account');
            $actionNamespace = 'noroute';
            $actionName = 'index';
            $request->setModuleName($moduleName)->setControllerName($actionNamespace)->setActionName($actionName);
            return true;
        }
        return false;
    }
}
