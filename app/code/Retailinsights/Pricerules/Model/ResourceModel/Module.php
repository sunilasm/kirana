<?php
    namespace Retailinsights\Pricerules\Model\ResourceModel;

    class Module extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
    {
        /**
         * Initialize resource model
         *
         * @return void
         */
        protected function _construct()
        {       
            $this->_init('mytable', 'module_id');
        }
    }
    ?>