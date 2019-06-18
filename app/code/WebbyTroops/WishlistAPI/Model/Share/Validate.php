<?php
namespace WebbyTroops\WishlistAPI\Model\Share;

use Magento\Framework\Exception\ValidatorException;

class Validate
{
    public function __construct(
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->wishlistConfig = $wishlistConfig;
        $this->storeManager = $storeManager;
    }
    
    public function validateData($emails, $wishlist, $message)
    {
        $sharingLimit = $this->wishlistConfig->getSharingEmailLimit();
        $textLimit = $this->wishlistConfig->getSharingTextLimit();
        $emailsLeft = $sharingLimit - $wishlist->getShared();
        $emailsArray = [];
        foreach ($emails as $email) {
            $emailsArray[] = $email->getEmail();
        }
        if (strlen($message) > $textLimit) {
            throw new ValidatorException(__('Message length must not exceed '.$textLimit.' symbols'));
        } else {
            $message = nl2br(htmlspecialchars($message));
            if (empty($emailsArray)) {
                throw new ValidatorException(__('Please enter an email address.'));
            } else {
                if (count($emailsArray) > $emailsLeft) {
                    throw new ValidatorException(__('This wish list can be shared '.$emailsLeft.' more times.'));
                } else {
                    foreach ($emailsArray as $index => $email) {
                        $email = trim($email);
                        if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
                            throw new ValidatorException(__('Please enter an email address.'));
                        }
                        $emailsArray[$index] = $email;
                    }
                }
            }
        }
        return ['emails' => $emailsArray, 'message' => $message];
    }
}
