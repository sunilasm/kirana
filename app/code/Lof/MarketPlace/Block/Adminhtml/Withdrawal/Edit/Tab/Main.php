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
namespace Lof\MarketPlace\Block\Adminhtml\Withdrawal\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    protected $_wysiwygConfig1;

    /**
     * @param \Magento\Backend\Block\Template\Context $context       
     * @param \Magento\Framework\Registry             $registry      
     * @param \Magento\Framework\Data\FormFactory     $formFactory   
     * @param \Magento\Store\Model\System\Store       $systemStore   
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig 
     * @param array                                   $data          
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_wysiwygConfig1 = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm(){
      
    	/** @var $model \Lof\MarketPlace\Model\Withdrawal */
    	$model = $this->_coreRegistry->registry('lof_marketplace_withdrawal');

    	/**
    	 * Checking if user have permission to save information
    	 */
    	if($this->_isAllowedAction('Lof_MarketPlace::withdrawal_edit')){
    		$isElementDisabled = false;
    	}else {
    		$isElementDisabled = true;
    	}

    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('seller_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Withdrawal Information')]);
       
    	if ($model->getId()) {
    		$fieldset->addField('withdrawal_id', 'hidden', ['name' => 'withdrawal_id']);
    	}


    	$fieldset->addField(
    		'name',
    		'text',
    		[
	    		'name' => 'name',
	    		'label' => __('Name'),
	    		'title' => __('Name'),
                'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    		);
        $fieldset->addField(
            'fee',
            'text',
            [
                'name' => 'fee',
                'label' => __('Fee'),
                'title' => __('Fee'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'min_amount',
            'text',
            [
                'name' => 'min_amount',
                'label' => __('Min Amount'),
                'title' => __('Min Amount'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'max_amount',
            'text',
            [
                'name' => 'max_amount',
                'label' => __('Max Amount'),
                'title' => __('Max Amount'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'order',
            'text',
            [
                'name' => 'order',
                'label' => __('Order Withdrawal'),
                'title' => __('Order Withdrawal'),
                'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'style' => 'height:200px;',
                'label' => __('Description'),
                'title' => __('Description'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldset->addField(
            'message',
            'textarea',
            [
                'name' => 'message',
                'label' => __('Note Message'),
                'title' => __('Note Message'),
                'disabled' => $isElementDisabled,
            ]
        );
    	$form->setValues($model->getData());
    	$this->setForm($form);
 
    	return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Withdrawal Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Withdrawal Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
}