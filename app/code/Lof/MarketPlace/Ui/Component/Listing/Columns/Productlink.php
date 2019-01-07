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
 * Class Productlink.
 */
class Productlink extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    protected $_productloader; 
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
        \Magento\Catalog\Model\ProductFactory $_productloader,
        array $components = [],
        array $data = []
    ) {
        $this->_productloader = $_productloader;
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
        $objectManager       = \Magento\Framework\App\ObjectManager::getInstance ();
        
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['product_id'])) {

                    $product = $this->_productloader->create()->load($item['product_id']);
                   
                     if(!$item['product_id']) {
                        $sellerProduct = $objectManager->create('Lof\MarketPlace\Model\SellerProduct')->load($item['product_id'],'product_id');
                       $sellerProduct->setProductName($product->getName())->save();
                    }
                    $item[$fieldName] = "<a href='".$this->urlBuilder->getUrl('catalog/product/edit', ['id' => $item['product_id']])."' target='blank' title='".__('View Product')."'>".$product->getName().'</a>';

                }
            }
        }

        return $dataSource;
    }
}
