<?php
namespace Asm\Kiranaproducts\Block\Adminhtml\Kiranaproducts;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Asm\Kiranaproducts\Model\kiranaproductsFactory
     */
    protected $_kiranaproductsFactory;

    /**
     * @var \Asm\Kiranaproducts\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Asm\Kiranaproducts\Model\kiranaproductsFactory $kiranaproductsFactory
     * @param \Asm\Kiranaproducts\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Asm\Kiranaproducts\Model\KiranaproductsFactory $KiranaproductsFactory,
        \Asm\Kiranaproducts\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_kiranaproductsFactory = $KiranaproductsFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_kiranaproductsFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('entity_id'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'product_id',
            [
                'header' => __('product_id'),
                'type' => 'number',
                'index' => 'product_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'seller_id',
            [
                'header' => __('seller_id'),
                'type' => 'number',
                'index' => 'seller_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'product_name',
            [
                'header' => __('product_name'),
                'type' => 'text',
                'index' => 'product_name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('name'),
                'type' => 'text',
                'index' => 'name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'doorstep_price',
            [
                'header' => __('doorstep_price'),
                'type' => 'text',
                'index' => 'doorstep_price',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'pickup_from_store',
            [
                'header' => __('pickup_from_store'),
                'type' => 'text',
                'index' => 'pickup_from_store',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'mrp',
            [
                'header' => __('mrp'),
                'type' => 'text',
                'index' => 'mrp',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		
		   $this->addExportType($this->getUrl('kiranaproducts/*/exportCsv', ['_current' => true]),__('CSV'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('entity_id');
        //$this->getMassactionBlock()->setTemplate('Asm_Kiranaproducts::kiranaproducts/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('kiranaproducts');

        // $this->getMassactionBlock()->addItem(
        //     'delete',
        //     [
        //         'label' => __('Delete'),
        //         'url' => $this->getUrl('kiranaproducts/*/massDelete'),
        //         'confirm' => __('Are you sure?')
        //     ]
        // );

        // $statuses = $this->_status->getOptionArray();

        // $this->getMassactionBlock()->addItem(
        //     'status',
        //     [
        //         'label' => __('Change status'),
        //         'url' => $this->getUrl('kiranaproducts/*/massStatus', ['_current' => true]),
        //         'additional' => [
        //             'visibility' => [
        //                 'name' => 'status',
        //                 'type' => 'select',
        //                 'class' => 'required-entry',
        //                 'label' => __('Status'),
        //                 'values' => $statuses
        //             ]
        //         ]
        //     ]
        // );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('kiranaproducts/*/index', ['_current' => true]);
    }

    /**
     * @param \Asm\Kiranaproducts\Model\kiranaproducts|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		return '#';
    }

	

}