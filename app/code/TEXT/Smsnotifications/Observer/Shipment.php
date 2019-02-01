<?php

namespace TEXT\Smsnotifications\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer       as Observer;
use \Magento\Framework\View\Element\Context as Context;
use \TEXT\Smsnotifications\Helper\Data                 as Helper;
/**
 * Customer login observer
 */
class Shipment implements ObserverInterface
{
    /**
     * Message manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    const AJAX_PARAM_NAME = 'infscroll';
    /**
     *
     */
    const AJAX_HANDLE_NAME = 'infscroll_ajax_request';

    /**
     * Https request
     *
     * @var \Zend\Http\Request
     */
    protected $_request;

    /**
     * Layout Interface
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Cache
     * @var $_cache
     */
    protected $_cache;

    /**
     * Helper for SmsnotificationsSMS Module
     * @var \TEXT\Smsnotifications\Helper\Data
     */
    protected $_helper;

    
    /**
     * Username
     * @var $username
     */
    protected $username;

    /**
     * Password
     * @var $password
     */
    protected $password;

    /**
     * Constructor
     * @param Context $context
     * @param Helper $helper _helper
     */
    public function __construct(
        Context $context,
        Helper $helper
    ) {
        $this->_helper  = $helper;
        $this->_request = $context->getRequest();
        $this->_layout  = $context->getLayout();
    }

    /**
     * The execute class
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $settings = $this->_helper->getSettings();
          /*For multiselect array */
        $arr= $settings['order_statuss'];
        $a = explode(',', $settings['order_statuss']);
        $b = explode(',', $settings['order_statuss']);
        $final_array = array_combine($a, $b);
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
		// $logger = new \Zend\Log\Logger();
		// $logger->addWriter($writer);
		// $logger->info("---- Shipment ----");
        // $logger->info("Order status:");
        // $logger->info(print_r($final_array,true));
        // $logger->info("Order settings:");
        // $logger->info(print_r($settings,true));
        $admin_recipients = array();
        /*For api key and sender id */
        $this->username         = $this->_helper->getSmsnotificationsApiUsername();
        $this->password         = $this->_helper->getSmsnotificationsApiPassword();
                 
        $shipment   = $observer->getShipment();
        // $logger->info("Order Shippment:");
        // $logger->info(print_r($settings,true));
        $order     = $shipment->getOrder($shipment);
        $orderId       =  $order->getIncrementId();
        $firstname     =  $order->getBillingAddress()->getFirstName();
        $middlename    =  $order->getBillingAddress()->getMiddlename();
        $lastname      =  $order->getBillingAddress()->getLastname();
        $totalPrice    =  number_format($order->getGrandTotal(), 2);
        $countryCode   =  $order->getOrderCurrencyCode();
        $customerEmail =  $order->getCustomerEmail();
              
        $telephone=  $this->destination  = $order->getBillingAddress()->getTelephone();
        // $logger->info("Telephone:".$telephone);
        if(in_array('shippment', $final_array)){
            $text = '';
            if ($telephone){
                $text= $settings['order_shiped'];
                $text = str_replace('{order_id}', $orderId, $text);
                $text = str_replace('{firstname}', $firstname, $text);
                $text = str_replace('{lastname}', $lastname, $text);
                $text = str_replace('{price}',  $totalPrice, $text);
                $text = str_replace('{emailid}',  $customerEmail, $text);
                $text = str_replace('{country_code}',  $countryCode, $text);
            }
            $admin_recipients[]=$settings['admin_recipients'];
            array_push($admin_recipients, $telephone);
            // $logger->info("Receipients:");
            // $logger->info(print_r($admin_recipients,true));
            $object_manager = \Magento\Framework\App\ObjectManager ::getInstance();
            $result = $object_manager->get('TEXT\Smsnotifications\Helper\Data')->sendSms($text,$admin_recipients);
            // $logger->info("Result:".$result);
            return($result );
        }
    }
}