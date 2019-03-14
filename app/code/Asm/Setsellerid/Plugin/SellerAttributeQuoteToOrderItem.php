<?php
namespace Asm\Setsellerid\Plugin;
 
class SellerAttributeQuoteToOrderItem
{
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);
        $orderItem->setSellerId($item->getSellerId());
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
    $logger = new \Zend\Log\Logger();
    $logger->addWriter($writer);
    $logger->info('sds');
        return $orderItem;
    }
}
?>