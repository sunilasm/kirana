<?php
namespace Retailinsights\Cartrules\Model\Config\Source;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
/**
* Custom Attribute Renderer
*/
class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
/**
* @var OptionFactory
*/
protected $optionFactory;
/**
* @param OptionFactory $optionFactory
*/
/**
* Get all options
*
* @return array
*/
public function getAllOptions()
{
/* your Attribute options list*/
$this->_options=[ 
['label'=>'kg', 'value'=>'0'],
['label'=>'gm', 'value'=>'1'],
['label'=>'ltr', 'value'=>'2'],
['label'=>'ml', 'value'=>'3'],
['label'=>'pads', 'value'=>'4'],
['label'=>"no's", 'value'=>"5"],
['label'=>'mtr', 'value'=>'6'],
['label'=>'pcs', 'value'=>'7'],
['label'=>'packs', 'value'=>'8'],
['label'=>'slices', 'value'=>'9'],
['label'=>'Ggb', 'value'=>'10'],
['label'=>'Astd', 'value'=>'11'],
['label'=>'mgm', 'value'=>'12'],
['label'=>'bunch', 'value'=>'13'],
['label'=>'watt', 'value'=>'14'],
['label'=>'strips', 'value'=>'15'],
['label'=>'sheets', 'value'=>'16'],
['label'=>'pages', 'value'=>'17'],
['label'=>'dozen', 'value'=>'18'],
['label'=>'bottle', 'value'=>'19'],
['label'=>'box', 'value'=>'20']
];
return $this->_options;
}
}
