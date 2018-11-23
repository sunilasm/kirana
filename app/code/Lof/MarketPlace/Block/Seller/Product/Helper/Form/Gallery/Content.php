<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Block\Seller\Product\Helper\Form\Gallery;

class Content extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content
{
    /**
     * @var string
     */
     protected $_template = 'Lof_MarketPlace::catalog/product/helper/gallery.phtml';

    
    /**
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild('uploader', 'Lof\MarketPlace\Block\Seller\Product\Helper\Form\Gallery\Uploader');

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()->getUrl('catalog/product_gallery/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );

        $this->_eventManager->dispatch('catalog_product_gallery_prepare_layout', ['block' => $this]);

        return \Magento\Framework\View\Element\AbstractBlock::_prepareLayout();
    }
}
