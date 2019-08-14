<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_product', 'madai_id', array(

    'group'         				=> 'General',
    'input'         				=> 'text',
    'type'          				=> 'text',
    'label'         				=> 'Madai Product ID',
    'backend'       				=> '',
	'frontend'						=> '',
    'visible'       				=> true,
    'required'      				=> false,
    'user_defined' 					=> true,
    'searchable' 					=> false,
    'filterable' 					=> false,
    'comparable'    				=> false,
    'visible_on_front' 				=> true,
    'visible_in_advanced_search'  	=> false,
    'is_html_allowed_on_front' 		=> false,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'note'							=> 'Madai Product ID'
));


$conn = $installer->getConnection();
$theTable = $this->getTable('sales_flat_quote_item');

if($conn->tableColumnExists($theTable, 'madai_discount'))
	Mage::log('Column madai_discount already exists in quote item');
else
	$conn->addColumn($theTable, 'madai_discount', 'DECIMAL(12,4)');


if($conn->tableColumnExists($theTable, 'madai_order_id'))
	Mage::log('Column madai_product_id already exists in quote item');
else
	$conn->addColumn($theTable, 'madai_order_id', 'VARHCAR(100)');


$theTable = $this->getTable('sales_flat_order_item');

if($conn->tableColumnExists($theTable, 'madai_discount'))
	Mage::log('Column madai_discount already exists  in order_item');
else
	$conn->addColumn($theTable, 'madai_discount', 'DECIMAL(12,4)');


if($conn->tableColumnExists($theTable, 'madai_order_id'))
	Mage::log('Column madai_product_id already exists in order_item');
else
	$conn->addColumn($theTable, 'madai_order_id', 'VARHCAR(100)');


$installer->endSetup();