<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Block\Seller;

use Magento\Framework\Message\MessageInterface;

/**
 * Adminhtml footer block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Messages extends \Magento\Framework\View\Element\Messages
{
    /**
     * @var Message\InterpretationStrategyInterface
     */
    protected $interpretationStrategy;
    
    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param \Magento\Framework\Message\Factory $messageFactory
     * @param \Magento\Framework\Message\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Message\InterpretationStrategyInterface $interpretationStrategy
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $interpretationStrategy,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy
        );
        $this->interpretationStrategy = $interpretationStrategy;
        
        $this->addMessages($this->messageManager->getMessages(true));
    }
    
    /**
     * Render messages in HTML format grouped by type
     *
     * @return string
     */
    protected function _renderMessagesByType()
    {
        $html = '';
        foreach ($this->getMessageTypes() as $type) {
            if ($messages = $this->getMessagesByType($type)) {
                if (!$html) {
                    $html .= '<' . $this->firstLevelTagName . ' class="messages">';
                }
                $messateType = $type;
                switch($type){
                    case MessageInterface::TYPE_ERROR:
                        $title = __("Error!");
                        $class = 'fa-ban';
                        break;
                    case MessageInterface::TYPE_NOTICE:
                        $title = __("Info");
                        $class = 'fa-info';
                        $messateType = 'info';
                        break;
                    case MessageInterface::TYPE_SUCCESS:
                        $title = __("Success");
                        $class = 'fa-check';
                        break;
                    case MessageInterface::TYPE_WARNING:
                        $title = __("Alert");
                        $class = 'fa-warning';
                        break;
                }
                foreach ($messages as $message) {
                    $html .= '<' . $this->secondLevelTagName . ' class="alert ' . 'alert-' . $messateType . ' ' . $type .
                    ' alert-dismissable">';
                    $html .= '<' . $this->contentWrapTagName . $this->getUiId('message', $type) . '>';
                    $html .= '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>';
                    $html .= '<h4><i class="icon fa '.$class.'"></i> '.$title.'</h4>';
                    $html .= $this->interpretationStrategy->interpret($message);
                    $html .= '</' . $this->contentWrapTagName . '>';
                    $html .= '</' . $this->secondLevelTagName . '>';
                }
            }
        }
        if ($html) {
            $html .= '</' . $this->firstLevelTagName . '>';
        }
        return $html;
    }
}
