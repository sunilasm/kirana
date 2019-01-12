<?php
namespace Asm\Addressattribute\Observer;

class Model implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $customerAddress = $observer->getCustomerAddress();

        if($customerAddress->getEntityId()){

	        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	        $resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
	        $connection = $resource->getConnection();
	        $tableName = $resource->getTableName('customer_address_entity');

	        $sql = "UPDATE " . $tableName . " SET latitude = '" . $customerAddress->getLatitude() . "', longitude = '" . $customerAddress->getLongitude() . "'  WHERE entity_id = " . $customerAddress->getEntityId();
	        $connection->query($sql);
    	}

        $customer = $customerAddress->getCustomer(); //if you want to get customer details
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($customerAddress->getData());//here you will get address data
		$logger->info($customerAddress->getLatitude());//here you will get address data
		$logger->info($customerAddress->getLongitude());//here you will get address data
    }
}