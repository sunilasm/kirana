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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Lof\MarketPlace\Block\Adminhtml\Seller\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

class SellerActions extends Column
{
	/** Url Path */
	const SELLER_URL_PATH_EDIT = 'lofmarketplace/seller/edit';
	const SELLER_URL_PATH_DELETE = 'lofmarketplace/seller/delete';
    const SELLER_URL_PATH_ENABLE = 'lofmarketplace/seller/enable';
    const SELLER_URL_PATH_DISABLE = 'lofmarketplace/seller/disable';

	/** @var UrlBuilder */
	protected $actionUrlBuilder;

	/** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @param ContextInterface   $context            
     * @param UiComponentFactory $uiComponentFactory 
     * @param UrlBuilder         $actionUrlBuilder   
     * @param UrlInterface       $urlBuilder         
     * @param array              $components         
     * @param array              $data               
     * @param [type]             $editUrl            
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::SELLER_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['seller_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['seller_id' => $item['seller_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::SELLER_URL_PATH_DELETE, ['seller_id' => $item['seller_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ${ $.$data.name }'),
                            'message' => __('Are you sure you want to delete a ${ $.$data.name } record?')
                        ]
                    ];
                    $item[$name]['enable'] = [
                        'href' => $this->urlBuilder->getUrl(self::SELLER_URL_PATH_ENABLE, ['seller_id' => $item['seller_id']]),
                        'label' => __('Approved'),
                        'confirm' => [
                            'title' => __('Approved ${ $.$data.name }'),
                            'message' => __('Are you sure you want to enable a ${ $.$data.name } record?')
                        ]
                    ];
                    $item[$name]['disable'] = [
                        'href' => $this->urlBuilder->getUrl(self::SELLER_URL_PATH_DISABLE, ['seller_id' => $item['seller_id']]),
                        'label' => __('Disapproved'),
                        'confirm' => [
                            'title' => __('Disapproved ${ $.$data.name }'),
                            'message' => __('Are you sure you want to disable a ${ $.$data.name } record?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}