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
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketProductConfigurable\Controller\Marketplace\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Framework\App\Action\Context;

/**
 * Class Wizard
 */
class Wizard extends Action
{
    /**
     * @var Builder
     */
    protected $productBuilder;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     */
    public function __construct(Context $context, Builder $productBuilder)
    {
        parent::__construct($context);
        $this->productBuilder = $productBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->productBuilder->build($this->getRequest());

        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultLayout->getLayout()->getUpdate()->removeHandle('default');

        return $resultLayout;
    }
}
