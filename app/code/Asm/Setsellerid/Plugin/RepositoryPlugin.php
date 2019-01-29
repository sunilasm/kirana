<?php

namespace Asm\Setsellerid\Plugin;

class RepositoryPlugin
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    public function beforeSave(
        \Magento\Quote\Model\Quote\Item\Repository $subject,
        \Magento\Quote\Api\Data\CartItemInterface $cartItem
    )
    {
        $cartId = $cartItem->getQuoteId();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->getShippingAddress();
    }
}