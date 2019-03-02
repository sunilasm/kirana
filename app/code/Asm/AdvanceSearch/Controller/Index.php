<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Asm\AdvanceSearch\Controller\;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{Factory
     */
    public function __construct(
        Context $context,
       
    ) {
        parent::__construct($context);
     
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        echo "fsdfsdf";
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$attributeId = 168; // Id of attribute
$attributeModel = $objectManager->create('Magento\Product\Model\Attribute')->load($attributeId);
$data = [];
$options = $attributeModel->getSource()->getAllOptions(); //get all options
foreach ($options as $value) {
    if (is_numeric($value['value'])) {
         $data['option']['delete'][$value['value']] = 1;
    }
 }
$attributeModel->addData($data);
$attributeModel->save();

    }
}
