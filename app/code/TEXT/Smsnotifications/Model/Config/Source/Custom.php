<?php
namespace TEXT\Smsnotifications\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use \Magento\Framework\Event\Observer       as Observer;
use \Magento\Framework\View\Element\Context as Context;
use \TEXT\Smsnotifications\Helper\Data                 as Helper;

/*
Custome class for multiselect order status value
*/
class Custom implements ArrayInterface
{
	public function __construct(
        Context $context,
        Helper $helper
    ) {
        $this->_helper  = $helper;
        $this->_request = $context->getRequest();
        $this->_layout  = $context->getLayout();
    }
public function toOptionArray()
    {
    return [
	    ['value'=>'placeorder' ,'label'=>_('PlaceOrder')],
        ['value'=>'hold' ,'label'=>_('Hold')],
        ['value'=>'unhold' ,'label'=>_('Unhold')],
        ['value'=>'complete' ,'label'=>_('Complete')],
        ['value'=>'cancel' ,'label'=>_('Cancel')],
        ['value'=>'invoice' ,'label'=>_('Invoice')],
        ['value'=>'shippment' ,'label'=>_('Shippement')]

        ];
    }
}