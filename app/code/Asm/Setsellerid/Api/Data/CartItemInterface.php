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
    const KEY_SELLER_KIRANA_ID = 'seller_kirana_id';
    const KEY_SELLER_ORG_STORE_ID = 'seller_org_store_id';
    const KEY_KIRANA_QTY = 'kirana_qty';
    const KEY_ORG_STORE_QTY = 'org_store_qty';
    /**#@-*/


    /**
     * Returns the item seller_id.
     *
     * @return int|null Item seller_id. Otherwise, null.
     */
    public function getSellerId();
    public function getSellerKiranaId();
    public function setSellerKiranaId($seller_kirana_id);

    public function getSellerOrgStoreId();
    public function setSellerOrgStoreId($seller_org_store_id);

    public function getKiranaQty();
    public function setKiranaQty($kirana_qty);

    public function getOrgStoreQty();
    public function setOrgStoreQty($org_store_qty);
    /**
     * Sets the item seller_id.
     *
     * @param int $seller_id
     * @return $this
     */
    public function setSellerId($seller_id);
    public function getImageUrl();
    public function setImageUrl($imageUrl);
    public function setVolume($volume);
}
