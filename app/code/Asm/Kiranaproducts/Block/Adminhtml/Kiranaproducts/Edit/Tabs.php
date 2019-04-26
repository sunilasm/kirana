<?php
namespace Asm\Kiranaproducts\Block\Adminhtml\Kiranaproducts\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('kiranaproducts_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Kiranaproducts Information'));
    }
}