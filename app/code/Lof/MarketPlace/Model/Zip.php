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

use Magento\Framework\Archive;

/**
 * Orderitems Model
 */
class Zip extends Archive
{
	/**
     * Unzip Images.
     *
     * @param string $source
     * @param string $destination
     */
    public function unzipImages($source, $destination)
    {
        $zip = new \ZipArchive();
        $zip->open($source);
        $zip->extractTo($destination);
        $zip->close();
    }

    /**
     * Unzip Link Files.
     *
     * @param string $source
     * @param string $destination
     *
     * @return string
     */
    public function unzipLinks($source, $destination)
    {
        $zip = new \ZipArchive();
        $zip->open($source);
        $zip->extractTo($destination);
        $zip->close();
    }
}