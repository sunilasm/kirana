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

namespace Lof\MarketPlace\App;

class Router extends \Magento\Framework\App\Router\Base
{
    /**
     * @var \Magento\Framework\UrlInterface $url
     */
    protected $_url;

    /**
     * List of required request parameters
     * Order sensitive
     *
     * @var string[]
     */
    protected $_requiredParams = ['areaFrontName', 'moduleFrontName', 'actionPath', 'actionName'];

    /**
     * We need to have noroute action in this router
     * not to pass dispatching to next routers
     *
     * @var bool
     */
    protected $applyNoRoute = true;

    /**
     * @var string
     */
    protected $pathPrefix = 'marketplace';

    /**
     * Check whether redirect should be used for secure routes
     *
     * @return bool
     */
    protected function _shouldRedirectToSecure()
    {
        return false;
    }
    
    /**
     * Parse request URL params
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return array
     */
    protected function parseRequest(\Magento\Framework\App\RequestInterface $request)
    {

        $output = [];
        $path = trim($request->getPathInfo(), '/');
        $params = explode('/', $path ? $path : $this->pathConfig->getDefaultPath());

        foreach ($this->_requiredParams as $paramName) {
            $output[$paramName] = array_shift($params);
        }

        if(empty($output['moduleFrontName']) || empty($output['actionPath'])) {
            $output['moduleFrontName'] = 'catalog';
            $output['actionPath'] = 'dashboard';

        } 
        for ($i = 0, $l = sizeof($params); $i < $l; $i += 2) {
            $output['variables'][$params[$i]] = isset($params[$i + 1]) ? urldecode($params[$i + 1]) : '';
        }
        return $output;
    }
}
