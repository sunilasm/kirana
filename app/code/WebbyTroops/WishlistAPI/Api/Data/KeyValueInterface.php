<?php
namespace WebbyTroops\WishlistAPI\Api\Data;

/**
 * KeyValueInterface.
 * @api
 */
interface KeyValueInterface
{
    const LABEL_KEY = "key";
    const LABEL_VALUE = "value";

    /**
     * Get key
     *
     * @return string
     */
    public function getKey();
    
    /**
     * Set key
     *
     * @return string
     */
    public function setKey($key);

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();
    
    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);
}
