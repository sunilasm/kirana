<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Psr\Log\LoggerInterface as Logger;

/**
 * App page collection
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager
    ) {
        $mainTable = 'lof_marketplace_sellerinvoice';
        $resourceModel = 'Magento\Sales\Model\ResourceModel\Order\Invoice';
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $fields = [
            'status',
            'grand_total',
            'base_grand_total',
            'seller_id'
        ];
        foreach($fields as $field){
            $this->addFilterToMap(
                $field,
                'main_table.'.$field
            );
        }
    }
    
    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        
        parent::_initSelect();
        $this->join([
            'invoice_grid'=>$this->getTable('sales_invoice_grid')],
            'main_table.invoice_id=invoice_grid.entity_id',
            [
                'increment_id',
                'order_increment_id',
                'store_id',
                'customer_name',
                'billing_name',
                'billing_address',
                'shipping_address',
                'store_currency_code',
                'order_currency_code',
                'base_currency_code',
                'global_currency_code',
                'created_at'
            ]);
        
        return $this;
    }
}
