<?php
namespace WebbyTroops\WishlistAPI\Model\Share;

class SendEmail
{
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }
    
    public function sendEmails()
    {
        $transport = $this->_transportBuilder->setTemplateIdentifier(
            $this->scopeConfig->getValue(
                'wishlist/email/email_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->setTemplateOptions(
            [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getStoreId(),
                        ]
        )->setTemplateVars(
            [
                            'customer' => $customer,
                            'customerName' => $customerName,
                            'salable' => $wishlist->isSalable() ? 'yes' : '',
                            'items' => $this->getWishlistItems($resultLayout),
                            'viewOnSiteLink' => $this->_url->getUrl('*/shared/index', ['code' => $sharingCode]),
                            'message' => $message,
                            'store' => $this->storeManager->getStore(),
                        ]
        )->setFrom(
            $this->scopeConfig->getValue(
                'wishlist/email/email_identity',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->addTo(
            $email
        )->getTransport();

        $transport->sendMessage();
    }
}
