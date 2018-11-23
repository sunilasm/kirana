<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Ui\DataProvider\Product\Seller;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class ProductDataProvider
 */
class ProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
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
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory,$addFieldStrategies,$addFilterStrategies,$meta,$data);
     
        if($helper->getSellerId()) {
            $this->collection->addAttributeToFilter('seller_id',$helper->getSellerId());
        } else {
            $this->collection->addAttributeToFilter('seller_id',0);
        }
        /*Join with vendor table.*/
    }
}
