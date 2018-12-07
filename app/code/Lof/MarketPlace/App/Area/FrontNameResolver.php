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

namespace Lof\MarketPlace\App\Area;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class FrontNameResolver implements \Magento\Framework\App\Area\FrontNameResolverInterface
{
    const XML_PATH_USE_CUSTOM_VENDOR_PATH = 'marketplace/url/use_custom_path';

    const XML_PATH_CUSTOM_VENDOR_PATH = 'marketplace/url/custom_path';

    const XML_PATH_USE_CUSTOM_VENDOR_URL = 'marketplace/url/use_custom';

    const XML_PATH_CUSTOM_VENDOR_URL = 'marketplace/url/custom';

    /**
     * Backend area code
     */
    const AREA_CODE = 'marketplace';

    /**
     * @var array
     */
    protected $standardPorts = ['http' => '80', 'https' => '443'];

    /**
     * @var string
     */
    protected $defaultFrontName = 'marketplace';

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;


    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * @param \Magento\Backend\App\Config $config
     * @param DeploymentConfig $deploymentConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Backend\App\Config $config,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve area front name
     *
     * @param bool $checkHost If true, verify front name is valid for this url (hostname is correct)
     * @return string|bool
     */
    public function getFrontName($checkHost = false)
    {
        if ($checkHost && !$this->isHostBackend()) {
            return false;
        }
        $isCustomPathUsed = (bool)(string)$this->config->getValue(self::XML_PATH_USE_CUSTOM_VENDOR_PATH);
        if ($isCustomPathUsed) {
            return (string)$this->config->getValue(self::XML_PATH_CUSTOM_VENDOR_PATH);
        }
        return $this->defaultFrontName;
    }

    /**
     * Return whether the host from request is the backend host
     *
     * @return bool
     */
    public function isHostBackend()
    {
        if ($this->scopeConfig->getValue(self::XML_PATH_USE_CUSTOM_VENDOR_URL, ScopeInterface::SCOPE_STORE)) {
            $backendUrl = $this->scopeConfig->getValue(self::XML_PATH_CUSTOM_VENDOR_URL, ScopeInterface::SCOPE_STORE);
        } else {
            $backendUrl = $this->scopeConfig->getValue(Store::XML_PATH_UNSECURE_BASE_URL, ScopeInterface::SCOPE_STORE);
        }

        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        return stripos($this->getHostWithPort($backendUrl), $host) !== false;
    }

    /**
     * Get host with port
     *
     * @param string $url
     * @return mixed|string
     */
    private function getHostWithPort($url)
    {
        $scheme = parse_url(trim($url), PHP_URL_SCHEME);
        $host = parse_url(trim($url), PHP_URL_HOST);
        $port = parse_url(trim($url), PHP_URL_PORT);
        if (!$port) {
            $port = isset($this->standardPorts[$scheme]) ? $this->standardPorts[$scheme] : null;
        }
        return isset($port) ? $host . ':' . $port : $host;
    }
}
