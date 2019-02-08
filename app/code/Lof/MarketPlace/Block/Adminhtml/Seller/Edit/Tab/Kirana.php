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
namespace Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab;

class Kirana extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

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
     * @param \Lof\MarketPlace\Helper\Data            $viewHelper    
     * @param array                                   $data          
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\MarketPlace\Helper\Data $viewHelper,
        array $data = []
    ) {
        $this->_viewHelper = $viewHelper;
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
    	/** @var $model \Lof\MarketPlace\Model\Seller */
    	$model = $this->_coreRegistry->registry('lof_marketplace_seller');

    	/**
    	 * Checking if user have permission to save information
    	 */
    	if($this->_isAllowedAction('Lof_MarketPlace::seller_edit')){
    		$isElementDisabled = false;
    	}else {
    		$isElementDisabled = true;
    	}

    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('seller_');
        //$store_type = $this->_websiteFactory->create()->getCollection()->toOptionArray();
    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Kirana Attributes')]);


    	if ($model->getId()) {
    		$fieldset->addField('seller_id', 'hidden', ['name' => 'seller_id']);
    	}

    	$fieldset->addField(
    		'store_type',
    		'select',
    		array(
	    		'name' => 'store_type',
	    		'label' => __('Store Type'),
                'title' => __('Store Type'),
                'values' =>  $this->storeTypetoOptionArray(),
	    		'required' => true,
                'disabled' => $isElementDisabled
    		)
    	);
        $fieldset->addField(
    		'24by7_shop',
    		'select',
    		[
	    		'name' => '24by7_shop',
	    		'label' => __('24*7 Shop'),
                'title' => __('24*7 Shop'),
                'values' =>  $this->toOptionArray(),
                'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    	);
    	$fieldset->addField(
    		'opening_time',
    		'text',
    		[
	    		'name' => 'opening_time',
	    		'label' => __('Opening Time'),
                'title' => __('Opening Time'),
                'class' => '',
                'disabled' => $isElementDisabled
    		]
        );
        $fieldset->addField(
    		'closeing_time',
    		'text',
    		[
	    		'name' => 'closeing_time',
	    		'label' => __('Closing Time'),
                'title' => __('Closing Time'),
                'class' => '',
                'disabled' => $isElementDisabled
    		]
        );
        $fieldset->addField(
            'non_working_days',
            'text',
            [
                'name' => 'non_working_days',
                'label' => __('Days Not Working'),
                'title' => __('Days Not Working'),
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'pan',
            'text',
            [
                'name' => 'pan',
                'label' => __('PAN'),
                'title' => __('PAN'),
                'class' => 'alphanumeric',
                'disabled' => $isElementDisabled
            ]
        );

       
        $fieldset->addField(
            'gst',
            'text',
            [
                'name' => 'gst',
                'label' => __('GST'),
                'title' => __('GST'),
                'class' => 'alphanumeric validate-length minimum-length-15 maximum-length-15',
                'max_text_length' => 10,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'digital_verification',
            'select',
            [
                'label' => __('Digital Verification'),
                'title' => __('Digital Verification '),
                'name' => 'digital_verification',
                'values' =>  $this->toOptionArray(),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'physical_verification',
            'select',
            [
                'label' => __('Physical Verification'),
                'title' => __('Physical Verification'),
                'name' => 'physical_verification',
                'required' => true,
                'values' =>  $this->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'smart_phone',
            'select',
            [
                'label' => __('Smartphone with data'),
                'title' => __('Smartphone with data'),
                'name' => 'smart_phone',
                'values' =>  $this->toOptionArray(),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
    	$fieldset->addField(
            'knows_english',
            'select',
            [
                'label' => __('Knows English'),
                'title' => __('Knows English'),
                'name' => 'knows_english',
                'values' =>  $this->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
    	$fieldset->addField(
            'parent_store',
            'select',
            [
                'label' => __('Parent Store'),
                'title' => __('Parent Store'),
                'name' => 'parent_store',
                'required' => true,
                'values' =>  $this->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'parent_store_id',
            'text',
            [
                'name' => 'parent_store_id',
                'label' => __('Parent Store ID'),
                'title' => __('Parent Store ID'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'kirana_type',
            'select',
            [
                'label' => __('Kirana Type'),
                'title' => __('Kirana Type'),
                'name' => 'kirana_type',
                'values' =>  $this->kiranaTypetoOptionArray(),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
    	$fieldset->addField(
            'kirana_locality',
            'select',
            [
                'label' => __('Kirana Locality'),
                'title' => __('Kirana Locality'),
                'name' => 'kirana_locality',
                'values' =>  $this->kiranaLocalitytoOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'kirana_owner',
            'text',
            [
                'name' => 'kirana_owner',
                'label' => __('Kirana Owner'),
                'title' => __('Kirana Owner'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'kirana_fixed_line',
            'text',
            [
                'name' => 'kirana_fixed_line',
                'label' => __('Kirana Fixed Line'),
                'title' => __('Kirana Fixed Line'),
                // 'class' => 'number validate-range validate-number-range-10-10',
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
       // $values['store_type'] = array(2);
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
        return __('Seller Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Seller Information');
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
    public function  storeTypetoOptionArray()
    {
        $storeTypeAttribute = [
                ['value' => '0', 'label' => __('Select an option')],
                ['value' => 'KIR', 'label' => __('KIR')],
                ['value' => 'MED', 'label' => __('MED')],
                ['value' => 'NA', 'label' => __('NA')]
            ];
        return $storeTypeAttribute;
    }
    public function  kiranaLocalitytoOptionArray()
    {
        $storeTypeAttribute = [
                ['value' => '0', 'label' => __('Select an option')],
                ['value' => 'KIR', 'label' => __('KIR')],
                ['value' => 'MED', 'label' => __('MED')],
                ['value' => 'NA', 'label' => __('NA')]
            ];
        return $storeTypeAttribute;
    }
    public function  kiranaTypetoOptionArray()
    {
        $storeTypeAttribute = [
                ['value' => '0', 'label' => __('Select an option')],
                ['value' => 'KIR', 'label' => __('KIR')],
                ['value' => 'MED', 'label' => __('MED')],
                ['value' => 'NA', 'label' => __('NA')]
            ];
        return $storeTypeAttribute;
    }
    public function  toOptionArray()
    {
        $storeTypeAttribute = [
                ['value' => '0', 'label' => __('Select an option')],
                ['value' => 'Yes', 'label' => __('Yes')],
                ['value' => 'No', 'label' => __('No')]
            ];
        return $storeTypeAttribute;
    }
}