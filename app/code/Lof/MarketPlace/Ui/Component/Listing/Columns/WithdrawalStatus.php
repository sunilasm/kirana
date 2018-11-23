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

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class WithdrawalStatus.
 */
class WithdrawalStatus extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder; 

    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {

                if (isset($item['status'])) {
                    if($item['status'] == 0) {
                        $item[$fieldName] = __('Pending');
                        $item[$fieldName . '_html'] = '<span class="fue-status fue-status-pending">' . ucfirst(__('Pending')) . '</span>';
                    } elseif($item['status'] == 1) {
                        $item[$fieldName] = __('Completed');
                        $item[$fieldName . '_html'] = '<span class="fue-status fue-status-completed">' . ucfirst(__('Completed')) . '</span>';
                    } elseif($item['status'] == 2) {
                        $item[$fieldName] = __('Canceled');
                        $item[$fieldName . '_html'] = '<span class="fue-status fue-status-canceled">' . ucfirst(__('Canceled')) . '</span>';
                    }
                    
                }
            }
        }
        return $dataSource;
    }
}
