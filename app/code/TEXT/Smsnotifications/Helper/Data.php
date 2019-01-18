<?php
namespace TEXT\Smsnotifications\Helper;

use \Magento\Framework\App\ObjectManager as ObjectManager;
use \Magento\Framework\Event\Observer as Observer;
use \Magento\Store\Model\ScopeInterface as ScopeInterface;
use \Magento\Framework\App\Helper\AbstractHelper as AbstractHelper;

class Data extends AbstractHelper
{
  
    /**
     * This will used by Smsnotifications sms admins to confirm which e-commerce platform is sending sms
     * @var string
     */
    public $platform         = 'Magento';
    /**
     * The version of e-commerce platform
     * @var string
     */
    public $platformVersion  = '2.0';
    /**
     * Return type of api method
     * @var string
     */
    public $format           = 'json';
    /**
     * To be used by the API
     * @var string
     */
    public $host             = 'https://www.Smsnotificationssms.com/';

    public $app_name = 'Textlocal_SMSNotifications';
    public function getSettings()
    {
        $settings = array();
        $settings['sms_gateway_url'] = "https://api.textlocal.in/";//
        $settings['sms_auth_token'] = $this->getConfig('text_Smsnotifications_configuration/basic_configuration/smsnotifications_username');
        $settings['sms_sender_name'] = $this->getConfig('text_Smsnotifications_configuration/basic_configuration/smsnotifications_password');
        $settings['admin_recipients'] = $this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_admin_mobile');
        $settings['admin_onhold']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_hold_admin_message');
        $settings['customer_neworder']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_new_order_admin_message');
        $settings['admin_unhold']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_unhold_admin_message');
        $settings['order_cancell']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_cancelled_admin_message');
        $settings['order_invoice']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_invoiced_admin_message');
        $settings['order_shiped']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_shiped_admin_message');
        $settings['order_payment'] =$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_payment_review_message');
        $settings['paypal_cancel'] =$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_paypal_canceled');
        $settings['paypal_reversed'] =$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_paypal_reversed');
        $settings['pending_payment'] =$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_pending_payment');
        $settings['pending_paypal'] =$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_pending_paypal');
        $settings['order_suspected_fraud'] =$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_suspected_fraud');
        $settings['edit_account']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_editaccount_admin_message');
        $settings['forgot_password']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_forgotpwd_admin_message');
        $settings['review_submit']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_review_admin_message');
        $settings['review_approve']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_review_approve_message');
        $settings['change_password']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_changepwd_admin_message');
        $settings['new_order']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_new_order_admin_message');
        $settings['customer_register']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_register_admin_message');
        $settings['newsletter_subs']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_newslettersubs_admin_message');
        $settings['order_statuss']=$this->getConfig('text_smsnotifications_admins/admin_configuration/order_status');
        $settings['new_order_status']=$this->getConfig('text_smsnotifications_admins/admin_configuration/neworder_status');
        $settings['coupon_code']=$this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_couponcode_admin_message');
         return $settings;
    }
    /**
     * Getting Basic Configuration
     * These functions are used to get the api username and password
     */

    /**
     * Getting SmsnotificationsSMS API Username
     * @return string
     */

    public function getSmsnotificationsApiUsername()
    {
        return $this->getConfig('text_Smsnotifications_configuration/basic_configuration/smsnotifications_username');
    }

    /**
     * Getting SmsnotificationsSMS API Password
     * @return string
     */
    public function getSmsnotificationsApiPassword()
    {
        return $this->getConfig('text_smsnotifications_configuration/basic_configuration/smsnotifications_password');
    }
  /**
   * Getting Admin Mobile Number
   * @return string
   */
    public function getAdminSenderId()
    {
        return $this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_admin_mobile');
    }

    /**
     * Checking Admin SMS is enabled or not
     * @return string
     */
    public function isAdminNotificationsEnabled()
    {
        return $this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_admin_enabled');
    }
     /**
      * Send Configuration path to this function and get the module admin Config data
      * @param @var $configPath
      * @return string
      */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
        ScopeInterface::SCOPE_STORE);
    }
     /**
      * Getting Customer Message
      * @return string
      */
    public function getCustomerMessageOnOrder()
    {
        return $this->getConfig('text_smsnotifications_orders/new_order/smsnotifications_new_order_message');
    }
    /**
     * Sending SMS
     * @param @var $username
     * @param @var $password
     * @param @var $senderID
     * @param @var $destination
     * @param @var $message
     * @return void
     */
    public function sendSms($body, $admin_recipients = array())
    {

        $settings = $this->getSettings();
        $errors = array();
        $apiuri = $settings['sms_gateway_url'];
        $apiurl = $apiuri."send?&apiKey=".urlencode($settings['sms_auth_token'])."&sender=".urlencode($settings['sms_sender_name'])."&numbers=".urlencode(implode(',', $admin_recipients))."&message=".urlencode($body);
        $result = file_get_contents($apiurl);
       $rows = json_decode($result, true);
        if ($rows['status'] != 'success') {
            return false;
        } 
        return true;
    }
      /**
       * Getting admin message for new order
       * @return string
       */
    public function getAdminMessageForNewOrder()
    {
        return $this->getConfig('text_smsnotifications_admins/admin_configuration/smsnotifications_new_order_admin_message');
    }
    public function getOrder(Observer $observer)
    {
        $order              = $observer->getOrder();
        $orderId            = $order->getIncrementId();
        $objectManager      = ObjectManager::getInstance();
        $order              = $objectManager->get('Magento\Sales\Model\Order');
        $orderInformation   = $order->loadByIncrementId($orderId);
        return $orderInformation;
    }
}
