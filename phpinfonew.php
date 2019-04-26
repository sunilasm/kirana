<?php

ini_set('error_reporting', E_ALL);
ini_set("display_errors", "1");

use Magento\Framework\App\Bootstrap;
require 'app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$registry = $objectManager->get('Magento\Framework\Registry');
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$ids = array(487,453,452,444,442,381,327,318,315,312,311); // your order_id 

foreach ($ids as $id) {

    $order = $objectManager->create('Magento\Sales\Model\Order')->load($id);
    $registry->register('isSecureArea','true');
    $order->delete();
    $registry->unregister('isSecureArea');
    echo "order deleted";

}
