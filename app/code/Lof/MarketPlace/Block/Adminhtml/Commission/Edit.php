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
namespace Lof\MarketPlace\Block\Adminhtml\Commission;

/**
 * Seller edit block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize commission edit block
     *
     * @return void
     */
    protected function _construct(){

    	$this->_objectId = 'commission_id';
    	$this->_blockGroup = 'Lof_MarketPlace';
    	$this->_controller = 'adminhtml_commission';

    	parent::_construct();

    	if($this->_isAllowedAction('Lof_MarketPlace::commission_save')){
    		$this->buttonList->update('save','label',__('Save Commission'));
    		$this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
    	}else{
    		$this->buttonList->remove('save');
    	}

    	if ($this->_isAllowedAction('Lof_MarketPlace::commission_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Commission'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('lof_marketplace_commission')->getId()) {
            return __("Edit Commission '%1'", $this->escapeHtml($this->_coreRegistry->registry('lof_marketplace_commission')->getName()));
        } else {
            return __('New Commission');
        }
    }
     /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    
    protected function _afterToHtml($html)
    {
        return parent::_afterToHtml($html . $this->_getJsInitScripts());
    }
     protected function _getJsInitScripts()
    {
        return "
        <script>
            require([
                'jquery',
                'domReady!'
            ], function($){
                
                function hideShowCommissionAction(){
                    if ($('#rule_commission_by').val() == 'by_percent') {
                        $('#rule_commission_action').parent().parent().show();
                    } else {
                        $('#rule_commission_action').parent().parent().hide();
                    }

                    return true;
                }
                hideShowCommissionAction();
                window.hideShowCommissionAction = hideShowCommissionAction;
            });
        </script> ";

    }
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('cms/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
