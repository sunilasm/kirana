<?php
namespace Retailinsights\Promotion\Model\Import;
use Retailinsights\Promotion\Model\Import\CustomImport\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Retailinsights\Promotion\Model\PostFactory;
use Retailinsights\Promotion\Model\PostTableFactory;
use Retailinsights\Promotion\Model\PostSellerFactory;

class CustomImport extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
   
    const STORE = 'rule_id';
    const RULE = 'store_id';
    const STATUS = 'status';
    const SDATE = 'pstart_date';
    const EDATE = 'pend_date';
    const SELLER_NAME = 'seller_name';
    const SELLER_TYPE = 'seller_type';
    const TYPE = 'rule_type';
    const DESCRIPTION = 'description';
    const CONDITION = 'conditions_serialized';
    const ACTION = 'actions_serialized';
    const SIMPLE_ACTION = 'simple_action';
    const DISCOUNT = 'discount_amount';
    const SELLER = 'discount_amount';
    
   
    const TABLE_ENTITY = 'retailinsights_promostoremapp';

    protected $rule;
    protected $PostFactory;
    protected $PostTableFactory;
    protected $PostSellerFactory;
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_ID_IS_EMPTY => 'Empty',
    ];
 
    protected $_permanentAttributes = [self::STORE];
     
    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;
     
    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        self::STORE,
        self::RULE,
        self::STATUS,
        self::SDATE,
        self::EDATE,

        self::SELLER_NAME,
        self::SELLER_TYPE,
        
        self::TYPE,
        self::DESCRIPTION,
        self::CONDITION,
        self::ACTION,
        self::SIMPLE_ACTION,
        self::DISCOUNT
       
    ];

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;
     
    protected $_validators = [];
     
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_connection;
     
    protected $_resource;
     
    /**
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        PostFactory $PostFactory,
        PostTableFactory $PostTableFactory,
        PostSellerFactory $PostSellerFactory,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\CatalogRule\Model\RuleFactory $rule
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->PostTableFactory = $PostTableFactory;
        $this->PostSellerFactory = $PostSellerFactory;
        $this->PostFactory = $PostFactory;
        $this->rule = $rule;
    }
 
    public function getValidColumnNames() {
        return $this->validColumnNames;
    }
 
    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode() {
        return 'faq';
    }
 
    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum) {
    

        $title = false;
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
         
        $this->_validatedRows[$rowNum] = true;
       
        if (!isset($rowData[self::STORE]) || empty($rowData[self::STORE])) {
            $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
            return false;
        }
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }
 
    /**
     * Create advanced question data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    protected function _importData() {

        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }
        return true;
    }
 
    /**
     * Save question
     *
     * @return $this
     */
    public function saveEntity() {
        $this->saveAndReplaceEntity();
        return $this;
    }
 
    /**
     * Replace question
     *
     * @return $this
     */
    public function replaceEntity() {
        $this->saveAndReplaceEntity();
        return $this;
    }
 
    /**
     * Deletes question data from raw data.
     *
     * @return $this
     */
    public function deleteEntity() {
        $ids = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowId = $rowData[self::STORE];
                    $ids[] = $rowId;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($ids) {
            $this->deleteEntityFinish(array_unique($ids),self::TABLE_ENTITY);
        }
        return $this;
    }
 
    /**
     * Save and replace question
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceEntity() {
        $behavior = $this->getBehavior();
        $ids = [];

        
        $collectionSeller= $this->PostSellerFactory->create()->getCollection();
        $seller_data = $collectionSeller->getData();

       $collection= $this->rule->create()->getCollection();
       $catalog_data = $collection->getData();

       
       $rule_data = $collection->getData();


        $rule_info = array();
        $rule = array();

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {        
            $rule_id = 0;
            

            $description=0;
            $conditions_serialized =0;
            $actions_serialized =0;
            $simple_action =0;
            $discount_amount =0;
            $seller_name=0;

            $entityList = [];

          

            foreach ($bunch as $rowNum => $rowData) { 
                       
                        foreach($rule_data as $cat){
                            if($rowData['rule_id']== $cat['name']){
                            $rule_id=$cat['rule_id'];
                            }

                            foreach($catalog_data as $cat_l){
                                
                                if($rule_id == $cat_l['rule_id']){
                                    $description = $cat_l['description'];
                                    $conditions_serialized= $cat_l['conditions_serialized'];
                                    $actions_serialized = $cat_l['actions_serialized'];
                                    $simple_action = $cat_l['simple_action'];
                                    $discount_amount = $cat_l['discount_amount'];
                                }
                            }
                            foreach($seller_data as $seller){
                                

                                if($rowData['store_id'] == $seller['seller_id']){
                                    $seller_name = $seller['name'];
                                   
                                }
                            }
                        }

                        
            
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
              

                $rowId= $rule_id; //
                $ids[] = $rowId;
                $entityList[$rowId][] = [
                   

                  self::STORE => $rule_id,
                  self::RULE => $rowData[self::RULE],
                  self::SDATE => $rowData[self::SDATE],
                  self::EDATE => $rowData[self::EDATE],
                  self::STATUS => $rowData[self::STATUS],

                 self::SELLER_NAME => $seller_name,
                 self::SELLER_TYPE => $rowData[self::SELLER_TYPE],
    
                self::TYPE => $rowData[self::TYPE],
                  self::DESCRIPTION=> $description,
                  self::CONDITION => $conditions_serialized,
                  self::ACTION => $actions_serialized,
                  self::SIMPLE_ACTION => $simple_action,
                  self::DISCOUNT => $discount_amount,  
                 
                ];
            }
           

            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($ids) {
                    if ($this->deleteEntityFinish(array_unique($ids), self::TABLE_ENTITY)) {
                        $this->saveEntityFinish($entityList, self::TABLE_ENTITY);
                        
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveEntityFinish($entityList, self::TABLE_ENTITY);
            }
        }
        return $this;
    }
 
    /**
     * Save question
     *
     * @param array $priceData
     * @param string $table
     * @return $this
     */
    protected function saveEntityFinish(array $entityData, $table) {
      
        if ($entityData) {
          
            $tableName = $this->_connection->getTableName($table);
            $entityIn = [];
            $entityInup = [];
            $i=20;

            foreach ($entityData as $entityRows) {
            $collection= $this->PostTableFactory->create();
            $item = $collection->getCollection();
            
            $rule_id = $entityRows[0]['rule_id'];
            $store_id = $entityRows[0]['store_id'];

            $value = $item->addFieldToFilter('rule_id', $rule_id)
            ->addFieldToFilter('store_id', $store_id);
            
           $match_data = $value->getData();
          
               if($match_data){
                  
                   $collection->setPId($match_data[0]['p_id']);
                   $collection->setRuleId($entityRows[0]['rule_id']);
                   $collection->setStoreId($entityRows[0]['store_id']);
                   $collection->setPstartDate($entityRows[0]['pstart_date']);
                   $collection->setPendDate($entityRows[0]['pend_date']);

                   $collection->setSellerName($entityRows[0]['seller_name']);
                   $collection->setSellerType($entityRows[0]['seller_type']);

                   $collection->setStatus($entityRows[0]['status']);
                   $collection->setRuleType($entityRows[0]['rule_type']); 
                   $collection->setDescription($entityRows[0]['description']);
                   $collection->setConditionsSerialized($entityRows[0]['conditions_serialized']);
                   $collection->setActionsSerialized($entityRows[0]['actions_serialized']);
                   $collection->setSimpleAction($entityRows[0]['simple_action']);
                   $collection->setDiscountAmount($entityRows[0]['discount_amount']);
                   $collection->save();
            
               }else{
                  
                   $collection->setRuleId($entityRows[0]['rule_id']);
                   $collection->setStoreId($entityRows[0]['store_id']);
                   $collection->setPstartDate($entityRows[0]['pstart_date']);
                   $collection->setPendDate($entityRows[0]['pend_date']);

                   $collection->setSellerName($entityRows[0]['seller_name']);
                   $collection->setSellerType($entityRows[0]['seller_type']);

                   $collection->setStatus($entityRows[0]['status']);
                   $collection->setRuleType($entityRows[0]['rule_type']);
                   $collection->setDescription($entityRows[0]['description']);
                   $collection->setConditionsSerialized($entityRows[0]['conditions_serialized']);
                   $collection->setActionsSerialized($entityRows[0]['actions_serialized']);
                   $collection->setSimpleAction($entityRows[0]['simple_action']);
                   $collection->setDiscountAmount($entityRows[0]['discount_amount']);
                   $collection->save();
               }        
            }
        }
        return $this;
    }
    protected function deleteEntityFinish(array $ids, $table) {
 
        if ($table && $ids) {
            try {
                $this->countItemsDeleted += $this->_connection->delete(
                    $this->_connection->getTableName($table),
                    $this->_connection->quoteInto('rule_id IN (?)', $ids)
                );
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }
}