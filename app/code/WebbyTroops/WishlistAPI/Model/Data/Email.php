<?php

namespace WebbyTroops\WishlistAPI\Model\Data;

/**
 * Class Email
 */
class Email extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \WebbyTroops\WishlistAPI\Api\Data\EmailInterface
{
    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->getData(self::LABEL_EMAIL);
    }
    
    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::LABEL_EMAIL, $email);
    }
}
