<?php
namespace WebbyTroops\WishlistAPI\Api\Data;

/**
 * ShareWishlistInterface.
 * @api
 */
interface ShareWishlistInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LABEL_EMAILS        = 'emails';
    const LABEL_COMMENTS      = 'comments';
    /**#@-*/

    /**
     * Get Emails
     *
     * @return \WebbyTroops\WishlistAPI\Api\Data\EmailInterface[]
     */
    public function getEmails();

    /**
     * Get Comments
     *
     * @return string
     */
    public function getComments();

    /**
     * Set Emails
     *
     * @param \WebbyTroops\WishlistAPI\Api\Data\EmailInterface[] $emails
     * @return $this
     */
    public function setEmails($emails);

    /**
     * Set Comments
     *
     * @param string $comments
     * @return $this
     */
    public function setComments($comments);
}
