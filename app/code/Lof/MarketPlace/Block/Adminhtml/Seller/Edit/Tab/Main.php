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
     * @param \Lof\MarketPlace\Helper\Data                  $viewHelper    
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Seller Information')]);


        if ($model->getId()) {
            $fieldset->addField('seller_id', 'hidden', ['name' => 'seller_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Seller Name'),
                'title' => __('Seller Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        if($model->getData('url_key')) {
            $fieldset->addField(
                'link_seller',
                'note',
                [
                'name' => 'link_seller',
                'label' => __('Link Seller'),
                'title' => __('Link Seller'),
                'text' => $model->getUrl()
                ]
            );
        }
        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'note' => __('Empty to auto create url key'),
                'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'contact_number',
            'text',
            [
                'name' => 'contact_number',
                'label' => __('Contact Number'),
                'title' => __('Contact Number'),
                'disabled' => $isElementDisabled
            ]
        );
        
         $fieldset->addField(
            'shop_title',
            'text',
            [
                'name' => 'shop_title',
                'label' => __('Shop Title'),
                'title' => __('Shop Title'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'group_id',
            'select',
            [
                'label' => __('Seller Group'),
                'title' => __('Seller Group'),
                'name' => 'group_id',
                'required' => true,
                'options' => $this->_viewHelper->getGroupList(),
                'disabled' => $isElementDisabled
            ]
        );
         $fieldset->addField(
            'customer_id',
            'select',
            [
                'label' => __('Customer'),
                'title' => __('Customer'),
                'name' => 'customer_id',
                'required' => true,
                'options' => $this->_viewHelper->getCustomerList(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Company Banner'),
                'title' => __('Company Banner'),
                'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'thumbnail',
            'image',
            [
                'name' => 'thumbnail',
                'label' => __('Company Logo'),
                'title' => __('Company Logo'),
                'disabled' => $isElementDisabled
            ]
            );

        $wysiwygDescriptionConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        $fieldset->addField(
            'company_locality',
            'text',
            [
                'name' => 'company_locality',
                'label' => __('Company Locality'),
                'title' => __('Company Locality'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'company_description',
            'editor',
            [
                'name' => 'company_description',
                'style' => 'height:200px;',
                'label' => __('Company Description'),
                'title' => __('Company Description'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
            ]
        );
        $fieldset->addField(
            'return_policy',
            'editor',
            [
                'name' => 'return_policy',
                'style' => 'height:200px;',
                'label' => __('Return Policy'),
                'title' => __('Return Policy'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
            ]
        );
         $fieldset->addField(
            'shipping_policy',
            'editor',
            [
                'name' => 'shipping_policy',
                'style' => 'height:200px;',
                'label' => __('Shipping Policy'),
                'title' => __('Shipping Policy'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
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
          $fieldset->addField(
            'address',
            'text',
            [
                'name' => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
          $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
          $fieldset->addField(
            'region',
            'text',
            [
                'name' => 'region',
                'label' => __('State'),
                'title' => __('State'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'country',
            'text',
            [
                'name' => 'country',
                'label' => __('Country'),
                'title' => __('Country'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name' => 'postcode',
                'label' => __('Postcode'),
                'title' => __('Postcode'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'geo_lat',
            'text',
            [
                'name' => 'geo_lat',
                'label' => __('Latitude'),
                'title' => __('Latitude'),
                'required' => true,
                'readonly' => true
            ]
        );

        $fieldset->addField(
            'geo_lng',
            'text',
            [
                'name' => 'geo_lng',
                'label' => __('Longitude'),
                'title' => __('Longitude'),
                'required' => true,
                'readonly' => true
            ]
        );


        $fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Page Status'),
                'name' => 'status',
                'options' => $model->getAvailableStatuses(),
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
}