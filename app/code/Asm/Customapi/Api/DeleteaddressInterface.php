<?php
namespace Asm\Customapi\Api;
 
interface DeleteaddressInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    // edit customer address
    public function deletecustomeraddress($addressId);
}