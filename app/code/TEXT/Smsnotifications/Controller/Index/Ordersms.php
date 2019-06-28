<?php
/**
 * @category   Asm
 * @package    Asm_Search
 * @author     sunilnalawade15@gmail.com
 * @copyright  This file was generated by using Module Creator(http://code.vky.co.in/magento-2-module-creator/) provided by VKY <viky.031290@g$
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace TEXT\Smsnotifications\Controller\Index;
use Magento\Framework\App\Action\Context;
use \TEXT\Smsnotifications\Helper\Data as Helper;

class Ordersms extends \Magento\Framework\App\Action\Action
{
        
        protected $_helper;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
            \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
            \Magento\Framework\Api\SortOrderBuilder $sortBuilder,
           Helper $helper,
            Context $context
    ) {
          $this->orderRepository = $orderRepository;
              $this->searchCriteriaBuilder = $searchCriteriaBuilder;
              $this->sortBuilder = $sortBuilder;
              $this->_helper  = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
                //date_default_timezone_set('Asia/Kolkata'); 
                $time = time();
                $to = date('Y-m-d H:i:s', $time);
                $lastTime = $time - 300; // 60*60*24
                $from = date('Y-m-d H:i:s', $lastTime);
                //print_r("to:-".$to);
                //print_r("from:-".$from); exit;
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $OrderFactory = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\CollectionFactory');
                $orderCollection = $OrderFactory->create()->addFieldToSelect(array('*'));
                $orderCollection->addFieldToFilter('created_at', ['lteq' => $to])->addFieldToFilter('created_at', ['gteq' => $from]);
               //print_r($orderCollection->getSelect()->__toString());exit;  

                $table = "";
		        $table .= "<table style='border:1px solid #000'>";
                $table .= "<tr style='border:1px solid #000'>";
                $table .= "<td style='border:1px solid #000;'>";
                $table .= "Order Id";
                $table .= "</td>";
                $table .= "<td style='border:1px solid #000'>";
                $table .= "Customer";
                $table .= "</td>";
                $table .= "<td style='border:1px solid #000'>";
                $table .= "Status";
                $table .= "</td>";
                $table .= "</tr>";

 			foreach($orderCollection as $order):
			     $result = '';
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load($order->getCustomerId());
                         $settings = $this->_helper->getSettings();
           /*For multiselect array */
                $arr= $settings['order_statuss'];
                $a = explode(',', $settings['order_statuss']);
                $b = explode(',', $settings['order_statuss']);
                $final_array = array_combine($a, $b);

                $orderId       =  $order->getIncrementId();
                $firstname     =  $order->getBillingAddress()->getFirstName();
                $middlename    =  $order->getBillingAddress()->getMiddlename();
                $lastname      =  $order->getBillingAddress()->getLastname();
                $totalPrice    =  number_format($order->getGrandTotal(), 2);
                $countryCode   =  $order->getOrderCurrencyCode();
                $customerEmail =  $order->getCustomerEmail();
		$customerFname = $customer->getFirstname();
                $customerLname = $customer->getLastname();

               $telephone = $customer->getPrimaryBillingAddress()->getTelephone();
          	//print_r($customer->getData());exit;        
	        if(in_array('placeorder', $final_array))
                {
		    $admin_recipients = array();
                    if ($telephone)     
                    {
                        $text= $settings['new_order'];
                        $text = str_replace('{order_id}', $orderId, $text);
                        $text = str_replace('{firstname}', $customerFname, $text);
                        $text = str_replace('{lastname}', $customerLname, $text);
                        $text = str_replace('{price}',  $totalPrice, $text);
                        $text = str_replace('{emailid}',  $customerEmail, $text);
                        $text = str_replace('{country_code}',  $countryCode, $text);
                    } 
                    $admin_recipients[]=$settings['admin_recipients'];
		   // print_r($admin_recipients);
                    array_push($admin_recipients, $telephone);
	   
                    $result = $objectManager->get('TEXT\Smsnotifications\Helper\Data')->sendSms($text,$admin_recipients);
print_r($result);                   
 exit;
                }
                $table .= "<tr style='border:1px solid #000'>";
                $table .= "<td style='border-right:1px solid #000'>";
                $table .= $orderd;
                $table .= "</td>";
                $table .= "<td style='border-right:1px solid #000'>";
                $table .= $customerFname." ".$customerLname;
                $table .=  "</td>";
                if($result != ''){
                	$table .=  "<td>";
	                $table .=  "Sent";
	                $table .=  "</td>";
                }else{
                	$table .=  "<td>";
	                $table .=  "Fail";
	                $table .=  "</td>";
                }
                $table .= "</tr>";
               
                //print_r($orderId.'--send--'.$result);
                //print_r($result);
//exit;
             endforeach;
             $table .= "</table>";
	         echo $table;
    }
}

