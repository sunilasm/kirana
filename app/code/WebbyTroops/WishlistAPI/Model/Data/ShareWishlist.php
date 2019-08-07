<?php

namespace WebbyTroops\WishlistAPI\Model\Data;

/**
 * Class ShareWishlist
 */
class ShareWishlist extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \WebbyTroops\WishlistAPI\Api\Data\ShareWishlistInterface
{
    /**
     * @inheritdoc
     */
    public function getEmails()
    {
        return $this->getData(self::LABEL_EMAILS);
    }
    
    /**
     * @inheritdoc
     */
    public function getComments()
    {
        return $this->getData(self::LABEL_COMMENTS);
    }
    
    /**
     * @inheritdoc
     */
    public function setEmails($emails)
    {
        return $this->setData(self::LABEL_EMAILS, $emails);
    }
    
    /**
     * @inheritdoc
     */
    public function setComments($comments)
    {
        return $this->setData(self::LABEL_COMMENTS, $comments);
    }
}
