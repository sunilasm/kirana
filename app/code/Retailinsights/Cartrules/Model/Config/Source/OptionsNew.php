<?php

namespace Retailinsights\Cartrules\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;

use Magento\Framework\DB\Ddl\Table;

/**

* Custom Attribute Renderer

*/

class OptionsNew extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource

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
['label'=>'Kg', 'value'=>'0'],

['label'=>'gm', 'value'=>'1'],

['label'=>'Lt', 'value'=>'2'],

['label'=>'ml', 'value'=>'3'],

['label'=>'Pads', 'value'=>'4'],
['label'=>"No's", 'value'=>"5"],
['label'=>'Gram', 'value'=>'6'],
['label'=>'Meters', 'value'=>'7'],
['label'=>'Piece', 'value'=>'8'],
['label'=>'Pack', 'value'=>'9'],
['label'=>'Slices', 'value'=>'10'],
['label'=>'Ggb', 'value'=>'11'],
['label'=>'Astd', 'value'=>'12'],
['label'=>'Pcs', 'value'=>'13'],
['label'=>'mgm+1', 'value'=>'14'],
['label'=>'Bunch', 'value'=>'15'],
['label'=>'Watt', 'value'=>'16'],
['label'=>'Strips', 'value'=>'17'],
['label'=>'sheets', 'value'=>'18'],
['label'=>'Gms', 'value'=>'19'],
['label'=>'pages', 'value'=>'20'],
['label'=>'Pices', 'value'=>'21'],
['label'=>'gmq', 'value'=>'22'],
['label'=>'Dozen', 'value'=>'23'],
['label'=>'Bottle', 'value'=>'24'],




['label'=>'Box', 'value'=>'25']

];

return $this->_options;

}

}

