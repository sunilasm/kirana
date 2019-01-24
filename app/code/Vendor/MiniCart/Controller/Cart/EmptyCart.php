<?php

namespace Vendor\MiniCart\Controller\Cart;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;

class EmptyCart extends Action
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * EmptyCart constructor.
     *
     * @param Context $context
     * @param Session $session
     * @param JsonFactory $jsonFactory
     * @param Data $jsonHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Session $session,
        JsonFactory $jsonFactory,
        Data $jsonHelper,
        LoggerInterface $logger,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->checkoutSession = $session;
        $this->jsonFactory = $jsonFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->cart = $cart;
        parent::__construct($context);
    }

    /**
     * Ajax execute
     *
     */
    public function execute()
    {
        $response = [
            'errors' => false
        ];

        if ($this->getRequest()->isAjax()) {
            try {
                $this->cart->truncate()->save();
                $response['message'] = __('Empty Cart.');

            } catch (\Exception $e) {
                $response = [
                    'errors' => true,
                    'message' => __('Some thing went wrong.')
                ];
                $this->logger->critical($e);
            }
        } else {
            $response = [
                'errors' => true,
                'message' => __('Need to access via Ajax.')
            ];
        }
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultJson = $this->jsonFactory->create();
        return $resultJson->setData($response);
    }
}

