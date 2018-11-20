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
namespace Lof\MarketPlace\Block\Adminhtml\Payment;

/**
 * Payment edit block
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
     * Initialize payment edit block
     *
     * @return void
     */
    protected function _construct(){

    	$this->_objectId = 'payment_id';
    	$this->_blockGroup = 'Lof_MarketPlace';
    	$this->_controller = 'adminhtml_payment';

    	parent::_construct();

    	if($this->_isAllowedAction('Lof_MarketPlace::payment_save')){
    		$this->buttonList->update('save','label',__('Save Payment'));
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

    	if ($this->_isAllowedAction('Lof_MarketPlace::payment_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Payment'));
        } else {
            $this->buttonList->remove('delete');
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
                
                function hideShowFeeAction(){
                    if ($('#seller_fee_by').val() == 'by_percent') {
                        $('#seller_fee_percent').parent().parent().show();
                        $('#seller_fee').parent().parent().hide();
                    } else if($('#seller_fee_by').val() == 'by_fixed') {
                        $('#seller_fee_percent').parent().parent().hide();
                         $('#seller_fee').parent().parent().show();
                    } else if($('#seller_fee_by').val() == 'all') {
                        $('#seller_fee').parent().parent().show();
                         $('#seller_fee_percent').parent().parent().show();
                    }

                    return true;
                }
                hideShowFeeAction();
                window.hideShowFeeAction = hideShowFeeAction;
            });
        </script> ";

    }
    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('lof_marketplace_payment')->getId()) {
            return __("Edit Payment '%1'", $this->escapeHtml($this->_coreRegistry->registry('lof_marketplace_payment')->getName()));
        } else {
            return __('New Payment');
        }
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
