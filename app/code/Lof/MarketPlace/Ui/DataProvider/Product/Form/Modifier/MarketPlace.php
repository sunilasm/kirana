<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Lof\MarketPlace\Helper\Data as MarketPlaceHelper;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;

/**
 * Data provider for "Customizable Options" panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MarketPlace extends AbstractModifier
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_marketPlaceHelper;
    
    /**
     * Set collection factory
     *
     * @var AttributeSetCollectionFactory
     */
    protected $_attributeSetCollectionFactory;
    
    /**
     * @var LocatorInterface
     */
    protected $locator;
    
    /**
     * Constructor
     * 
     * @param MarketPlaceHelper $marketPlaceHelper
     * @return \Lof\MarketPlace\Ui\DataProvider\Product\Form\Modifier\MarketPlace
     */
    public function __construct(
        LocatorInterface $locator,
        MarketPlaceHelper $marketPlaceHelper,
        AttributeSetCollectionFactory $attributeSetCollectionFactory
    ) {
        $this->locator = $locator;
        $this->_marketPlaceHelper = $marketPlaceHelper;
        $this->_attributeSetCollectionFactory = $attributeSetCollectionFactory;
        
        return $this;
    }
    /**
     * @var array
     */
    protected $_meta = [];
    

    public function modifyData(array $data){
        return $data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->_meta = $meta;
        $this->removeNotUsedSections();
        $this->updateCustomOptionsJs();
        
        return $this->_meta;
    }
    
    /**
     * Remove not used sections
     */
    public function removeNotUsedSections(){
        if(isset($this->_meta['product-details']['children']['container_seller_id']))
            unset($this->_meta['product-details']['children']['container_seller_id']);
         if(isset($this->_meta['product-details']['children']['container_approval']))
            unset($this->_meta['product-details']['children']['container_approval']);
    }
    /**
     * Update custom options js
     */
    public function updateCustomOptionsJs(){
        $this->_meta['custom_options']['children']['options']['children']['record']
        ['children']['container_option']['children']['container_common']
        ['children']['type']['arguments']['data']['config']['component']
        = 'Lof_MarketPlace/js/custom-options-type';
        
        $this->_meta['custom_options']['children']['options']['children']['record']
        ['children']['container_option']['children']['container_common']
        ['children']['title']['arguments']['data']['config']['component']
        = 'Lof_MarketPlace/component/static-type-input';
    }
}
