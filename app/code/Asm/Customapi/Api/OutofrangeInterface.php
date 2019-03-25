<?php
namespace Asm\Customapi\Api;
 
interface OutofrangeInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function outofrange();
}