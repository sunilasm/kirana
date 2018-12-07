<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Controller\Adminhtml\Payment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface as PageRepository;

use Lof\MarketPlace\Model\Payment as PaymentModel;

class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var PageRepository  */
    protected $paymentRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /** @var paymentModel */
    protected $paymentModel;

    /**
     * @param Context        $context         
     * @param PageRepository $paymentRepository 
     * @param JsonFactory    $jsonFactory     
     * @param PaymentModel     $paymentModel      
     */
    public function __construct(
        Context $context,
        PageRepository $paymentRepository,
        JsonFactory $jsonFactory,
        PaymentModel $paymentModel
        ) {
        parent::__construct($context);
        $this->pageRepository = $paymentRepository;
        $this->jsonFactory = $jsonFactory;
        $this->paymentModel = $paymentModel;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
                ]);
        }

        foreach (array_keys($postItems) as $paymentId) {
            /** @var \Lof\MarketPlace\Model\Payment $payment */
            $payment = $this->_objectManager->create('Lof\MarketPlace\Model\Payment');
            $paymentData = $postItems[$paymentId];

            try {
                $payment->load($paymentId);
                $payment->setData(array_merge($payment->getData(), $paymentData));
                $payment->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithpaymentId($payment, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithpaymentId($payment, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithpaymentId(
                    $payment,
                    __('URL key already exists.')
                    );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
            ]);
    }

    /**
     * Add page title to error message
     *
     * @param PageInterface $payment
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithpaymentId($payment, $errorText)
    {
        return '[Page ID: ' . $payment->getId() . '] ' . $errorText;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::payment_save');
    }
}