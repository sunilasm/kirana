<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\Model;

use Magento\TestFramework\Inspection\Exception;

class Sender
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var string|null
     */
    protected $messageSubject = null;

    /**
     * @var string|null
     */
    protected $messageBody = null;
     /**
     * @var string|null
     */
    protected $emailSubject = null;

    /**
     * @var string|null
     */
    protected $emailContent = null;

    public $_storeManager;

    protected $_priceCurrency;

    protected $_transportBuilder;


      /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    protected $messageManager;

    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Lof\MarketPlace\Helper\Data $helper

    ) {
        $this->messageManager = $messageManager;
        $this->inlineTranslation    = $inlineTranslation;
        $this->_transportBuilder = $_transportBuilder;
        $this->helper           = $helper;
    }


    public function newMessage($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/message_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ]) 
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['sender_email'])
            ->setReplyTo($data['sender_email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }

    public function newRating($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/rating_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['seller_email'])
            ->setReplyTo($data['seller_email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
    public function registerSeller($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/register_seller_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['email'])
            ->setReplyTo($data['email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
    public function newOrder($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/order_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['email'])
            ->setReplyTo($data['email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
    public function newInvoice($data) {
        try {
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/invoice_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['email'])
            ->setReplyTo($data['email'])
            ->getTransport();

            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
      public function newShipment($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/shipment_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['email'])
            ->setReplyTo($data['email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
    public function replyMessage($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/reply_message_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['receiver_email'])
            ->setReplyTo($data['receiver_email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
    public function approveSeller($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/approve_seller_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['email'])
            ->setReplyTo($data['email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
     public function approveProduct($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/approve_product_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['email'])
            ->setReplyTo($data['email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
     public function unapproveSeller($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/unapprove_seller_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['email'])
            ->setReplyTo($data['email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
    public function unapproveProduct($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/unapprove_product_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['email'])
            ->setReplyTo($data['email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
     public function sellerNewMessage($data) {
        try {
            
            $postObject = new \Magento\Framework\DataObject();
    
            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          
            $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->helper->getConfig('email_settings/seller_message_template'))

            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
            ->addTo($data['receiver_email'])
            ->setReplyTo($data['receiver_email'])
            ->getTransport();
            try  {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                
            } catch(\Exception $e){
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }

        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            return;
        }
    }
   
    /**
     * Get email body
     *
     * @return string
     */
    public function getEmailContent($queue)
    {
        if ($this->emailContent == null) {
            $this->getPreviewEmail($queue);
            return $this->transportBuilder->getMessageContent();
        }
        return $this->emailContent;
    }

    /**
     * Get email subject
     *
     * @return null|string
     */
    public function getEmailSubject($queue)
    {
         
        if ($this->emailSubject == null) {
            $this->getPreviewEmail($queue);
            return $this->transportBuilder->getMessageSubject();
        }
        return $this->emailSubject;
    }

    /**
     * Get email body
     *
     * @return string
     */
    public function getMessageContent($queue)
    {
        if ($this->messageBody == null) {
            $this->getPreview($queue);
            return $this->transportBuilder->getMessageContent();
        }
        return $this->messageBody;
    }

    /**
     * Get email subject
     *
     * @return null|string
     */
    public function getMessageSubject($queue)
    {
         
        if ($this->messageSubject == null) {
            $this->getPreview($queue);
            return $this->transportBuilder->getMessageSubject();
        }
        return $this->messageSubject;
    }
}