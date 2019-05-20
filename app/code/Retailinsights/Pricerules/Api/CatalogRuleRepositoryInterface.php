<?php
namespace Retailinsights\Pricerules\Api;
 
interface CatalogRuleRepositoryInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $ruleId Users name.
     * @return string Greeting message with users name.
     */
    public function getRule($ruleId); 
     /**
     * Returns greeting message to user
     *
     * @api
     * @param string Users name.
     * @return string Greeting message with users name.
     */
    public function getRuleList(); 

     /**
     * Returns greeting message to user
     *
     * @api
     * @param string $ruleId Users name.
     * @return string Greeting message with users name.
     */
    public function getBuyXgetFixed($ruleId); 

    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $ruleId Users name.
     * @return string Greeting message with users name.
     */
    public function getBuyXXXgetY($ruleId); 

   /**
     * Returns greeting message to user
     *
     * @api
     * @param string $ruleId Users name.
     * @return string Greeting message with users name.
     */
    public function getBuythree($ruleId); 
     /**
     * Returns greeting message to user
     *
     * @api
     * @param string $ruleId Users name.
     * @return string Greeting message with users name.
     */
    public function getBuyXYZ($ruleId); 
}
