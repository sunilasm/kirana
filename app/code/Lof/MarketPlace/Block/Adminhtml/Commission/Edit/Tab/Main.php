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
namespace Lof\MarketPlace\Block\Adminhtml\Commission\Edit\Tab;

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
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_viewHelper;

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
        \Lof\MarketPlace\Helper\Data $viewHelper,
        \Lof\MarketPlace\Model\Group $group,
        array $data = []
    ) {
        $this->group = $group;
        $this->_viewHelper = $viewHelper;
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
       
    	/** @var $model \Lof\MarketPlace\Model\Commission */
    	$model = $this->_coreRegistry->registry('lof_marketplace_commission');

    	/**
    	 * Checking if user have permission to save information
    	 */
    	if($this->_isAllowedAction('Lof_MarketPlace::commission_edit')){
    		$isElementDisabled = false;
    	}else {
    		$isElementDisabled = true;
    	}

    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	//$form->setHtmlIdPrefix('seller_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Commission Information')]);

    	if ($model->getId()) {
    		$fieldset->addField('commission_id', 'hidden', ['name' => 'commission_id']);
    	}

    	$fieldset->addField(
    		'commission_title',
    		'text',
    		[
	    		'name' => ' commission_title',
	    		'label' => __('Title'),
	    		'title' => __('Title'),
	    		'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    		);
        $fieldset->addField(
            'description',
            'text',
            [
                'name' => ' description',
                'label' => __('Description'),
                'title' => __('Description'),
                'disabled' => $isElementDisabled
            ]
            );
         $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'is_active',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
          $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
    	$fieldset->addField(
            'from_date',
            'date',
            [
                'name' => 'from_date',
                'label' => __('From'),
                'title' => __('From'),
                 'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'to_date',
            'date',
            [
                'name' => 'to_date',
                'label' => __('To'),
                'title' => __('To'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'priority',
            'text',
            [
                'name' => 'priority',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'disabled' => $isElementDisabled
            ]
        );
        $group = $this->group->getCollection();
        $groups = [];
        foreach ($group as $k => $v) {
            $groups[] = [
                'label' => $v->getName(),
                'value'=> $v->getGroupId()
            ];
        }
        $fieldset->addField(
            'group_id',
            'multiselect',
            [
                'label' => __('Seller Group'),
                'title' => __('Seller Group'),
                'name' => 'groups[]',
                'values' => $groups,
                'disabled' => $isElementDisabled
            ]
        );
        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
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
        return __('Commission Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Commission Information');
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