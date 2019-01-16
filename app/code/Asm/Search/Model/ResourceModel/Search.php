<?php
/**
 * @category   Asm
 * @package    Asm_Search
 * @author     sunilnalawade15@gmail.com
 * @copyright  This file was generated by using Module Creator(http://code.vky.co.in/magento-2-module-creator/) provided by VKY <viky.031290@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Asm\Search\Model\ResourceModel;

class Search extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('asm_search', 'search_id');   //here "asm_search" is table name and "search_id" is the primary key of custom table
    }
}