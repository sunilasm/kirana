<?php
namespace WebbyTroops\WishlistAPI\Model\Data;

use WebbyTroops\WishlistAPI\Api\Data\WishlistItemsInterface;
use WebbyTroops\WishlistAPI\Api\Data\WishlistItemExtensionInterface;

class WishlistItems extends \Magento\Framework\Api\AbstractExtensibleObject implements WishlistItemsInterface
{
    /**
     * @inheritDoc
     */
    public function getItemId()
    {
        return $this->_get(self::LABEL_ITEM_ID);
    }
    
    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->_get(self::LABEL_STORE_ID);
    }
    
    /**
     * @inheritDoc
     */
    public function getAddedAt()
    {
        return $this->_get(self::LABEL_ADDED_AT);
    }
    
    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->_get(self::LABEL_DESCRIPITION);
    }
    
    /**
     * @inheritDoc
     */
    public function getQty()
    {
        return $this->_get(self::LABEL_QTY);
    }
    
    /**
     * @inheritDoc
     */
    public function getProduct()
    {
        return $this->_get(self::LABEL_PRODUCT);
    }
    
    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }
    
    /**
     * @inheritDoc
     */
    public function setItemId($wishlistItemId)
    {
        return $this->setData(self::LABEL_ITEM_ID, $wishlistItemId);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::LABEL_STORE_ID, $storeId);
    }
    
    /**
     * @inheritDoc
     */
    public function setAddedAt($addedAt)
    {
        return $this->setData(self::LABEL_ADDED_AT, $addedAt);
    }
    
    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::LABEL_DESCRIPITION, $description);
    }
    
    /**
     * @inheritDoc
     */
    public function setQty($qty)
    {
        return $this->setData(self::LABEL_QTY, $qty);
    }
    
    /**
     * @inheritDoc
     */
    public function setProduct($product)
    {
        return $this->setData(self::LABEL_PRODUCT, $product);
    }
    
    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(WishlistItemExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
