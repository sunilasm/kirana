<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * 
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\MarketPlace\Block\Adminhtml\Commission\Edit\Tab;

class Conditions extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{ 
     /**
     * @var \Magento\Store\Model\System\Store
     */
       /**
     * @var \Magento\Store\Model\System\Store
     */
       protected $systemStore;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $objectConverter;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;


    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\Collection
     */
    protected $orderStatusCollection;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory
     */
    protected $rendererFieldsetFactory;

    /**
     * @var \Lof\Followupcommission\Model\Event\Config
     */
    protected $eventConfig; 

    protected $commissionModel; 

    protected $_typeFactory; 

    protected $_ruleActions;
    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context                       $context               
     * @param \Magento\Framework\Registry                                   $registry              
     * @param \Magento\Framework\Data\FormFactory                           $formFactory            
     * @param \Magento\Customer\Api\GroupRepositoryInterface                $groupRepository       
     * @param SearchCriteriaBuilder                                         $searchCriteriaBuilder 
     * @param \Magento\Store\Model\System\Store                             $systemStore           
     * @param \Magento\Hideprice\Model\ResourceModel\Template\CollectionFactory $templatesFactory      
     * @param \Magento\Hideprice\Model\Template\Config                          $commissionConfig                 
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository, 
        \Magento\Catalog\Model\Product\TypeFactory $typeFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $orderStatusCollectionFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Rule\Block\Actions $ruleActions,
        \Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory $rendererFieldsetFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Convert\DataObject $objectConverter, 
        array $data = []
        ) {
        $this->_typeFactory = $typeFactory;
        $this->systemStore = $systemStore;
        $this->objectConverter = $objectConverter;
        $this->groupRepository = $groupRepository; 
        $this->orderStatusCollection = $orderStatusCollectionFactory->create();
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->conditions = $conditions;
        $this->_ruleActions = $ruleActions;
        $this->rendererFieldsetFactory = $rendererFieldsetFactory; 
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lof_marketplace_commission'); 
        if ($this->_isAllowedAction('Lof_MarketPlace::commission_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('commission_'); 

        $this->addProductConditions($form,$model);
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    } 

   

    protected function addProductConditions(\Magento\Framework\Data\Form $form, \Lof\MarketPlace\Model\Commission $model, $fieldsetId = 'actions_fieldset', $formName = 'edit_form') {  
        // commission_product_conditions_fieldset
        $id = $this->getRequest()->getParam('id');
        if (!$model) {   
            $model = $this->getRuleFactory()->create();
            $model->load($id);
        }


        $newChildUrl = $this->getUrl(
        'sales_rule/promo_quote/newActionHtml/form/commission_actions_fieldset',
        ['form_namespace' => $formName]
        );

        $renderer = $this->rendererFieldsetFactory->create()->setTemplate(
        'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
        $newChildUrl
        );
        
        $fieldset = $form->addFieldset(
            $fieldsetId,
            [
            'legend' => __(
                'Conditions (don\'t add conditions if rule is applied to all products)'
                )
            ]
            )->setRenderer(
            $renderer
            );
          
            $fieldset->addField(
                'actions',
                'text',
                [
                'name'           => 'apply_to',
                'label'          => __('Apply To'),
                'title'          => __('Apply To'),
                'required'       => true,
                'data-form-part' => $formName
                ]
            )->setRule(
                $model
            )->setRenderer(
                $this->_ruleActions
        );               

    }
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Conditions');
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
