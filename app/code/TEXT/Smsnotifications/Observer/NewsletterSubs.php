<?php

namespace TEXT\Smsnotifications\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer       as Observer;
use \Magento\Framework\View\Element\Context as Context;
use \TEXT\Smsnotifications\Helper\Data                 as Helper;
/**
 * Customer login observer
 */
class NewsletterSubs implements ObserverInterface
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
     * Message Manager
     * @var $messageManager
     */
    protected $messageManager;

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
     * Sender ID
     * @var $senderId
     */
    protected $senderId;

    /**
     * Destination
     * @var $destination
     */
    protected $destination;

    /**
     * Message
     * @var $message
     */
    protected $message;

    /**
     * Whether Enabled or not
     * @var $enabled
     */
    protected $enabled;

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
         
        
         $this->username = $this->_helper->getSmsnotificationsApiUsername();
         $this->password = $this->_helper->getSmsnotificationsApiPassword();

            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $om->create('Magento\Customer\Model\Session');

            $customer_id= $customerSession->getCustomerId();
            $name= $customerSession->getCustomer()->getName();
            $emailid=$customerSession->getCustomer()->getEmail();
            /*get customer mobile number */
            if($customer_id){
                $telephone= $customerSession->getCustomer()->getPrimaryBillingAddress()->getTelephone();
                 if ($telephone) {
                                  $text= $settings['newsletter_subs'];
                    
                                  $text = str_replace('{{customer_id}}', $customer_id, $text);
                                  $text = str_replace('{{name}}',  $name, $text);
                                  $text = str_replace('{{emailid}}',  $emailid, $text);  
                                  }                                                  
                    $admin_recipients[]=$settings['admin_recipients'];

                        array_push($admin_recipients, $telephone);

                           $object_manager = \Magento\Framework\App\ObjectManager ::getInstance();
                            $result = $object_manager->get('TEXT\Smsnotifications\Helper\Data')->sendSms($text,$admin_recipients);
                            
                         return($result );
                }
    }
}
    
