<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Block\Seller\Widget\Form;

/**
 * Adminhtml footer block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Container extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var string
     */
    protected $_template = 'Lof_MarketPlace::widget/form/container.phtml';
    
    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->updateButton('save', 'class', 'btn-success');
        $this->updateButton('reset', 'class', 'btn-warning');
        $this->updateButton('delete', 'class', 'btn-danger');
        $this->updateButton('back', 'class', 'btn-github');
    }
}
