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
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
/**
 * Class Commission
 */
class Commission1 extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Webkul\Marketplace\Model\Product
     */
    protected $commission;

    /**
     * Constructor
     *
     * @param \Magento\Cms\Model\Page $cmsPage
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = [],
        \Lof\MarketPlace\Model\Commission $commission
        )
    {
         parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
        $this->commission = $commission;
    }

    
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = 'commission';
             
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    $item[$fieldName] = $item[$fieldName].' %';
                }
            }
        }

        return $dataSource;
    }
}
