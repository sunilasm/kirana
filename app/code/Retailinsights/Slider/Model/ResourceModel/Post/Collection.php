<?php
namespace Retailinsights\Slider\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'ps_id';
    protected $_eventPrefix = 'promoslider_images';
    protected $_eventObject = 'post_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Retailinsights\Slider\Model\Post', 'Retailinsights\Slider\Model\ResourceModel\Post');
    }

}
