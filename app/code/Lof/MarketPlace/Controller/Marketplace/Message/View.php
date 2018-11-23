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
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Controller\Marketplace\Message;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;

abstract class View  extends \Magento\Customer\Controller\AbstractAccount 
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->registry = $registry;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Message information page
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
       
         $resultPage = $this->resultPageFactory->create();
     
        return $resultPage;
    }
}
