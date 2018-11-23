<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Model\Source;

use Magento\Framework\DB\Ddl\Table;

class Approval extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    const STATUS_NOT_SUBMITED	= 0;
    const STATUS_PENDING		= 1;
    const STATUS_APPROVED		= 2;
    const STATUS_UNAPPROVED		= 3;
    
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = null;
    
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Not Submited'), 'value' => self::STATUS_NOT_SUBMITED],
                ['label' => __('Pending'), 'value'      => self::STATUS_PENDING],
                ['label' => __('Approved'), 'value'     => self::STATUS_APPROVED],
                ['label' => __('Unapproved'), 'value' => self::STATUS_UNAPPROVED],
            ];
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
    
    
    /**
     * Get options as array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
    

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Product approval attribute',
            ],
        ];
    }
}
