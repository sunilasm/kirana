<?php
namespace Retailinsights\Promotion\Controller\Adminhtml\PostTable;
 
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Retailinsights\Promotion\Model\ResourceModel\PostTable\CollectionFactory;
 
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;
 
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
 
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Retailinsights\Promotion\Model\PostTableBackFactory $PostTableBackFactory,
        Context $context, 
        Filter $filter, 
        CollectionFactory $collectionFactory
    )
    {
        $this->_savedata=$PostTableBackFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $flag = 0; 

        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $data = $collection->getData();
      
        foreach ($collection as $item) {
            $item->delete();
            $flag = 1;
        }
       
        foreach($data as $d){
            
$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testdelPvn.log'); 
$logger = new \Zend\Log\Logger(); $logger->addWriter($writer);
$logger->info('Delete');
$logger->info($d);
           
            if($flag==1 && $d['rule_type'] ==1 && $d['status'] ==1){
                $mandeetotcol = $this->_savedata->create();
               
                $mandeetotcol->setStoreId($d['store_id']);
                $mandeetotcol->setRule($d['rule']);
                $mandeetotcol->setPstartDate($d['pstart_date']);
                $mandeetotcol->setPendDate($d['pend_date']);
                $mandeetotcol->setStatus($d['status']);
                $mandeetotcol->setDescription($d['description']);
                $mandeetotcol->setStoreName($d['store_name']);
                $mandeetotcol->setSellerType($d['seller_type']);
                $mandeetotcol->setConditionsSerialized($d['conditions_serialized']);
                $mandeetotcol->setActionsSerialized($d['actions_serialized']);
                $mandeetotcol->setSimpleAction($d['simple_action']);
                $mandeetotcol->setDiscountAmount($d['discount_amount']);
                $mandeetotcol->setRuleType($d['rule_type']);
                   
                if($mandeetotcol->save()){
                    $this->messageManager->addSuccess(__('You saved the rule.'));
                }else{
                    $this->messageManager->addSuccess(__('Rule Not saved.'));
                }
             
                }

        } 
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));
 
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}