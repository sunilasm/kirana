<?php
namespace Asm\Kiranaproducts\Model\Import;
use Asm\Kiranaproducts\Model\Import\CustomImport\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
class CustomImport extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const ID = 'entity_id';
    const PRODUCTID = 'product_id';
    //const ADMINASSING = 'adminassign';
    const SELLERID = 'seller_id';
    const PRODUCTNAME = 'product_name';
    //const STOREID = 'store_id';
    //const POSITION = 'position';
    //const STATUS = 'status';
    //const CREATEDAT = 'created_at';
    //const UPDATEDAT = 'updated_at';
    //const CUSTOMERID = 'customer_id';
    //const COMMISSION = 'commission';
    const NAME = 'name';
    const DOORSETP = 'doorstep_price';
    const PICKFSTORE = 'pickup_from_store';
    const MRP = 'mrp';
    const TABLE_Entity = 'mglof_marketplace_product';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
    ValidatorInterface::ERROR_MESSAGE_IS_EMPTY => 'Message is empty',
    ];
     protected $_permanentAttributes = [self::ID];
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
    self::ID,
    self::PRODUCTID,
    // self::ADMINASSING,
    self::SELLERID,
    self::PRODUCTNAME,
    // self::STOREID,
    // self::POSITION,
    // self::STATUS,
    // self::CREATEDAT,
    // self::UPDATEDAT,
    // self::CUSTOMERID,
    // self::COMMISSION,
    self::NAME,
    self::DOORSETP,
    self::PICKFSTORE,
    self::MRP,
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
    ProcessingErrorAggregatorInterface $errorAggregator
    ) {
    $this->jsonHelper = $jsonHelper;
    $this->_importExportData = $importExportData;
    $this->_resourceHelper = $resourceHelper;
    $this->_dataSourceModel = $importData;
    $this->_resource = $resource;
    $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
    $this->errorAggregator = $errorAggregator;
    }
    public function getValidColumnNames()
    {
        // print_r("Okk");
        // print_r($this->validColumnNames);exit;
        return $this->validColumnNames;
    }
    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'mglof_marketplace_product';
    }
    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
    $title = false;
    if (isset($this->_validatedRows[$rowNum])) {
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }
    $this->_validatedRows[$rowNum] = true;
    return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }
    /**
     * Create Advanced message data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteEntity();
        } else {
            $this->saveEntity();
        }

        //$this->saveEntity();
        return true;
    }
    /**
     * Save Message
     *
     * @return $this
     */
    public function saveEntity()
    {
    $this->saveAndReplaceEntity();
    return $this;
    }

    public function deleteEntity() {
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowId = $rowData[self::ID];
                    $listTitle[] = $rowId;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($listTitle) {
            $this->deleteEntityFinish(array_unique($listTitle), self::TABLE_Entity);
        }
        return $this;
    }

    /**
     * Save and replace data message
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceEntity()
    {
    $behavior = $this->getBehavior();
    //print_r($behavior);exit;
    $listTitle = [];
    while ($bunch = $this->_dataSourceModel->getNextBunch()) {
        $entityList = [];
        foreach ($bunch as $rowNum => $rowData) {
            if (!$this->validateRow($rowData, $rowNum)) {
                $this->addRowError(ValidatorInterface::ERROR_TITLE_IS_EMPTY, $rowNum);
                continue;
            }
            if ($this->getErrorAggregator()->hasToBeTerminated()) {
                $this->getErrorAggregator()->addRowToSkip($rowNum);
                continue;
            }

            $rowTtile= $rowData[self::ID];
            $listTitle[] = $rowTtile;
            $entityList[$rowTtile][] = [
                self::ID => $rowData[self::ID],
                self::PRODUCTID => $rowData[self::PRODUCTID],
                // self::ADMINASSING => $rowData[self::ADMINASSING],
                self::SELLERID => $rowData[self::SELLERID],
                self::PRODUCTNAME => $rowData[self::PRODUCTNAME],
                // self::STOREID => $rowData[self::STOREID],
                // self::POSITION => $rowData[self::POSITION],
                // self::STATUS => $rowData[self::STATUS],
                // self::CREATEDAT => $rowData[self::CREATEDAT],
                // self::UPDATEDAT => $rowData[self::UPDATEDAT],
                // self::CUSTOMERID => $rowData[self::CUSTOMERID],
                // self::COMMISSION => $rowData[self::COMMISSION],
                self::NAME => $rowData[self::NAME],
                self::DOORSETP => $rowData[self::DOORSETP],
                self::PICKFSTORE => $rowData[self::PICKFSTORE],
                self::MRP => $rowData[self::MRP],
            ];
        }
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
            
            //print_r($listTitle);exit;
            if ($listTitle) {
                if ($this->deleteEntityFinish(array_unique($listTitle), self::TABLE_Entity)) {
                    $this->saveEntityFinish($entityList, self::TABLE_Entity);
                }
            }
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
            $this->saveEntityFinish($entityList, self::TABLE_Entity);
        }
    }
    return $this;
    }
    /**
     * Save message to customtable.
     *
     * @param array $priceData
     * @param string $table
     * @return $this
     */
    protected function saveEntityFinish(array $entityData, $table)
    {
    if ($entityData) {
        $tableName = $this->_connection->getTableName($table);
        $entityIn = [];
        foreach ($entityData as $id => $entityRows) {
                foreach ($entityRows as $row) {
                    $entityIn[] = $row;
                }
        }
        if ($entityIn) {
            $this->_connection->insertOnDuplicate($tableName, $entityIn,[
                self::ID,
                self::PRODUCTID,
                // self::ADMINASSING,
                self::SELLERID,
                self::PRODUCTNAME,
                // self::STOREID,
                // self::POSITION,
                // self::STATUS,
                // self::CREATEDAT,
                // self::UPDATEDAT,
                // self::CUSTOMERID,
                // self::COMMISSION,
                self::NAME,
                self::DOORSETP,
                self::PICKFSTORE,
                self::MRP,
        ]);
        }
    }
    return $this;
    }


    protected function deleteEntityFinish(array $listTitle, $table) {
        //print_r($listTitle);exit;
        if ($table && $listTitle) {
            try {
                $this->countItemsDeleted += $this->_connection->delete(
                    $this->_connection->getTableName($table),
                    $this->_connection->quoteInto('entity_id IN (?)', $listTitle)
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