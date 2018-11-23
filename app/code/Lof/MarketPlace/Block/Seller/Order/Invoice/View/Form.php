<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Block\Seller\Order\Invoice\View;

/**
 * Order view tabs
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\View\Form
{
	protected $_templates = 'Lof_MarketPlace::order/invoice/view/form.phtml';
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}
