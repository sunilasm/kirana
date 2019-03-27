<?php
namespace Asm\Customapi\Api;
 
interface CountryInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function country();
}