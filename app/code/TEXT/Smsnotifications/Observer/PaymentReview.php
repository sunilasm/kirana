<?php

namespace TEXT\Smsnotifications\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer       as Observer;
use \Magento\Framework\View\Element\Context as Context;
use \TEXT\Smsnotifications\Helper\Data                 as Helper;
/**
 * Customer login observer
 */
class PaymentReview implements ObserverInterface
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
       echo"123";
       exit();

           $admin_recipients = array();
        
        if (strpos($_SERVER['REQUEST_URI'], 'order/payment_review') !== false) {
            

               $this->username         = $this->_helper->getSmsnotificationsApiUsername();
               $this->password         = $this->_helper->getSmsnotificationsApiPassword();
                    $order              = $this->_helper->getOrder($observer);
                    
                    $orderId       =  $order->getIncrementId();
                    $firstname     =  $order->getBillingAddress()->getFirstName();
                    $middlename    =  $order->getBillingAddress()->getMiddlename();
                    $lastname      =  $order->getBillingAddress()->getLastname();
                    $totalPrice    =  number_format($order->getGrandTotal(), 2);
                    $countryCode   =  $order->getOrderCurrencyCode();
                    $customerEmail =  $order->getCustomerEmail();
                    
             

                     // $order = $observer->getEvent()->getOrder(); 

          $telephone=  $this->destination  = $order->getBillingAddress()->getTelephone();
     
                     if ($telephone) {
                     

              $text=$settings['admin_onhold'];
          
              $text = str_replace('{order_id}', $orderId, $text);
              $text = str_replace('{firstname}', $firstname, $text);
              $text = str_replace('{lastname}', $lastname, $text);
              $text = str_replace('{price}',  $totalPrice, $text);
              $text = str_replace('{emailid}',  $customerEmail, $text);
              $text = str_replace('{country_code}',  $countryCode, $text);

}
                                      
    $admin_recipients[]=$settings['admin_recipients'];
 
    array_push($admin_recipients, $telephone);


       $object_manager = \Magento\Framework\App\ObjectManager ::getInstance();
    
        $result = $object_manager->get('TEXT\Smsnotifications\Helper\Data')->sendSms($text,$admin_recipients);
        return($result );
     

    
      }
    }
}


