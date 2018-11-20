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
namespace Lof\MarketPlace\Block\Adminhtml\Rating\Edit\Tab;

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

    protected $helper;

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
        \Lof\MarketPlace\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm(){
      
    	/** @var $model \Lof\MarketPlace\Model\Rating */
    	$model = $this->_coreRegistry->registry('lof_marketplace_rating');

    	/**
    	 * Checking if user have permission to save information
    	 */
    	if($this->_isAllowedAction('Lof_MarketPlace::rating_edit')){
    		$isElementDisabled = false;
    	}else {
    		$isElementDisabled = true;
    	}

    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('rating_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Rating Information')]);
       
    	if ($model->getId()) {
    		$fieldset->addField('rating_id', 'hidden', ['name' => 'rating_id']);
    	}


    	$fieldset->addField(
    		'nickname',
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
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'detail',
            'textarea',
            [
                'name' => 'detail',
                'label' => __('Detail'),
                'title' => __('Detail'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'seller_id',
            'text',
            [
                'name' => 'seller_id',
                'label' => __('Seller Id'),
                'title' => __('Seller Id'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'rate1',
            'text',
            [
                'name' => 'rate1',
                'label' => __('Rate 1'),
                'title' => __('Rate 1'),
                'disabled' => $isElementDisabled
            ]
            );
       $fieldset->addField(
            'rate2',
            'text',
            [
                'name' => 'rate2',
                'label' => __('Rate 2'),
                'title' => __('Rate 2'),
                'disabled' => $isElementDisabled
            ]
            );
       $fieldset->addField(
            'rate3',
            'text',
            [
                'name' => 'rate3',
                'label' => __('Rate 3'),
                'title' => __('Rate 3'),
                'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'options' => $this->helper->statusRating(),
                'disabled' => $isElementDisabled
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
        return __('Rating Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Rating Information');
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