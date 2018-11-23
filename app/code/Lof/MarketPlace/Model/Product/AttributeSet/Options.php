<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Model\Product\AttributeSet;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var null|array
     */
    protected $options;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $product
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $product,
        \Lof\MarketPlace\Helper\Data $helper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->product = $product;
        $this->helper =  $helper;
    }

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (null == $this->options) {
            $this->options = $this->collectionFactory->create()
                ->setEntityTypeFilter($this->product->getTypeId())
                ->addFieldToFilter('attribute_set_id',['nin'=>$this->helper->getAttributeSetRestriction()])
                ->toOptionArray();
        }
        return $this->options;
    }
}
