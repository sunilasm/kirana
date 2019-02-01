<?php
namespace Asm\Search\Api;
 
interface SearchInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function name();
    //public function clear();
    //public function checkcart();
    public function deletesku();
}
