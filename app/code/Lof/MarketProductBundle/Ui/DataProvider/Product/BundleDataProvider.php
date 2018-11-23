<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketProductBundle\Ui\DataProvider\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Bundle\Helper\Data;

class BundleDataProvider extends ProductDataProvider
{
    /**
     * @var Data
     */
    protected $dataHelper;
     /**
     * @var Data
     */
    protected $helper;
    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Data $dataHelper
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        Data $dataHelper,
        array $meta = [],
        array $data = [],
        array $addFieldStrategies = [],
        array $addFilterStrategies = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );
        $this->helper = $helper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->addAttributeToFilter(
                'type_id',
                $this->dataHelper->getAllowedSelectionTypes()
            );
            $this->getCollection()->addFilterByRequiredOptions()->addAttributeToFilter('seller_id',$this->helper->getSellerId());
            $this->getCollection()->addStoreFilter(
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }
}
