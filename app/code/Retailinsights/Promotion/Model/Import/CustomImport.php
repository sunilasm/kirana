<?php

namespace Retailinsights\Promotion\Model\Import;

use Retailinsights\Promotion\Model\Import\CustomImport\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Retailinsights\Promotion\Model\PostFactory;
use Retailinsights\Promotion\Model\PostTableFactory;
use Retailinsights\Promotion\Model\PostSellerFactory;
use Retailinsights\Promotion\Model\PostBWGYFactory;

use Retailinsights\Promotion\Model\PostWorthFactory;
use Retailinsights\Promotion\Model\PostXYZFactory;
use Retailinsights\Promotion\Model\PostXYZoffFactory;
use Retailinsights\Promotion\Model\PostByxFactory;

class CustomImport extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const STORE = 'rule';
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
    private $salesrule;
    protected $PostFactory;
    protected $PostTableFactory;
    protected $PostSellerFactory;
    protected $PostWorthFactory;
    protected $PostBWGYFactory;
    protected $PostXYZFactory;
    protected $PostXYZoffFactory;
    protected $PostByxFactory;
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
        \Retailinsights\Promotion\Model\PostTableBackFactory $PostTableBackFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        PostFactory $PostFactory,
        PostTableFactory $PostTableFactory,
        PostSellerFactory $PostSellerFactory,
        PostWorthFactory $PostWorthFactory,
        PostBWGYFactory $PostBWGYFactory,
        PostXYZFactory $PostXYZFactory,
        PostXYZoffFactory $PostXYZoffFactory,
        PostByxFactory $PostByxFactory,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\CatalogRule\Model\RuleFactory $rule,
        \Magento\SalesRule\Model\RuleFactory $salesrule
    ) {
        $this->_savedata=$PostTableBackFactory;
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
        $this->salesrule = $salesrule;
        $this->PostWorthFactory = $PostWorthFactory->create();
        $this->PostBWGYFactory = $PostBWGYFactory->create();
        $this->PostXYZFactory = $PostXYZFactory->create();
        $this->PostXYZoffFactory = $PostXYZoffFactory->create();
        $this->PostByxFactory = $PostByxFactory->create();

        $this->Posttemp = $PostTableFactory->create();
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
        

        $collectionCustom= $this->PostWorthFactory->getCollection(); //custom worth information
        $custom_worth = $collectionCustom->getData();

        $collectionCustom= $this->PostBWGYFactory->getCollection(); //custom worth information
        $custom_BWGY = $collectionCustom->getData();
       
        $collectionCustom= $this->PostXYZFactory->getCollection(); //custom XYZ information
        $custom_XYZ = $collectionCustom->getData(); 

        
        $collectionCustom= $this->PostXYZoffFactory->getCollection(); //custom XYZ OFFinformation
        $custom_XYZoff = $collectionCustom->getData(); 

        $collectionCustom= $this->PostByxFactory->getCollection(); //custom BYx information
        $custom_byx = $collectionCustom->getData(); 
                 
        $collectionSeller= $this->PostSellerFactory->create()->getCollection(); //seller information
        $seller_data = $collectionSeller->getData();
      
        $collection= $this->rule->create()->getCollection(); //catalog rule collection
        $catalog_data = $collection->getData();

        $sales_collection = $this->salesrule->create()->getCollection(); //salesRules collection
        $sales = $sales_collection->getData();
      
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
            $flag = 0;

            foreach ($bunch as $rowNum => $rowData) {        //$rowData CSV information              
                if($rowData['rule_type'] == 1){         //catalog rule adding
                        foreach($rule_data as $cat){
                            if($rowData['rule']== $cat['name']){
                                $rule_id=$cat['rule_id'];
                           $flag=1;

                            foreach($catalog_data as $cat_l){
                                if($rule_id == $cat_l['rule_id']){
                                    $json = explode(":",$cat_l['name']);
                                    $desc = '{';
                                    $desc .= '"code":"'.$json[0].'","name":"'.$json[1].'"';
                                    $desc .= '}';
                                    
                                    $description = $desc;
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
                        }
if($flag==1){
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
                $entityList[] = [
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
            }
                if($rowData['rule_type'] == 0){     //sales rule adding
                    foreach($sales as $cat){
                        if($rowData['rule']== $cat['name']){
                        $rule_id=$cat['rule_id'];
                        $flag=1;
                        

                        foreach($sales as $cat_l){
                            if($rule_id == $cat_l['rule_id']){
                                $json = explode(":",$cat_l['name']); 
                            
                                $desc = '{';
                                $desc .= '"code":"'.$json[0].'","name":"'.$json[1].'"';
                                $desc .= '}';
                                
                                $description = $desc;
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
                }
                if($flag==1){
            if (!$this->validateRow($rowData, $rowNum)) {
                $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
                continue;
            }
            if ($this->getErrorAggregator()->hasToBeTerminated()) {
                $this->getErrorAggregator()->addRowToSkip($rowNum);
                continue;
            }

            $rowId= $rule_id;
            $ids[] = $rowId;
            $entityList[] = [
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
            }
            if($rowData['rule_type'] == 4){    //CUSTOM  WORTH XXX COLLECTION
                foreach($custom_worth as $cat){
                    if($rowData['rule']== $cat['name']){
                        $rule_id = $cat['post_id']; 
                        $flag=1;  
                    

                    foreach($custom_worth as $cat_l){
                        if($rule_id == $cat_l['post_id']){
                            $json = explode(":",$cat_l['name']); 
                            
                            $desc = '{';
                            $desc .= '"code":"'.$json[0].'","name":"'.$json[1].'"';
                            $desc .= '}';
                            
                            $description = $desc;
                            $conditions_serialized= 'equals';

                            $action = '{';          
                            $action .= '"buy_quantity":[{"qty":"'.$cat_l["quantity"].'"}],"get_product":[{"sku" :"'.$cat_l["get_product"].'"}],"subtotal":"'.$cat_l["subtotal"].'"';
                            $action .="}";
                            
                            $actions_serialized = $action;
                            $simple_action = 'free_product';
                            $discount_amount = '';
                        }
                    }
                    foreach($seller_data as $seller){
                        if($rowData['store_id'] == $seller['seller_id']){
                            $seller_name = $seller['name'];
                        }
                    }
                }
            }
            if($flag==1){
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
            
                $rowId= $rule_id;
                $ids[] = $rowId;
                $entityList[] = [
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
            } 
            if($rowData['rule_type'] == 5){    //CUSTOM XYZ COLLECTION BNG1O 
                foreach($custom_XYZ as $cat){
                    if($rowData['rule']== $cat['name']){
                        $rule_id = $cat['post_id'];   
                        $flag=1;
                    
                    
                    foreach($custom_XYZ as $cat_l){     
                                            
                        if($rule_id == $cat_l['post_id']){
                            $json = explode(":",$cat_l['name']); 

                          
                            $desc = '{';
                            $desc .= '"code":"'.$json[0].'","name":"'.$json[1].'"';
                            $desc .= '}';
                            $description = $desc;
                            
                            $conditions_serialized= 'equals';
                            $action = $cat_l['rule_condition'];
                            $action2 ='{"discount_product":[{"sku":"'.$cat_l['discount_product'].'","discount_product":"'.$cat_l['discount'].'"}]}';

                            $json_sum1 = json_encode(array_merge(json_decode($action, true),json_decode($action2, true)));

                            $actions_serialized = $json_sum1; 
                            $simple_action = 'final_price';
                            $discount_amount = $cat_l['discount'];
                        }
                    }
                    foreach($seller_data as $seller){
                        if($rowData['store_id'] == $seller['seller_id']){
                            $seller_name = $seller['name'];
                        }
                    }
                }
                }
if($flag==1){
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $rowId= $rule_id;
                $ids[] = $rowId;
                $entityList[] = [
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
            }

            if($rowData['rule_type'] == 8){    //CUSTOM XYZ OFF COLLECTION
               
                foreach($custom_XYZoff as $cat){
                    if($rowData['rule']== $cat['name']){
                        $rule_id = $cat['post_id'];   
                        $flag=1;
                    

                    foreach($custom_XYZoff as $cat_l){  
                       

                        if($rule_id == $cat_l['post_id']){
                            $json = explode(":",$cat_l['name']); 
                            
                            $desc = '{';
                            $desc .= '"code":"'.$json[0].'","name":"'.$json[1].'"';
                            $desc .= '}';
                            $description = $desc;
                            
                            $conditions_serialized= 'equals';
                            $action3 = $cat_l['rule_condition'];
                            $action4 ='{
                                "fixed_price": [{
                                    "fixed_price": "'.$cat_l['fixed_price'].'"
                                }]
                            }';

                            $json_sum = json_encode(array_merge(json_decode($action3, true),json_decode($action4, true)));

                            $actions_serialized = $json_sum; 
                            $simple_action = 'fixed_price';
                            $discount_amount = $cat_l['fixed_price'];
                        }
                    
                    }
                    foreach($seller_data as $seller){
                        if($rowData['store_id'] == $seller['seller_id']){
                            $seller_name = $seller['name'];
                        }
                    }
                }
            }
            if($flag==1){
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $rowId= $rule_id;
                $ids[] = $rowId;
                $entityList[] = [
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
            }

         
            if($rowData['rule_type'] == 7){    //CUSTOM BXGY COLLECTION    
                foreach($custom_byx as $cat){
                    if($rowData['rule']== $cat['name']){
                        $rule_id = $cat['post_id'];
                        $flag=1;
                       

                    foreach($custom_byx as $cat_l){
                            if($rule_id == $cat_l['post_id']){
                            $json = explode(":",$cat_l['name']);
                            
                            $desc = '{';
                            $desc .= '"code":"'.$json[0].'","name":"'.$json[1].'"';
                            $desc .= '}';

                            $description = $desc;
                            $conditions_serialized= 'equals';
                            $action = '{';          
                            $action .= '"buy_product":[{"sku" : "'.$cat_l["buy_product"].'","qty" : "'.$cat_l["buy_quantity"].'"}],"get_product":[{"sku" : "'.$cat_l["get_product"].'","qty" : "'.$cat_l["get_quantity"].'" }]';
                            $action .="}";
    
                            $actions_serialized = $action;
                            $simple_action = 'free';
                            $discount_amount = '';
                        }
                    }
                    foreach($seller_data as $seller){ 
                        if($rowData['store_id'] == $seller['seller_id']){
                            $seller_name = $seller['name'];
                        }
                    }
                }
            }
            if($flag==1){
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
            
                $rowId= $rule_id;
                $ids[] = $rowId;
                $entityList[] = [
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
            }

            
            if($rowData['rule_type'] == 9){    //CUSTOM BWGY COLLECTION  
                foreach($custom_BWGY as $cat){
                    if($rowData['rule']== $cat['name']){
                        $rule_id = $cat['post_id'];
                        $flag=1;
                       

                    foreach($custom_BWGY as $cat_l){
                      
                            if($rule_id == $cat_l['post_id']){
                            $json = explode(":",$cat_l['name']);
                            
                            $desc = '{';
                            $desc .= '"code":"'.$json[0].'","name":"'.$json[1].'"';
                            $desc .= '}';

                            $description = $desc;
                            $conditions_serialized= 'equals';
                            $action = '{';          
                            $action .= '"base_subtotal":[{"fixed_amount" : "'.$cat_l["fixed_amount"].'","operator" : "'.$cat_l["condition"].'"}],"get_product":[{"sku" : "'.$cat_l["get_product"].'","qty" : "'.$cat_l["get_quantity"].'" }]';
                            $action .="}";
    
                            $actions_serialized = $action;
                            $simple_action = 'free';
                            $discount_amount = '';
                        }
                    }
                    foreach($seller_data as $seller){ 
                        if($rowData['store_id'] == $seller['seller_id']){
                            $seller_name = $seller['name'];
                        }
                    }
                }
            }
            if($flag==1){
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
            
                $rowId= $rule_id;
                $ids[] = $rowId;
                $entityList[] = [
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
            }
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
            $rule_id = $entityRows['rule'];
            $rule_type = $entityRows['rule_type'];
           
            $value = $item
            ->addFieldToFilter('rule', $rule_id)
            ->addFieldToFilter('rule_type', $rule_type);
            
            $match_data = $value->getData();
          
               if($match_data){    
                   
                 //**** ADDING To Back up Table*** */
                 if($match_data[0]['rule_type']==1 && $match_data[0]['status']==1){
                    $mandeetotcol = $this->_savedata->create();

                    $mandeetotcol->setRule($match_data[0]['rule']);
                    $mandeetotcol->setStoreId($match_data[0]['store_id']);
                    $mandeetotcol->setPstartDate($match_data[0]['pstart_date']);
                    $mandeetotcol->setPendDate($match_data[0]['pend_date']);
                    $mandeetotcol->setStoreName($match_data[0]['store_name']); 
                    $mandeetotcol->setSellerType($match_data[0]['seller_type']);
                    $mandeetotcol->setStatus($match_data[0]['status']);
                    $mandeetotcol->setRuleType($match_data[0]['rule_type']); 
                    $mandeetotcol->setDescription($match_data[0]['description']);
                    $mandeetotcol->setConditionsSerialized($match_data[0]['conditions_serialized']);
                    $mandeetotcol->setActionsSerialized($match_data[0]['actions_serialized']);
                    $mandeetotcol->setSimpleAction($match_data[0]['simple_action']);
                    $mandeetotcol->setDiscountAmount($match_data[0]['discount_amount']);
                       
                    $mandeetotcol->save();
                   
                }
                //*********END */
                   $collection->setPId($match_data[0]['p_id']);
                   $collection->setRule($entityRows['rule']);
                   $collection->setStoreId($entityRows['store_id']);
                   $collection->setPstartDate($entityRows['pstart_date']);
                   $collection->setPendDate($entityRows['pend_date']);
                   $collection->setSellerName($entityRows['seller_name']);
                   $collection->setSellerType($entityRows['seller_type']);
                   $collection->setStatus($entityRows['status']);
                   $collection->setRuleType($entityRows['rule_type']); 
                   $collection->setDescription($entityRows['description']);
                   $collection->setConditionsSerialized($entityRows['conditions_serialized']);
                   $collection->setActionsSerialized($entityRows['actions_serialized']);
                   $collection->setSimpleAction($entityRows['simple_action']);
                   $collection->setDiscountAmount($entityRows['discount_amount']);
                   $collection->save();
               }else{
                   $collection->setRule($entityRows['rule']);
                   $collection->setStoreId($entityRows['store_id']);
                   $collection->setPstartDate($entityRows['pstart_date']);
                   $collection->setPendDate($entityRows['pend_date']);
                   $collection->setSellerName($entityRows['seller_name']);
                   $collection->setSellerType($entityRows['seller_type']);
                   $collection->setStatus($entityRows['status']);
                   $collection->setRuleType($entityRows['rule_type']);
                   $collection->setDescription($entityRows['description']);
                   $collection->setConditionsSerialized($entityRows['conditions_serialized']);
                   $collection->setActionsSerialized($entityRows['actions_serialized']);
                   $collection->setSimpleAction($entityRows['simple_action']);
                   $collection->setDiscountAmount($entityRows['discount_amount']);
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
                    $this->_connection->quoteInto('rule IN (?)', $ids)
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