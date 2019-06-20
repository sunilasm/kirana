<?php
namespace Asm\Customapi\Api;
 
interface EditaddressInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    // edit customer address
    public function editcustomeraddress();
}