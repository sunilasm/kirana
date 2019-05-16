<?php
namespace Asm\Timeslot\Api;
 
interface TimeslotInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function timeslot();
}