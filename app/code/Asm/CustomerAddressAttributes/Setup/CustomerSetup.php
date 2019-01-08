<?php

namespace Asm\CustomerAddressAttributes\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;

class CustomerSetup extends EavSetup {

	protected $eavConfig;

	public function __construct(
		ModuleDataSetupInterface $setup,
		Context $context,
		CacheInterface $cache,
		CollectionFactory $attrGroupCollectionFactory,
		Config $eavConfig
		) {
		$this -> eavConfig = $eavConfig;
		parent :: __construct($setup, $context, $cache, $attrGroupCollectionFactory);
	} 

	public function installAttributes($customerSetup) {
		$this -> installCustomerAttributes($customerSetup);
		$this -> installCustomerAddressAttributes($customerSetup);
	} 

	public function installCustomerAttributes($customerSetup) {
			
	} 

	public function installCustomerAddressAttributes($customerSetup) {
			

		$customerSetup -> addAttribute('customer_address',
			'geo_lat',
			[
			'label' => 'Geo Latitude',
			'system' => 0,
			'user_defined' => true,
			'required' => false,
			'position' => 1000,
            'sort_order' =>1000,
            'visible' =>  true,
			'default_value' => '',
			'note' => '',
				

                        'type' => 'varchar',
                        'input' => 'text',
			
			]
			);

		$customerSetup -> getEavConfig() -> getAttribute('customer_address', 'geo_lat')->setData('is_user_defined',1)->setData('default_value','')-> setData('used_in_forms', ['adminhtml_customer_address', 'customer_register_address', 'customer_address_edit']) -> save();

				

		$customerSetup -> addAttribute('customer_address',
			'geo_lng',
			[
			'label' => 'Geo Longitude',
			'system' => 0,
			'user_defined' => true,
			'required' => false,
			'position' => 1001,
            'sort_order' =>1001,
            'visible' =>  true,
			'default_value' => '',
			'note' => '',
				

                        'type' => 'varchar',
                        'input' => 'text',
			
			]
			);

		$customerSetup -> getEavConfig() -> getAttribute('customer_address', 'geo_lng')->setData('is_user_defined',1)->setData('default_value','')-> setData('used_in_forms', ['adminhtml_customer_address', 'customer_register_address', 'customer_address_edit']) -> save();

				
	} 

	public function getEavConfig() {
		return $this -> eavConfig;
	} 
} 