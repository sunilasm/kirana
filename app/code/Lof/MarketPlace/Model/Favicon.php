<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Model;


class Favicon extends \Magento\Theme\Model\Favicon\Favicon
{
    /**
     * @return string
     */
    public function getDefaultFavicon()
    {
        return 'favicon.ico';
    }
    
    /**
     * @return string
     */
    protected function prepareFaviconFile()
    {
  
        $folderName = \Magento\Config\Model\Config\Backend\Image\Favicon::UPLOAD_DIR;
        $scopeConfig = $this->scopeConfig->getValue(
            'lofmarketplace/design/head_favicon',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $path = $folderName . '/' . $scopeConfig;
        $faviconUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path;

        if ($scopeConfig !== null && $this->checkIsFile($path)) {
            return $faviconUrl;
        }
    
        return false;
    }
}
