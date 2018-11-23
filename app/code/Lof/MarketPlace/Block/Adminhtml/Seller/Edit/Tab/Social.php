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

/**
 * Customer account form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Social extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Lof_MarketPlace::seller_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('seller_');

        $model = $this->_coreRegistry->registry('lof_marketplace_seller');

        $fieldset = $form->addFieldset(
            'meta_fieldset',
            ['legend' => __('Social Infomation'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'tw_active',
            'checkbox',
            [
                'name' => 'tw_active',
                'checked' => $model->getData('tw_active'),
                'label' => __('Twitter Active'),
                'title' => __('Twitter Active'),
                'data-form-part' => $this->getData('tw_active'),
                'onchange' => 'this.value = this.checked;',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'twitter_id',
            'text',
            [
                'name' => 'twitter_id',
                'label' => __('Twitter'),
                'title' => __('Twitter'),
                'disabled' => $isElementDisabled
            ]
        );

         $fieldset->addField(
            'fb_active',
            'checkbox',
            [
                'name' => 'fb_active',
                'checked' => $model->getData('fb_active'),
                'label' => __('Facebook Active'),
                'title' => __('Facebook Active'),
                'data-form-part' => $this->getData('fb_active'),
                'onchange' => 'this.value = this.checked;',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'facebook_id',
            'text',
            [
                'name' => 'facebook_id',
                'label' => __('Facebook'),
                'title' => __('Facebook'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'gplus_active',
            'checkbox',
            [
                'name' => 'gplus_active',
                'checked' => $model->getData('gplus_active'),
                'label' => __('Google Plus Active'),
                'title' => __('Google Plus Active'),
                'data-form-part' => $this->getData('gplus_active'),
                'onchange' => 'this.value = this.checked;',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'gplus_id',
            'text',
            [
                'name' => 'gplus_id',
                'label' => __('Google Plus'),
                'title' => __('Google Plus'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'youtube_active',
            'checkbox',
            [
                'name' => 'youtube_active',
                'checked' => $model->getData('youtube_active'),
                'label' => __('Youtube Active'),
                'title' => __('Youtube Active'),
                'data-form-part' => $this->getData('youtube_active'),
                'onchange' => 'this.value = this.checked;',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'youtube_id',
            'text',
            [
                'name' => 'youtube_id',
                'label' => __('Youtube'),
                'title' => __('Youtube'),
                'disabled' => $isElementDisabled
            ]
        );

         $fieldset->addField(
            'vimeo_active',
            'checkbox',
            [
                'name' => 'vimeo_active',
                'checked' => $model->getData('vimeo_active'),
                'label' => __('Vimeo Active'),
                'title' => __('Vimeo Active'),
                'data-form-part' => $this->getData('vimeo_active'),
                'onchange' => 'this.value = this.checked;',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'vimeo_id',
            'text',
            [
                'name' => 'vimeo_id',
                'label' => __('Vimeo'),
                'title' => __('Vimeo'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'linkedin_active',
            'checkbox',
            [
                'name' => 'linkedin_active',
                'checked' => $model->getData('linkedin_active'),
                'label' => __('Linkedin Active'),
                'title' => __('Linkedin Active'),
                'data-form-part' => $this->getData('linkedin_active'),
                'onchange' => 'this.value = this.checked;',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'linkedin_id',
            'text',
            [
                'name' => 'linkedin_id',
                'label' => __('Linkedin'),
                'title' => __('Linkedin'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'instagram_active',
            'checkbox',
            [
                'name' => 'instagram_active',
                'checked' => $model->getData('instagram_active'),
                'label' => __('Instagram Active'),
                'title' => __('Instagram Active'),
                'data-form-part' => $this->getData('instagram_active'),
                'onchange' => 'this.value = this.checked;',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'instagram_id',
            'text',
            [
                'name' => 'instagram_id',
                'label' => __('Instagram'),
                'title' => __('Instagram'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'pinterest_active',
            'checkbox',
            [
                'name' => 'pinterest_active',
                'checked' => $model->getData('pinterest_active'),
                'label' => __('Pinterest Active'),
                'title' => __('Pinterest Active'),
                'data-form-part' => $this->getData('pinterest_active'),
                'onchange' => 'this.value = this.checked;',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'pinterest_id',
            'text',
            [
                'name' => 'pinterest_id',
                'label' => __('Pinterest'),
                'title' => __('Pinterest'),
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
        return __('Social Infomation');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Social Infomation');
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
