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
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Block\Adminhtml\Group\Edit\Tab;

/**
 * Customer account form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Option extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        if ($this->_isAllowedAction('Lof_MarketPlace::group_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('group_');

        $model = $this->_coreRegistry->registry('lof_marketplace_seller');

        $fieldset = $form->addFieldset(
            'meta_fieldset',
            ['legend' => __('Option Data'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'can_add_product',
            'select',
            [
                'label' => __('Can Add New Product'),
                'title' => __('Can Add New Product'),
                'name' => 'can_add_product',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
        $fieldset->addField(
            'can_cancel_order',
            'select',
            [
                'label' => __('Can Cancel Order'),
                'title' => __('Can Cancel Order'),
                'name' => 'can_cancel_order',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
        $fieldset->addField(
            'can_create_invoice',
            'select',
            [
                'label' => __('Can Create Invoice'),
                'title' => __('Can Create Invoice'),
                'name' => 'can_create_invoice',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
         $fieldset->addField(
            'can_create_shipment',
            'select',
            [
                'label' => __('Can Create Shipment'),
                'title' => __('Can Create Shipment'),
                'name' => 'can_create_shipment',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
          $fieldset->addField(
            'can_create_creditmemo',
            'select',
            [
                'label' => __('Can Create Credit Memo'),
                'title' => __('Can Create Credit Memo'),
                'name' => 'can_create_creditmemo',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
           $fieldset->addField(
            'hide_payment_info',
            'select',
            [
                'label' => __('Hide Payment Information'),
                'title' => __('Hide Payment Information'),
                'name' => 'hide_payment_info',
                'note' => __('The payment information of customer will be hidden from order.'),
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
            $fieldset->addField(
            'can_submit_order_comments',
            'select',
            [
                'label' => __('Can submit comments'),
                'title' => __('Can submit comments'),
                'name' => 'can_submit_order_comments',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
             $fieldset->addField(
            'can_use_message',
            'select',
            [
                'label' => __('Can Use Message'),
                'title' => __('Can Use Message'),
                'name' => 'can_use_message',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
              $fieldset->addField(
            'can_use_shipping',
            'select',
            [
                'label' => __('Can Use Shipping'),
                'title' => __('Can Use Shipping'),
                'name' => 'can_use_shipping',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
               $fieldset->addField(
            'can_use_review',
            'select',
            [
                'label' => __('Can Use Review'),
                'title' => __('Can Use Review'),
                'name' => 'can_use_review',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
                $fieldset->addField(
            'can_use_rating',
            'select',
            [
                'label' => __('Can Use Rating'),
                'title' => __('Can Use Rating'),
                'name' => 'can_use_rating',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
                 $fieldset->addField(
            'can_use_import_export',
            'select',
            [
                'label' => __('Can Import/Export Product'),
                'title' => __('Can Import/Export Product'),
                'name' => 'can_use_import_export',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
                  $fieldset->addField(
            'can_use_vacation',
            'select',
            [
                'label' => __('Can Use Vacation'),
                'title' => __('Can Use Vacation'),
                'name' => 'can_use_vacation',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        ); 
        $fieldset->addField(
            'can_use_report',
            'select',
            [
                'label' => __('Can Use Report'),
                'title' => __('Can Use Report'),
                'name' => 'can_use_report',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
         $fieldset->addField(
            'can_use_withdrawal',
            'select',
            [
                'label' => __('Can Use Withdrawal'),
                'title' => __('Can Use Withdrawal'),
                'name' => 'can_use_withdrawal',
                'options' => ['1' => __('Yes'), '0' => __('No')]
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
        return __('Option Data');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Option Data');
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
