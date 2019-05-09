<?php

namespace Retailinsights\Slider\Block\Adminhtml\Index;

class Index extends \Magento\Backend\Block\Widget\Container
{
   
    protected $_postFactory;

    public function __construct(\Magento\Backend\Block\Widget\Context $context,
    \Retailinsights\Slider\Model\PostFactory $PostFactory,array $data = [])
    {
        parent::__construct($context, $data);
        $this->_postFactory = $PostFactory;
    }
    public function getImageCollection()
    {
      $ImageCollection = $this->_postFactory->create()->getCollection();
      return $ImageCollection;
    }
}
