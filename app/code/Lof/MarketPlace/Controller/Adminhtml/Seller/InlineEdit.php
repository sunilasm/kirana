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
namespace Lof\MarketPlace\Controller\Adminhtml\Seller;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface as PageRepository;

use Lof\MarketPlace\Model\Seller as SellerModel;

class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var PageRepository  */
    protected $sellerRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /** @var sellerModel */
    protected $sellerModel;

    /**
     * @param Context $context
     * @param PageRepository $sellerRepository
     * @param JsonFactory $jsonFactory
     * @param Lof\MarketPlace\Model\Seller $sellerModel
     */
    public function __construct(
        Context $context,
        PageRepository $sellerRepository,
        JsonFactory $jsonFactory,
        SellerModel $sellerModel
        ) {
        parent::__construct($context);
        $this->pageRepository = $sellerRepository;
        $this->jsonFactory = $jsonFactory;
        $this->sellerModel = $sellerModel;
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

        foreach (array_keys($postItems) as $sellerId) {
            /** @var \Lof\MarketPlace\Model\Group $seller */
            $seller = $this->_objectManager->create('Lof\MarketPlace\Model\Seller');
            $sellerData = $postItems[$sellerId];

            try {
                $seller->load($sellerId);
                $seller->setData(array_merge($seller->getData(), $sellerData));
                $seller->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithgroupId($seller, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithgroupId($seller, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPageId(
                    $page,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => 'abc',
            'error' => 'def'
            ]);
    }

    /**
     * Add page title to error message
     *
     * @param PageInterface $seller
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithgroupId($seller, $errorText)
    {
        return '[Page ID: ' . $seller->getId() . '] ' . $errorText;
    }
}