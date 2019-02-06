<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Asm\Setsellerid\Api\Data;

/**
 * Interface CartItemInterface
 * @api
 * @since 100.0.2
 */
interface CartItemInterface 
{
   
    const KEY_SELLER_ID = 'seller_id';
    const KEY_IMAGE_URL = 'image_url';

    /**#@-*/


    /**
     * Returns the item seller_id.
     *
     * @return int|null Item seller_id. Otherwise, null.
     */
    public function getSellerId();
   /* public function getSellerId()
    {
        return $this->getData('seller_id');
    }*/

    /**
     * Sets the item seller_id.
     *
     * @param int $seller_id
     * @return $this
     */
    public function setSellerId($seller_id);
   /* public function setSellerId($seller_id)
    {
        return $this->setData('seller_id', $seller_id);
    }*/

     public function getImageUrl();
    // {
    //     return $this->_get(self::KEY_IMAGE_URL);
    // }

     public function setImageUrl($imageUrl);
    // {
    //     $this->setData('image_url', $imageUrl);
    //     return $this;
    // }
  
}
