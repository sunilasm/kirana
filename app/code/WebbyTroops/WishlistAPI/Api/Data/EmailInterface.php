<?php
namespace WebbyTroops\WishlistAPI\Api\Data;

/**
 * EmailInterface.
 * @api
 */
interface EmailInterface
{
    const LABEL_EMAIL = "email";

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail();
    
    /**
     * Set Email
     *
     * @return string
     */
    public function setEmail($email);
}
