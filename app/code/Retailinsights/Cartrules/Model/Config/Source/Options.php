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
['label'=>'Kg', 'value'=>'Kg'],

['label'=>'gm', 'value'=>'gm'],

['label'=>'Lt', 'value'=>'Lt'],

['label'=>'ml', 'value'=>'ml'],

['label'=>'Pads', 'value'=>'Pads'],
['label'=>"No's", 'value'=>"No's"],
['label'=>'Gram', 'value'=>'Gram'],
['label'=>'Meters', 'value'=>'Meters'],
['label'=>'Piece', 'value'=>'Piece'],
['label'=>'Pack', 'value'=>'Pack'],
['label'=>'Slices', 'value'=>'Slices'],
['label'=>'Ggb', 'value'=>'Ggb'],
['label'=>'Astd', 'value'=>'Astd'],
['label'=>'Pcs', 'value'=>'Pcs'],
['label'=>'mgm+1', 'value'=>'gm+1'],
['label'=>'Bunch', 'value'=>'Bunch'],
['label'=>'Watt', 'value'=>'Watt'],
['label'=>'Strips', 'value'=>'Strips'],
['label'=>'sheets', 'value'=>'sheets'],
['label'=>'Gms', 'value'=>'Gms'],
['label'=>'pages', 'value'=>'pages'],
['label'=>'Pices', 'value'=>'Pices'],
['label'=>'gmq', 'value'=>'gmq'],
['label'=>'Dozen', 'value'=>'Dozen'],
['label'=>'Bottle', 'value'=>'Bottle'],




['label'=>'Box', 'value'=>'Box']

];

return $this->_options;

}

}

