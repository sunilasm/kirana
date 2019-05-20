<?php

namespace Retailinsights\pricerules\Ui\Component\Listing\Column;

class NoteGetFixed extends \Magento\Ui\Component\Listing\Columns\Column {

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ){
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
          
            foreach ($dataSource['data']['items'] as & $item) {
                
              
                   $item['NoteGetFixed'] = "<a href='".$this->context->getUrl('retailinsights_pricerules/postFixed/rules').'?post_id='.$item['post_id']."'>edit</a>";

                 
            }
        }
       
        //Retailinsights/Pricerules/view/adminhtml/templares/buyxyz.phtml
        return $dataSource;
    }
}