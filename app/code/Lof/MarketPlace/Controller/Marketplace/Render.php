<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Controller\Marketplace;

use Lof\MarketPlace\Controller\Marketplace\AbstractAction;
use Magento\Framework\View\Element\UiComponentInterface;
use Lof\MarketPlace\App\Action\Context;
use Magento\Ui\Controller\UiActionInterface;
use Magento\Framework\View\Element\UiComponentFactory;
/**
 * Class Render
 */
class Render extends \Magento\Framework\App\Action\Action
{
    /**
     * @var UiComponentFactory
     */
    protected $factory;

    /**
     * @param Context $context
     * @param UiComponentFactory $factory
     */
    public function __construct(Context $context, UiComponentFactory $factory)
    {
        parent::__construct($context);
        $this->factory = $factory;
    }

    /**
     * Action for AJAX request
     *
     * @return void
     */
    public function execute()
    {
        $component = $this->factory->create($this->_request->getParam('namespace'));
        $this->prepareComponent($component);
        $this->_response->appendBody((string) $component->render());
    }

    /**
     * Call prepare method in the component UI
     *
     * @param UiComponentInterface $component
     * @return void
     */
    protected function prepareComponent(UiComponentInterface $component)
    {
        foreach ($component->getChildComponents() as $child) {
            $this->prepareComponent($child);
        }
        $component->prepare();
    }
}
